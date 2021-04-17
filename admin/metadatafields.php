<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/classes/metadatafield_form.php');

require_login();
require_capability('block/hubcourseinfo:managemetadatafields', context_system::instance());

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/admin/metadatafields.php');
$PAGE->set_title(get_string('managemetadatafields', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);

$PAGE->navbar
  ->add(get_string('administrationsite'), new moodle_url('/admin/search.php'))
  ->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']))
  ->add(get_string('blocks'), new moodle_url('/admin/category.php', ['category' => 'blocksettings']))
  ->add(get_string('pluginname', 'block_hubcourseinfo'), new moodle_url('/admin/settings.php', ['section' => 'blocksettinghubcourseinfo']))
  ->add(get_string('managemetadatafields', 'block_hubcourseinfo'));

$metadatafields = block_hubcourseinfo_getmetadatafields();
$fieldstable = new html_table();
$fieldstable->head = [
  get_string('name'),
  get_string('required'),
  '',
  ''
];
$fieldstable->data = [];
foreach ($metadatafields as $metadatafield) {
  $fieldstable->data[] = [
    $metadatafield->name,
    $metadatafield->required ? html_writer::start_tag('i', ['class' => 'fa fa-check']) : '',
    html_writer::link(new moodle_url('/blocks/hubcourseinfo/admin/editmetadatafield.php', ['id' => $metadatafield->id]), html_writer::start_tag('i', ['class' => 'fa fa-pencil'])),
    html_writer::link(new moodle_url('/blocks/hubcourseinfo/admin/deletemetadatafield.php', ['id' => $metadatafield->id]), html_writer::start_tag('i', ['class' => 'fa fa-trash']))
  ];
}
$fieldstable->attributes['class'] = 'table';
$fieldstable->attributes['style'] = 'width: auto !important;';

$newfieldform = new metadatafield_form();
if ($newfieldform->is_submitted() && $newfieldform->is_validated()) {
  $data = $newfieldform->get_data();

  $metadatafield = new stdClass();
  $metadatafield->id = $data->id;
  $metadatafield->name = $data->name;
  $metadatafield->required = isset($data->required) && $data->required ? 1 : 0;
  $metadatafield->type = $data->type;
  if ($DB->insert_record('block_hubcourse_metafields', $metadatafield)) {
    redirect($PAGE->url);
  } else {
    throw new moodle_exception('Cannot add a new field', 'block_hubcourseinfo');
  }
}

echo $OUTPUT->header();

if ($metadatafields && count($metadatafields)) {
  echo html_writer::tag('h4', get_string('metadatafields', 'block_hubcourseinfo'));
  echo html_writer::table($fieldstable);
  echo html_writer::start_tag('hr');
}

echo html_writer::tag('h4', get_string('newfield', 'block_hubcourseinfo'));
$newfieldform->display();

echo $OUTPUT->footer();
