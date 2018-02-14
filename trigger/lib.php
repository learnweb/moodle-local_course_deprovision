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
 * Interface for the subplugintype trigger
 * It has to be implemented by all subplugins.
 *
 * @package tool_cleanupcourses
 * @subpackage trigger
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_cleanupcourses\trigger;

use tool_cleanupcourses\response\trigger_response;

defined('MOODLE_INTERNAL') || die();

abstract class base {


    /**
     * Checks the course and returns a repsonse, which tells if the course should be further processed.
     * @param $course object to be processed.
     * @param $triggerid int id of the trigger instance.
     * @return trigger_response
     */
    public abstract function check_course($course, $triggerid);

    /**
     * The return value should be equivalent with the name of the subplugin folder.
     * @return string technical name of the subplugin
     */
    public abstract function get_subpluginname();

    /**
     * @return instance_setting[] containing settings keys and PARAM_TYPES
     */
    public function instance_settings() {
        return array();
    }

    /**
     * This method can be overriden, to add form elements to the form_step_instance.
     * It is called in definition().
     * @param \MoodleQuickForm $mform
     */
    public function extend_add_instance_form_definition($mform) {
    }

    /**
     * This method can be overriden, to set default values to the form_step_instance.
     * It is called in definition_after_data().
     * @param \MoodleQuickForm $mform
     * @param array $settings array containing the settings from the db.
     */
    public function extend_add_instance_form_definition_after_data($mform, $settings) {
    }


    /**
     * Generates the link to the interaction table of the respective step
     * @param int $stepid id of the step
     * @return \moodle_url
     */
    public function get_interaction_link($stepid) {
        return new \moodle_url('admin/tool/cleanupcourses/view.php', array('stepid' => $stepid));
    }

    /**
     * If true, the trigger can be used to manually define workflows, based on an instance of this trigger.
     * This has to be combined with installing the workflow in db/install.php of the trigger plugin.
     * If false, at installation the trigger will result in a preset workflow, which can not be changed.
     * This is for instance relevant for the sitecourse trigger or the delayedcourses trigger.
     * @return bool
     */
    public function has_multiple_instances() {
        return false;
    }

}

/**
 * Class representing a local settings object for a subplugin instance.
 * @package tool_cleanupcourses\trigger
 */
class instance_setting {

    /** @var string name of the setting*/
    public $name;

    /** @var string param type of the setting, e.g. PARAM_INT */
    public $paramtype;

    /**
     * Create a local settings object.
     * @param string $name name of the setting
     * @param string $paramtype param type. Used for cleansing and parsing, e.g. PARAM_INT.
     */
    public function __construct($name, $paramtype) {
        $this->name = $name;
        $this->paramtype = $paramtype;
    }

}
