<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/deletemetadatafieldconfirm_form.php');

require_login();
require_capability('block/hubcourseinfo:managemetadatafields', context_system::instance());

$id = required_param('id', PARAM_INT);

$metadatafield = $DB->get_record('block_hubcourse_metafields', ['id' => $id]);
if (!$metadatafield) {
  throw new moodle_exception('Field not found', 'block_hubcourseinfo');
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/admin/deletemetadatafield.php', ['id' => $id]);
$PAGE->set_title(get_string('deletemetadatafieldconfirm', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);

$PAGE->navbar
  ->add(get_string('administrationsite'), new moodle_url('/admin/search.php'))
  ->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']))
  ->add(get_string('blocks'), new moodle_url('/admin/category.php', ['category' => 'blocksettings']))
  ->add(get_string('pluginname', 'block_hubcourseinfo'), new moodle_url('/admin/settings.php', ['section' => 'blocksettinghubcourseinfo']))
  ->add(get_string('managemetadatafields', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'));

$confirmform = new deletemetadatafieldconfirm_form($metadatafield);
if ($confirmform->is_submitted()) {
  if ($confirmform->is_cancelled()) {
    redirect(new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'));
    exit;
  }

  $DB->delete_records('block_hubcourse_metavalues', ['fieldid' => $metadatafield->id]);
  if (!$DB->delete_records('block_hubcourse_metafields', ['id' => $metadatafield->id])) {
    throw new moodle_exception('Cannot delete field', 'block_hubcourseinfo');
    exit;
  }
  redirect(new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'));
  exit;
}

echo $OUTPUT->header();
$confirmform->display();
echo $OUTPUT->footer();
  