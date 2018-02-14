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
 * Course Cleanup langauge strings.
 *
 * @package tool_cleanupcourses
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



$string['pluginname'] = 'Cleanup Courses';
$string['plugintitle'] = 'Cleanup Courses';

$string['general_config_header'] = "General & Subplugins";
$string['config_delay_duration'] = 'Duration of a course delay';
$string['config_delay_duration_desc'] = 'Defines the time frame, which a course is excluded from the cleanup course, when rolled back via user interaction.';
$string['active_processes_list_header'] = 'Active Processes';
$string['subpluginssettings_heading'] = 'Subplugin Workflow';
$string['active_workflows_heading'] = 'Active Workflows';
$string['workflow_definition_heading'] = 'Workflow Definitions';
$string['subpluginssettings_edit_workflow_definition_heading'] = 'Workflow Definition';
$string['subpluginssettings_workflow_definition_steps_heading'] = 'Workflow Steps';
$string['subpluginssettings_edit_trigger_instance_heading'] = 'Trigger for workflow \'{$a}\'';
$string['subpluginssettings_edit_step_instance_heading'] = 'Step Instance for workflow \'{$a}\'';
$string['add_new_step_instance'] = 'Add New Step Instance...';
$string['step_settings_header'] = 'Specific settings of the step type';
$string['trigger_settings_header'] = 'Specific settings of the trigger type';
$string['general_settings_header'] = 'General Settings';
$string['followedby_none'] = 'None';
$string['invalid_workflow'] = 'Invalid workflow configuration';
$string['invalid_workflow_details'] = 'Go to details view, to create a trigger for this workflow';

$string['process_cleanup'] = 'Run the cleanup courses processes';

$string['trigger_subpluginname'] = 'Subplugin Name';
$string['trigger_instancename'] = 'Instance Name';
$string['trigger_enabled'] = 'Enabled';
$string['trigger_sortindex'] = 'Up/Down';
$string['trigger_workflow'] = 'Workflow';

$string['add_workflow'] = 'Add Workflow';
$string['workflow_title'] = 'Title';
$string['workflow_active'] = 'Active';
$string['workflow_processes'] = 'Active processes';
$string['workflow_timeactive'] = 'Active since';
$string['workflow_sortindex'] = 'Up/Down';
$string['workflow_tools'] = 'Tools';
$string['viewsteps'] = 'View Workflow Steps';
$string['editworkflow'] = 'Edit Title';
$string['deleteworkflow'] = 'Delete Workflow';
$string['activateworkflow'] = 'Activate';

$string['step_type'] = 'Type';
$string['step_subpluginname'] = 'Subplugin Name';
$string['step_instancename'] = 'Instance Name';
$string['step_sortindex'] = 'Up/Down';
$string['step_edit'] = 'Edit';
$string['step_delete'] = 'Delete';

$string['trigger'] = 'Trigger';
$string['step'] = 'Process step';

$string['workflow_trigger'] = 'Trigger for the workflow';

$string['cleanupcoursestrigger'] = 'Trigger';
$string['cleanupcoursesstep'] = 'Process step';

$string['subplugintype_cleanupcoursestrigger'] = 'Trigger for starting the course cleanup';
$string['subplugintype_cleanupcoursestrigger_plural'] = 'Triggers for starting the course cleanup';
$string['subplugintype_cleanupcoursesstep'] = 'Step within a course cleanup process';
$string['subplugintype_cleanupcoursesstep_plural'] = 'Steps within a course cleanup process';

$string['nointeractioninterface'] = 'No Interaction Interface available!';
$string['tools'] = 'Tools';
$string['status'] = 'Status';

$string['nostepfound'] = 'A step with the given stepid could not be found!';
$string['noprocessfound'] = 'A process with the given processid could not be found!';

$string['nocoursestodisplay'] = 'There are currently no courses, which require your attention!';

$string['course_backups_list_header'] = 'Course Backups';
$string['backupcreated'] = 'Created at';
$string['restore'] = 'restore';

$string['workflownotfound'] = 'Workflow with id {$a} could not be found';
