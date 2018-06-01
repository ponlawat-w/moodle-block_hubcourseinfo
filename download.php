<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$versionid = required_param('version', PARAM_INT);
$version = $DB->get_record('block_hubcourse_versions', ['id' => $versionid]);
if (!$version) {
    throw new Exception(get_string('notknow', 'block_hubcourseinfo'));
}
$hubcourse = $DB->get_record('block_hubcourses', ['id' => $version->hubcourseid]);
if (!$hubcourse) {
    throw new Exception(get_string('notknow', 'block_hubcourseinfo'));
}

$hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);

require_login();
require_capability('block/hubcourseinfo:downloadcourse', $hubcoursecontext);

$fs = get_file_storage();

$files = $fs->get_area_files($hubcoursecontext->id, 'block_hubcourse', 'course', $versionid);
foreach ($files as $file) {
    $exts = explode('.', $file->get_filename());
    $ext = $exts[count($exts) - 1];

    if ($ext == 'mbz') {
        header('Content-Type: ' . $file->get_mimetype());
        header('Content-Length: ' . $file->get_filesize());
        header('Content-Disposition: attachment; filename="' . $file->get_filename() . '"');

        $fstream = $file->get_content_file_handle();
        while (!feof($fstream)) {
            echo fgets($fstream);
        }
        fclose($fstream); exit;
    }
}

throw new Exception('unexpected');