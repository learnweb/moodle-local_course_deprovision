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

namespace tool_cleanupcourses\entity;

defined('MOODLE_INTERNAL') || die();

/**
 * Cleanup Course Workflow class
 *
 * @package tool_cleanupcourses
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class workflow {

    /** int id of the workflow*/
    public $id;

    /** title of the workflow */
    public $title;

    /** bool true if workflow is active*/
    public $active;

    /** timestamp the workflow was set active */
    public $timeactive;

    /** int sort index of all active workflows */
    public $sortindex;

    private function __construct($id, $title, $active, $timeactive, $sortindex) {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
        $this->timeactive = $timeactive;
        $this->sortindex = $sortindex;
    }

    /**
     * Creates a Workflow from a DB record.
     * @param $record
     * @return workflow
     */
    public static function from_record($record) {
        $id = null;
        if (object_property_exists($record, 'id') && $record->id) {
            $id = $record->id;
        }
        if (!object_property_exists($record, 'title')) {
            return null;
        }

        if (object_property_exists($record, 'active') && $record->active) {
            $active = true;
        } else {
            $active = false;
        }

        $timeactive = null;
        if (object_property_exists($record, 'timeactive') && $record->timeactive) {
            $timeactive = $record->timeactive;
        }

        $sortindex = null;
        if (object_property_exists($record, 'sortindex')) {
            $sortindex = $record->sortindex;
        }

        $instance = new self($id, $record->title, $active, $timeactive, $sortindex);

        return $instance;
    }

}