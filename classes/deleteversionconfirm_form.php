<?php
require_once(__DIR__ . '/../../../lib/formslib.php');

class deleteversionconfirm_form extends moodleform {
    private $version;

    public function __construct($version) {
        $this->version = $version;
        parent::__construct();
    }

    public function definition() {
        $form = &$this->_form;

        $form->addElement('html', html_writer::tag('h3', get_string('deleteversionconfirm_title', 'block_hubcourseinfo')));
        $form->addElement('html', html_writer::tag('p', get_string('deleteversionconfirm_description', 'block_hubcourseinfo')));

        $form->addElement('hidden', 'id', $this->version->id);

        $this->add_action_buttons(true, get_string('delete'));
    }
}