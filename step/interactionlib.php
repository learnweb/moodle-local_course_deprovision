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
 * Interface for the interactions of the subplugintype step
 * It has to be implemented by all subplugins that want to use the interaction view.
 *
 * @package tool_lifecycle
 * @subpackage step
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\step;

use tool_lifecycle\entity\process;
use tool_lifecycle\entity\step_subplugin;
use tool_lifecycle\response\step_interactive_response;

defined('MOODLE_INTERNAL') || die();

abstract class interactionlibbase {

    /**
     * Returns the capability a user has to have to make decisions for a specific course.
     * @return string capability string.
     */
    public abstract function get_relevant_capability();

    /**
     * Returns if only the courses of a step instance should be shown or all courses for the submodule.
     * @return bool true, if only the courses of a step instance should be shown; otherwise false.
     */
    public function show_relevant_courses_instance_dependent() {
        return false;
    }

    /**
     * Returns an array of interaction tools to be displayed to be displayed on the view.php for the given process
     * Every entry is itself an array which consist of three elements:
     *  'action' => an action string, which is later passed to handle_action
     *  'alt' => a string text of the button
     * @param process $process process the action tools are requested for
     * @return array of action tools
     */
    public abstract function get_action_tools($process);


    /**
     * Returns the status message for the given process.
     * @param process $process process the status message is requested for
     * @return string status message
     */
    public abstract function get_status_message($process);

    /**
     * Called when a user triggered an action for a process instance.
     * @param process $process instance of the process the action was triggered upon.
     * @param step_subplugin $step instance of the step the process is currently in.
     * @param string $action action string. The function is called with 'default', during interactive processing.
     * @return step_interactive_response defines if the step still wants to process this course
     *      - proceed: the step has finished and respective controller class can take over.
     *      - stillprocessing: the step still wants to process the course and is responsible for rendering the site.
     *      - noaction: the action is not defined for the step.
     *      - rollback: the step has finished and respective controller class should rollback the process.
     */
    public abstract function handle_interaction($process, $step, $action = 'default');

    /**
     * Returns the due date.
     * @return null | timestamp
     */
    public function get_due_date($processid, $stepid) {
        return null;
    }
}