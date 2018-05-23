<?php
require_once(__DIR__ . '/../../../lib/formslib.php');

class review_form extends moodleform
{
    private $hubcourse;
    private $editing;

    public function __construct($hubcourse, $editing)
    {
        $this->hubcourse = $hubcourse;
        $this->editing = $editing;

        parent::__construct();
    }

    private function generatestars($max = 5)
    {
        $stars = array(0 => get_string('pleaserate', 'block_hubcourseinfo'));
        for ($i = 5; $i > 0; $i--) {
            $text = '';
            for ($s = 0; $s < $i; $s++) {
                $text .= '★';
            }
            for ($s = $i; $s < $max; $s++) {
                $text .= '☆';
            }

            $stars[$i] = $text;
        }

        return $stars;
    }

    public function definition()
    {
        $form = &$this->_form;

        $stars = $this->generatestars();

        $form->addElement('select', 'rate', get_string('ratethiscourse', 'block_hubcourseinfo'), $stars);
        $form->addElement('editor', 'comment', get_string('comment', 'block_hubcourseinfo'));

        $this->add_action_buttons(true, get_string($this->editing ? 'editreview' : 'submitreview', 'block_hubcourseinfo'));

        $form->addElement('hidden', 'id', $this->hubcourse->id);
    }

    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if ($data['rate'] < 1 || $data['rate'] > 5) {
            $errors['rate'] = get_string('reviewerr_pleaserate', 'block_hubcourseinfo');
        }

        if (trim($data['comment']['text']) == '') {
            $errors['comment'] = get_string('reviewerr_pleasecomment', 'block_hubcourseinfo');
        }

        return $errors;
    }
}