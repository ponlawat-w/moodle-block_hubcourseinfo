<?php

use core_privacy\local\request\writer;

require_once(__DIR__ . '/../../../../lib/formslib.php');

class deletemetadatafieldconfirm_form extends moodleform {
  private $metadatafield;

  public function __construct($metadatafield) {
    $this->metadatafield = $metadatafield;
    parent::__construct();
  }

  public function definition() {
    $form = &$this->_form;
    
    $form->addElement('html', html_writer::tag('h3', get_string('deletemetadatafieldconfirm_title', 'block_hubcourseinfo', $this->metadatafield->name), ['class' => 'text-danger']));
    $form->addElement('html', html_writer::tag('p', get_string('deletemetadatafieldconfirm_description', 'block_hubcourseinfo')));

    $form->addElement('hidden', 'id', $this->metadatafield->id);
    $form->setType('id', PARAM_INT);

    $this->add_action_buttons(true, get_string('delete'));
  }
}
