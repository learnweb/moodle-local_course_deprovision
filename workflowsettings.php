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
 * Displays the settings associated with one single workflow and handles action for it.
 *
 * @package tool_cleanupcourses
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');

require_login();

require_once(__DIR__ . '/adminlib.php');

$workflowid = required_param('workflowid', PARAM_INT);

$workflow = tool_cleanupcourses\manager\workflow_manager::get_workflow($workflowid);

if (!$workflow) {
    throw new moodle_exception('workflownotfound', 'tool_cleanupcourses',
        new \moodle_url('/admin/tool/cleanupcourses/adminsettings.php'), $workflowid);
}

// Create the class for this controller.
$workflowsettings = new tool_cleanupcourses\workflow_settings($workflowid);

$PAGE->set_context(context_system::instance());

// Execute the controller.
$workflowsettings->execute(optional_param('action', null, PARAM_TEXT),
    optional_param('subplugin', null, PARAM_INT));