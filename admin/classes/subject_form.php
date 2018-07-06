<?php
require_once(__DIR__ . '/../../../../lib/formslib.php');

class subject_form extends moodleform {
    private $id = 0;
    private $defaultname = '';

    public function __construct($id = 0, $name = '') {
        $this->id = $id;
        $this->defaultname = $name;
        parent::__construct();
    }

    public function definition() {
        $form = &$this->_form;

        $form->addElement('text', 'name', get_string('subjectname', 'block_hubcourseinfo'));
        $form->setDefault('name', $this->defaultname);
        $form->addRule('name', get_string('required'), 'required');

        $form->addElement('hidden', 'id');
        $form->setDefault('id', $this->id);

        $this->add_action_buttons($this->id ? true : false, get_string($this->id ? 'edit' : 'add'));
    }
}