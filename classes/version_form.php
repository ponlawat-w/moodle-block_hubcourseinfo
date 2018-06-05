<?php
require_once(__DIR__ . '/../../../lib/formslib.php');
require_once(__DIR__ . '/../../../backup/util/includes/restore_includes.php');

class version_form extends moodleform {
    private $hubcourseid;
    private $versionid;
    private $edit;
    private $defaultdata;

    public function __construct($hubcourseid, $versionid = 0, $edit = false, $defaultdata = null) {
        $this->hubcourseid = $hubcourseid;
        $this->versionid = $versionid;
        $this->edit = $edit;
        $this->info = null;
        $this->defaultdata = $defaultdata;
        parent::__construct();
    }

    public function definition() {
        $form = &$this->_form;

        $form->addElement('text', 'description', get_string('description'));
        $form->addRule('description', get_string('required'), 'required');

        if ($this->edit) {
            $form->addElement('hidden', 'id', $this->versionid);
            $form->setDefault('description', $this->defaultdata->description);

            $this->add_action_buttons(true, get_string('save'));
        } else {
            $form->addElement('hidden', 'id', $this->hubcourseid);

            $maxbytes = block_hubcourseinfo_getmaxfilesize();
            $form->addElement('filepicker', 'coursefile', get_string('coursefile', 'block_hubcourseinfo'), null,
                ['maxbytes' => $maxbytes, 'accepted_types' => '.mbz']);
            $form->addElement('html', get_string('maxfilesize', 'block_hubcourseinfo', $maxbytes / 1024 / 1024));
            $form->addRule('coursefile', get_string('required'), 'required');

            $this->add_action_buttons(true, get_string('add'));
        }
    }
}