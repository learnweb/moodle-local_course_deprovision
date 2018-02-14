<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Manager for Subplugins
 *
 * @package tool_cleanupcourses
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_cleanupcourses\manager;

use tool_cleanupcourses\entity\step_subplugin;
use tool_cleanupcourses\entity\trigger_subplugin;

defined('MOODLE_INTERNAL') || die();

class step_manager extends subplugin_manager {

    /**
     * Returns a step instance if one with the is is available.
     * @param int $stepinstanceid id of the step instance
     * @return step_subplugin|null
     */
    public static function get_step_instance($stepinstanceid) {
        global $DB;
        $record = $DB->get_record('tool_cleanupcourses_step', array('id' => $stepinstanceid));
        if ($record) {
            $subplugin = step_subplugin::from_record($record);
            return $subplugin;
        } else {
            return null;
        }
    }

    /**
     * Returns a step instance for a workflow with a specific sortindex.
     * @param int $workflowid id of the workflow
     * @param int $sortindex sortindex of the step within the workflow
     * @return step_subplugin|null
     */
    public static function get_step_instance_by_workflow_index($workflowid, $sortindex) {
        global $DB;
        $record = $DB->get_record('tool_cleanupcourses_step',
            array(
                'workflowid' => $workflowid,
                'sortindex' => $sortindex)
        );
        if ($record) {
            $subplugin = step_subplugin::from_record($record);
            return $subplugin;
        } else {
            return null;
        }
    }

    /**
     * Persists a subplugin to the database.
     * @param step_subplugin $subplugin
     */
    public static function insert_or_update(step_subplugin &$subplugin) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        if ($subplugin->id) {
            $DB->update_record('tool_cleanupcourses_step', $subplugin);
        } else {
            $subplugin->sortindex = self::count_steps_of_workflow($subplugin->workflowid) + 1;
            $subplugin->id = $DB->insert_record('tool_cleanupcourses_step', $subplugin);
        }
        $transaction->allow_commit();
    }

    /**
     * Removes all step instances from the database.
     * Should only be used, when uninstalling the subplugin.
     * @param string $subpluginname step instance id
     */
    public static function remove_all_instances($subpluginname) {
        $steps = self::get_step_instances_by_subpluginname($subpluginname);
        foreach ($steps as $step) {
            self::remove($step->id);
        }
    }

    /**
     * Removes a step instance from the database.
     * @param int $stepinstanceid step instance id
     */
    private static function remove($stepinstanceid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        if ($record = $DB->get_record('tool_cleanupcourses_step', array('id' => $stepinstanceid))) {
            $step = step_subplugin::from_record($record);
            self::remove_from_sortindex($step);
            settings_manager::remove_settings($step->id, SETTINGS_TYPE_STEP);
            $DB->delete_records('tool_cleanupcourses_step', (array) $step);
        }
        $transaction->allow_commit();
    }

    /**
     * Removes a subplugin from the sortindex of a workflow and adjusts all other indizes.
     * @param step_subplugin $toberemoved
     */
    private static function remove_from_sortindex(&$toberemoved) {
        global $DB;
        if (isset($toberemoved->sortindex)) {
            $subplugins = $DB->get_records_select('tool_cleanupcourses_step',
                "sortindex > $toberemoved->sortindex",
                array('workflowid' => $toberemoved->workflowid));
            foreach ($subplugins as $record) {
                $subplugin = step_subplugin::from_record($record);
                $subplugin->sortindex--;
                self::insert_or_update($subplugin);
            }
        }
    }

    /**
     * Changes the sortindex of a step by swapping it with another.
     * @param int $stepid id of the step
     * @param bool $up tells if the step should be set up or down
     */
    public static function change_sortindex($stepid, $up) {
        global $DB;
        $step = self::get_step_instance($stepid);
        // Prevent first entry to be put up even more.
        if ($step->sortindex == 1 && $up) {
            return;
        }
        // Prevent last entry to be put down even more.
        if ($step->sortindex == self::count_steps_of_workflow($step->workflowid) && !$up) {
            return;
        }
        $index = $step->sortindex;
        if ($up) {
            $otherindex = $index - 1;
        } else {
            $otherindex = $index + 1;
        }
        $transaction = $DB->start_delegated_transaction();

        $otherrecord = $DB->get_record('tool_cleanupcourses_step',
            array(
                'sortindex' => $otherindex,
                'workflowid' => $step->workflowid)
        );
        $otherstep = step_subplugin::from_record($otherrecord);

        $step->sortindex = $otherindex;
        $otherstep->sortindex = $index;
        self::insert_or_update($step);
        self::insert_or_update($otherstep);

        $transaction->allow_commit();
    }

    /**
     * Gets the list of step instances of a workflow.
     * @param int $workflowid id of the workflow
     * @return array of step instances.
     */
    public static function get_step_instances($workflowid) {
        // TODO: Alter calls to include workflow id.
        global $DB;
        $records = $DB->get_records('tool_cleanupcourses_step',
            array(
                'workflowid' => $workflowid
            ));
        $steps = array();
        foreach ($records as $id => $record) {
            $steps[$id] = $record->instancename;
        }
        return $steps;
    }

    /**
     * Gets the list of step instances for a specific subpluginname.
     * @return step_subplugin[] array of step instances.
     */
    public static function get_step_instances_by_subpluginname($subpluginname) {
        global $DB;
        $records = $DB->get_records('tool_cleanupcourses_step', array('subpluginname' => $subpluginname));
        $steps = array();
        foreach ($records as $id => $record) {
            $steps[$id] = step_subplugin::from_record($record);
        }
        return $steps;
    }

    /**
     * Gets the list of step subplugins.
     * @return array of step subplugins.
     */
    public static function get_step_types() {
        $subplugins = \core_component::get_plugin_list('cleanupcoursesstep');
        $result = array();
        foreach ($subplugins as $id => $plugin) {
            $result[$id] = get_string('pluginname', 'cleanupcoursesstep_' . $id);
        }
        return $result;
    }

    /**
     * Handles an action of the subplugin_settings.
     * @param string $action action to be executed
     * @param int $subpluginid id of the subplugin
     */
    public static function handle_action($action, $subpluginid) {
        if ($action === ACTION_UP_STEP) {
            self::change_sortindex($subpluginid, true);
        }
        if ($action === ACTION_DOWN_STEP) {
            self::change_sortindex($subpluginid, false);
        }
        if ($action === ACTION_STEP_INSTANCE_DELETE) {
            self::remove($subpluginid);
        }
    }

    /**
     * Returns if the process data is saved instance or subplugin dependent.
     * Default if false. Can be overriden in show_relevant_courses_instance_dependent() of the interactionlib.
     * @param int $stepid id of the step process data should be saved for.
     * @return bool if true data is saved instance dependent.
     * Otherwise it does not matter which instance of a subplugin created the data.
     */
    public static function is_process_data_instance_dependent($stepid) {
        $step = self::get_step_instance($stepid);
        $interactionlib = lib_manager::get_step_interactionlib($step->subpluginname);
        if (!$interactionlib) {
            return false;
        }
        return $interactionlib->show_relevant_courses_instance_dependent();
    }

    /**
     * Gets the count of steps belonging to a workflow.
     * @param int $workflowid id of the workflow.
     * @return int count of the steps.
     */
    public static function count_steps_of_workflow($workflowid) {
        global $DB;
        return $DB->count_records('tool_cleanupcourses_step',
            array('workflowid' => $workflowid)
        );
    }

    /**
     * Removes all instances, which belong to the workflow instance.
     * @param $workflowid int id of the workflow.
     */
    public static function remove_instances_of_workflow($workflowid) {
        global $DB;
        $DB->delete_records('tool_cleanupcourses_step', array('workflowid' => $workflowid));
    }
}
