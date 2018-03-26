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

defined('MOODLE_INTERNAL') || die();

use tool_lifecycle\entity\process;
use tool_lifecycle\entity\trigger_subplugin;
use tool_lifecycle\entity\step_subplugin;
use tool_lifecycle\entity\workflow;
use tool_lifecycle\manager\settings_manager;
use tool_lifecycle\manager\trigger_manager;
use tool_lifecycle\manager\step_manager;
use tool_lifecycle\manager\workflow_manager;

/**
 * tool_lifecycle generator tests
 *
 * @package    tool_lifecycle
 * @category   test
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lifecycle_generator extends testing_module_generator {

    /**
     * Creates an artificial workflow without steps.
     */
    public static function create_workflow() {
        // Create Workflow.
        $record = new stdClass();
        $record->id = null;
        $record->title = 'myworkflow';
        $workflow = workflow::from_record($record);
        workflow_manager::insert_or_update($workflow);
        // Create trigger.
        $record = new stdClass();
        $record->subpluginname = 'startdatedelay';
        $record->instancename = 'startdatedelay';
        $record->workflowid = $workflow->id;
        $trigger = trigger_subplugin::from_record($record);
        trigger_manager::insert_or_update($trigger);
        return $workflow;
    }

    /**
     * Creates an artificial workflow without steps, which is triggered manually.
     * @param \stdClass $triggersettings settings of the manual trigger
     * @return workflow
     */
    public static function create_manual_workflow($triggersettings) {
        // Create Workflow.
        $record = new stdClass();
        $record->id = null;
        $record->title = 'myworkflow';
        $workflow = workflow::from_record($record);
        workflow_manager::insert_or_update($workflow);
        // Create trigger.
        $record = new stdClass();
        $record->subpluginname = 'manual';
        $record->instancename = 'manual';
        $record->workflowid = $workflow->id;
        $trigger = trigger_subplugin::from_record($record);
        trigger_manager::insert_or_update($trigger);
        settings_manager::save_settings($trigger->id, SETTINGS_TYPE_TRIGGER, $trigger->subpluginname, $triggersettings);
        return $workflow;
    }

    /**
     * Creates a step for a given workflow and stores it in the DB
     * @param $instancename
     * @param $subpluginname
     * @param $workflowid
     * @return step_subplugin created step
     */
    public static function create_step($instancename, $subpluginname, $workflowid) {
        $step = new step_subplugin($instancename, $subpluginname, $workflowid);
        step_manager::insert_or_update($step);
        return $step;
    }

    /**
     * Creates an artificial workflow with three steps.
     */
    public static function create_workflow_with_steps() {
        $workflow = self::create_workflow();
        self::create_step('instance1', 'subpluginname', $workflow->id);
        self::create_step('instance2', 'subpluginname', $workflow->id);
        self::create_step('instance3', 'subpluginname', $workflow->id);
        return $workflow;
    }

    public static function create_process($courseid, $workflowid) {
        global $DB;

        $record = new \stdClass();
        $record->id = null;
        $record->courseid = $courseid;
        $record->workflowid = $workflowid;
        $record->timestepchanged = time();
        $record->stepindex = 0;
        $process = process::from_record($record);
        $process->id = $DB->insert_record('tool_lifecycle_process', $process);
        return $process;
    }
}
