<?php

use block_sharing_cart\required_capabilities;

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

$date = userdate(time(), '%Y%m%d_%H%M%S');

$hubcourseid = optional_param('id', 0, PARAM_INT);
if ($hubcourseid) {
  $hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
  if (!$hubcourse) {
    throw new moodle_exception('Hubcourse not found', 'block_hubcourseinfo');
  }
  $hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);
  $coursecontext = $hubcoursecontext->get_course_context();
  $course = $DB->get_record('course', ['id' => $coursecontext->instanceid]);
  if (!$course) {
    throw new moodle_exception('Course not found', 'block_hubcourseinfo');
  }
  require_login($course);
  require_capability('block/hubcourseinfo:managecourse', $hubcoursecontext);
  $metadatafields = block_hubcourseinfo_getmetadatafields();
  $filename = $date . '-' . $course->shortname . '.csv';
  $f = block_hubcourseinfo_getmetadatacsvstreamheader($filename, $metadatafields);
  block_hubcourseinfo_putmetadatacsv($f, $course, $hubcourse, $metadatafields);
  block_hubcourseinfo_closemetadatacsv($f);
  exit;
} else {
  // export all
  require_login();
  require_capability('block/hubcourseinfo:exportmetadataall', context_system::instance());
  $metadatafields = block_hubcourseinfo_getmetadatafields();
  $filename = $date . '-' . 'all_courses.csv';
  $f = block_hubcourseinfo_getmetadatacsvstreamheader($filename, $metadatafields);
  $hubcourses = $DB->get_records('block_hubcourses', [], 'id ASC');
  foreach ($hubcourses as $hubcourse) { 
    $course = $DB->get_record('course', ['id' => $hubcourse->courseid]);
    if (!$course) {
      continue;
    }
    block_hubcourseinfo_putmetadatacsv($f, $course, $hubcourse, $metadatafields);
  }
  block_hubcourseinfo_closemetadatacsv($f);
  exit;
}
