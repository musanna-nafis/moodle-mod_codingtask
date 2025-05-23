<?php


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once(__DIR__.'/../../lib/formslib.php');

class mod_codingtask_mod_form extends moodleform_mod {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('codingtaskname', 'codingtask'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('textarea', 'description', get_string('description', 'codingtask'));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('textarea', 'input', get_string('input', 'codingtask'));
        $mform->setType('input', PARAM_RAW);

        $mform->addElement('textarea', 'expectedoutput', get_string('expectedoutput', 'codingtask'));
        $mform->setType('expectedoutput', PARAM_RAW);
        

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }
}
