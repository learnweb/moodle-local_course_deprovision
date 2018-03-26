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
 * Table listing all active automatically triggered workflows.
 *
 * @package tool_lifecycle
 * @copyright  2018 Jan Dageförde WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\table;

use tool_lifecycle\manager\process_manager;
use tool_lifecycle\manager\step_manager;
use tool_lifecycle\manager\trigger_manager;
use tool_lifecycle\manager\workflow_manager;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');
require_once(__DIR__ . '/../../lib.php');

abstract class workflow_table extends \table_sql {

    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->set_attribute('class', $this->attributes['class'] . ' ' . $uniqueid);
    }

    /**
     * Render title column.
     * @param $row
     */
    public function col_title($row) {
        return $row->title . '<br><span class="workflow_displaytitle">' . $row->displaytitle . '</span>';
    }

    /**
     * Render activate column.
     * @param $row
     * @return string activate time for workflows
     */
    public function col_timeactive($row) {
        global $OUTPUT, $PAGE;
        if ($row->timeactive) {
            return userdate($row->timeactive, get_string('strftimedatetime'), 0);
        }
        return $OUTPUT->single_button(new \moodle_url($PAGE->url,
            array('action' => ACTION_WORKFLOW_ACTIVATE,
                'sesskey' => sesskey(),
                'workflowid' => $row->id)),
            get_string('activateworkflow', 'tool_lifecycle'));
    }

    /**
     * Render the trigger column.
     * @param $row
     * @return string instancename of the trigger
     */
    public function col_trigger($row) {
        $trigger = trigger_manager::get_trigger_for_workflow($row->id);
        if ($trigger) {
            return $trigger->instancename;
        }
    }

    /**
     * Render the processes column. It shows the number of active processes for the workflow instance.
     * @param $row
     * @return string instancename of the trigger
     */
    public function col_processes($row) {
        return process_manager::count_processes_by_workflow($row->id);
    }

    /**
     * Render tools column.
     * @param $row
     * @return string action buttons for workflows
     */
    public function col_tools($row) {
        global $OUTPUT;
        $output = '';

        $alt = get_string('viewsteps', 'tool_lifecycle');
        $icon = 't/viewdetails';
        $url = new \moodle_url('/admin/tool/lifecycle/workflowsettings.php',
            array('workflowid' => $row->id, 'sesskey' => sesskey()));
        $output .= $OUTPUT->action_icon($url, new \pix_icon($icon, $alt, 'moodle', array('title' => $alt)),
            null , array('title' => $alt));

        return $output;
    }

    /**
     * Util function for writing an action icon link
     *
     * @param string $action URL parameter to include in the link
     * @param string $workflowid URL parameter to include in the link
     * @param string $icon The key to the icon to use (e.g. 't/up')
     * @param string $alt The string description of the link used as the title and alt text
     * @return string The icon/link
     */
    protected function format_icon_link($action, $workflowid, $icon, $alt) {
        global $PAGE, $OUTPUT;

        return $OUTPUT->action_icon(new \moodle_url($PAGE->url,
                array('action' => $action,
                    'sesskey' => sesskey(),
                    'workflowid' => $workflowid)),
                new \pix_icon($icon, $alt, 'moodle', array('title' => $alt)),
                null , array('title' => $alt)) . ' ';
    }

}