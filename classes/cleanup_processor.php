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
 * Offers functionality to trigger, process and finish cleanup processes.
 *
 * @package tool_cleanupcourses
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_cleanupcourses;

use tool_cleanupcourses\entity\trigger_subplugin;
use tool_cleanupcourses\manager\process_manager;
use tool_cleanupcourses\manager\step_manager;
use tool_cleanupcourses\manager\trigger_manager;
use tool_cleanupcourses\manager\lib_manager;
use tool_cleanupcourses\manager\workflow_manager;
use tool_cleanupcourses\response\step_interactive_response;
use tool_cleanupcourses\response\step_response;
use tool_cleanupcourses\response\trigger_response;


defined('MOODLE_INTERNAL') || die;

class cleanup_processor {

    public function __construct() {

    }

    /**
     * Processes the trigger plugins for all relevant courses.
     */
    public function call_trigger() {
        $activeworkflows = workflow_manager::get_active_automatic_workflows();

        $recordset = $this->get_course_recordset();
        while ($recordset->valid()) {
            $course = $recordset->current();
            foreach ($activeworkflows as $workflow) {
                $trigger = trigger_manager::get_trigger_for_workflow($workflow->id);
                $lib = lib_manager::get_automatic_trigger_lib($trigger->subpluginname);
                $response = $lib->check_course($course, $trigger->id);
                if ($response == trigger_response::next()) {
                    continue;
                }
                if ($response == trigger_response::exclude()) {
                    break;
                }
                if ($response == trigger_response::trigger()) {
                    process_manager::create_process($course->id, $trigger->workflowid);
                    break;
                }
            }
            $recordset->next();
        }
    }

    /**
     * Calls the process_course() method of each step submodule currently responsible for a given course.
     */
    public function process_courses() {
        foreach (process_manager::get_processes() as $process) {
            while (true) {

                if ($process->stepindex == 0) {
                    process_manager::proceed_process($process);
                }

                $step = step_manager::get_step_instance_by_workflow_index($process->workflowid, $process->stepindex);
                $lib = lib_manager::get_step_lib($step->subpluginname);
                try {
                    $course = get_course($process->courseid);
                } catch (\dml_missing_record_exception $e) {
                    // Course no longer exists!
                    break;
                }
                if ($process->waiting) {
                    $result = $lib->process_waiting_course($process->id, $step->id, $course);
                } else {
                    $result = $lib->process_course($process->id, $step->id, $course);
                }
                if ($result == step_response::waiting()) {
                    process_manager::set_process_waiting($process);
                    break;
                } else if ($result == step_response::proceed()) {
                    if (!process_manager::proceed_process($process)) {
                        break;
                    }
                } else if ($result == step_response::rollback()) {
                    process_manager::rollback_process($process);
                    break;
                } else {
                    throw new \moodle_exception('Return code \''. var_dump($result) . '\' is not allowed!');
                }
            }
        }

    }

    /**
     *
     * @param $processid int id of the process
     * @return boolean if true, interaction finished.
     *      If false, the current step is still processing and cares for displaying the view.
     */
    public function process_course_interactive($processid) {
        $process = process_manager::get_process_by_id($processid);
        $step = step_manager::get_step_instance_by_workflow_index($process->workflowid, $process->stepindex + 1);
        // If there is no next step, then proceed, which will delete/finish the process.
        if (!$step) {
            process_manager::proceed_process($process);
            return true;
        }
        if ($interactionlib = lib_manager::get_step_interactionlib($step->subpluginname)) {
            // Actually proceed to the next step.
            process_manager::proceed_process($process);
            $response = $interactionlib->handle_interaction($process, $step);
            switch ($response) {
                case step_interactive_response::still_processing():
                    return false;
                    break;
                case step_interactive_response::no_action():
                    break;
                case step_interactive_response::proceed():
                    // In case of proceed, call recursively.
                    return $this->process_course_interactive($processid);
                    break;
                case step_interactive_response::rollback():
                    process_manager::rollback_process($process);
                    break;
            }
            return true;
        }
        return true;
    }

    /**
     * Returns a record set with all relevant courses.
     * Relevant means that there is currently no cleanup process running for this course.
     * @return \moodle_recordset with relevant courses.
     */
    private function get_course_recordset() {
        global $DB;
        $sql = 'SELECT {course}.* from {course} '.
            'left join {tool_cleanupcourses_process} '.
            'ON {course}.id = {tool_cleanupcourses_process}.courseid '.
            'WHERE {tool_cleanupcourses_process}.courseid is null';
        return $DB->get_recordset_sql($sql);
    }

}
