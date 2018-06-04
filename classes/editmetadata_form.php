<?php
require_once(__DIR__ . '/../../../lib/formslib.php');

class editmetadata_form extends moodleform
{
    private $hubcourse;
    private $course;
    private $new;

    public function __construct($hubcourse, $new = 0)
    {
        global $DB;

        $this->hubcourse = $hubcourse;
        $this->course = $DB->get_record('course', ['id' => $hubcourse->courseid]);
        if (!$this->course) {
            throw new Exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
        }

        $this->new = $new;

        parent::__construct();
    }

    public function definition()
    {
        global $DB;
        $categories = $DB->get_records('course_categories', ['visible' => 1]);
        $categoriesoptions = [];
        foreach ($categories as $category) {
            $categoriesoptions[$category->id] = $category->name;
        }

        $form = &$this->_form;

        $form->addElement('text', 'fullname', get_string('fullnamecourse'), ['style' => 'width: 100%;']);
        $form->setDefault('fullname', $this->course->fullname);
        $form->addRule('fullname', get_string('required'), 'required');

        $form->addElement('text', 'shortname', get_string('shortnamecourse'), ['style' => 'width: 100%;']);
        $form->setDefault('shortname', $this->course->shortname);
        $form->addRule('shortname', get_string('required'), 'required');

        $form->addElement('select', 'category', get_string('category'), $categoriesoptions);
        $form->setDefault('category', $this->course->category);

        $form->addElement('text', 'demourl', get_string('demourl', 'block_hubcourseinfo'), ['style' => 'width: 100%;']);
        $form->setDefault('demourl', $this->hubcourse->demourl);
        $form->setType('demourl', PARAM_URL);

        $form->addElement('textarea', 'description', get_string('description'), ['style' => 'width: 100%;', 'rows' => '5']);
        $form->setDefault('description', $this->hubcourse->description);

        $form->addElement('hidden', 'id', $this->hubcourse->id);
        $form->addElement('hidden', 'new', $this->new);

        $this->add_action_buttons(!$this->new, get_string('save'));
    }
}