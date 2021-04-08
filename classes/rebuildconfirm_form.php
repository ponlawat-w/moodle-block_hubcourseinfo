<?php

require_once(__DIR__  . '/../../../lib/formslib.php');

class rebuildconfirm_form extends moodleform {
  private $versionid;
  private $hubcourseid;

  public function __construct($versionid, $hubcourseid) {
    $this->versionid = $versionid;
    $this->hubcourseid = $hubcourseid;
    parent::__construct();
  }

  public function definition() {
    $form = &$this->_form;
    $form->addElement('html', html_writer::tag('h3', get_string('rebuildconfirm_title', 'block_hubcourseinfo')));
    if ($this->versionid) {
      $form->addElement('html', html_writer::tag('p', get_string('rebuildconfirm_description', 'block_hubcourseinfo')));
    } else {
      $form->addElement('text', 'description', get_string('description'));
      $form->addRule('description', get_string('required'), 'required');
      $form->setType('description', PARAM_TEXT);
    }

    $form->addElement('hidden', 'vid', $this->versionid);
    $form->addElement('hidden', 'hid', $this->hubcourseid);
    $form->setType('vid', PARAM_INT);
    $form->setType('hid', PARAM_INT);

    $this->add_action_buttons(true, get_string('rebuild', 'block_hubcourseinfo'));
  }
}
