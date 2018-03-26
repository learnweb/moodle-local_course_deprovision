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
 * Pluginfo for life cycle trigger
 *
 * @package tool_lifecycle
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lifecycle\plugininfo;

use core\plugininfo\base;
use tool_lifecycle\manager\lib_manager;
use tool_lifecycle\manager\step_manager;
use tool_lifecycle\manager\trigger_manager;
use tool_lifecycle\manager\workflow_manager;
use tool_usertours\step;

defined('MOODLE_INTERNAL') || die();


class lifecycletrigger extends base {
    public function is_uninstall_allowed() {
        if ($this->is_standard()) {
            return false;
        }
        // Only allow uninstall, if no active workflow for the trigger is present.
        $triggers = trigger_manager::get_instances($this->name);
        foreach ($triggers as $trigger) {
            if (workflow_manager::is_active($trigger->workflowid)) {
                return false;
            }
        }
        return true;
    }

    public function uninstall(\progress_trace $progress) {
        $lib = lib_manager::get_trigger_lib($this->name);
        if ($lib->has_multiple_instances()) {
            trigger_manager::remove_all_instances($this->name);
        } else {
            $instances = trigger_manager::get_instances($this->name);
            if (count($instances) != 1) {
                throw new \moodle_exception('There should be exactly one workflow for the trigger ' . $this->name);
            }
            $workflow = workflow_manager::get_workflow(array_shift($instances)->workflowid);
            if (step_manager::count_steps_of_workflow($workflow->id) > 0) {
                throw new \moodle_exception('There should be no steps for the workflow of the trigger ' . $this->name);
            }
            workflow_manager::remove($workflow->id);
        }
        return true;
    }
}