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
 * @package    tool_lifecycle
 * @copyright  2018 Tobias Reischmann, Nina Herrmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$observers = array(
    // Create or delete course is not considered since in this case the role_assigned or role_unassigned event is thrown.
    array(
        'eventname'   => '\core\event\role_assigned',
        'callback'    => 'tool_lifecycle\observer::role_changed'
    ),
    array(
        'eventname'   => '\core\event\role_unassigned',
        'callback'    => 'tool_lifecycle\observer::role_changed'
    )
);