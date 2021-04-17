<?php

require_once(__DIR__ . '/../../../../lib/formslib.php');

class metadatafield_form extends moodleform {
  private $metadatafield;

  public function __construct($metadatafield = null) {
    $this->metadatafield = $metadatafield ? $metadatafield : new stdClass();
    if (!$metadatafield) {
      $this->metadatafield->id = 0;
      $this->metadatafield->name = '';
      $this->metadatafield->required = 0;
      $this->metadatafield->type = 'text';
    }
    parent::__construct();
  }

  public function definition() {
    $form = &$this->_form;

    $form->addElement('text', 'name', get_string('metadatafieldname', 'block_hubcourseinfo'));
    $form->setDefault('name', $this->metadatafield->name);
    $form->setType('name', PARAM_TEXT);
    $form->addRule('name', get_string('required'), 'required');

    $form->addElement('checkbox', 'required', get_string('required'));
    $form->setType('required', PARAM_BOOL);
    $form->setDefault('required', $this->metadatafield->required ? true : false);

    $form->addElement('hidden', 'id');
    $form->setDefault('id', $this->metadatafield->id);
    $form->setType('id', PARAM_INT);

    $form->addElement('hidden', 'type');
    $form->setDefault('type', $this->metadatafield->type);
    $form->setType('type', PARAM_TEXT);

    $this->add_action_buttons($this->metadatafield->id ? true : false, get_string($this->metadatafield->id ? 'edit' : 'add'));
  }
}
