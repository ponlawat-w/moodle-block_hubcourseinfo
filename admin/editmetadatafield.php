<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/metadatafield_form.php');

require_login();
require_capability('block/hubcourseinfo:managemetadatafields', context_system::instance());

$id = required_param('id', PARAM_INT);

$metadatafield = $DB->get_record('block_hubcourse_metafields', ['id' => $id]);
if (!$metadatafield) {
  throw new moodle_exception('Field not found', 'block_hubcourseinfo');
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/admin/editmetadatafield.php', ['id' => $id]);
$PAGE->set_title(get_string('managemetadatafields', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);

$PAGE->navbar
  ->add(get_string('administrationsite'), new moodle_url('/admin/search.php'))
  ->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']))
  ->add(get_string('blocks'), new moodle_url('/admin/category.php', ['category' => 'blocksettings']))
  ->add(get_string('pluginname', 'block_hubcourseinfo'), new moodle_url('/admin/settings.php', ['section' => 'blocksettinghubcourseinfo']))
  ->add(get_string('managemetadatafields', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'));

$editmetadatafieldform = new metadatafield_form($metadatafield);
if ($editmetadatafieldform->is_submitted()) {
  if ($editmetadatafieldform->is_cancelled()) {
    redirect(new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'));
  } else if ($editmetadatafieldform->is_validated()) {
    $data = $editmetadatafieldform->get_data();
    $metadatafield = new stdClass();
    $metadatafield->id = $data->id;
    $metadatafield->name = $data->name;
    $metadatafield->required = isset($data->required) && $data->required ? 1 : 0;
    $metadatafield->type = $data->type;

    if ($DB->update_record('block_hubcourse_metafields', $metadatafield)) {
      redirect(new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'));
    } else {
      throw new moodle_exception('Cannot update metadata field', 'block_hubcourseinfo');
    }
  }
} else {
  $editmetadatafieldform = new metadatafield_form($metadatafield);
}

echo $OUTPUT->header();

echo html_writer::tag('h4', get_string('editmetadatafield', 'block_hubcourseinfo', $metadatafield->name));
$editmetadatafieldform->display();

echo $OUTPUT->footer();
