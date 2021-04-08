<?php
require_once(__DIR__ . '/../../../backup/util/includes/backup_includes.php');
require_once(__DIR__ . '/../lib.php');

class block_hubcourseinfo_coursebackup {

  private $hubcourseid;
  private $courseid;
  private $versionid;
  private $description;
  public $results = null;
  
  private $fs = null;
  private $hubcoursecontext = null;

  public function __construct($hubcourse, $versionid, $description = '') {
    $this->hubcourseid = $hubcourse->id;
    $this->courseid = $hubcourse->courseid;
    $this->versionid = $versionid;
    $this->description = $description;

    $this->fs = get_file_storage();
    $this->hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourseid($this->hubcourseid);
  }

  private function backup() {
    global $USER;
    $backupcontroller = new backup_controller(
      backup::TYPE_1COURSE,
      $this->courseid,
      backup::FORMAT_MOODLE,
      backup::INTERACTIVE_NO,
      backup::MODE_GENERAL,
      $USER->id
    );
    set_time_limit(0);
    $backupcontroller->set_status(backup::STATUS_AWAITING);
    $plan = $backupcontroller->get_plan();
    $plan->get_setting('skiphubcoursedata')->set_value(true);
    $backupcontroller->execute_plan();
    $this->results = $backupcontroller->get_results();
  }

  private function deleteoldfile() {
    if ($this->versionid) {
      $files = $this->fs->get_area_files($this->hubcoursecontext->id, 'block_hubcourse', 'course', $this->versionid);
      foreach ($files as $file) { 
        $file->delete();
      }
      $this->fs->delete_area_files($this->hubcoursecontext->id, 'block_hubcourse', 'course', $this->versionid);
    }
  }

  private function createversion() {
    global $USER, $DB, $CFG;
    $version = new stdClass();
    $version->id = 0;
    $version->hubcourseid = $this->hubcourseid;
    $version->moodleversion = $CFG->version;
    $version->moodlerelease = $CFG->release;
    $version->description = $this->description;
    $version->userid = $USER->id;
    $version->timeuploaded = time();
    $version->fileid = 0;
    $this->versionid = $DB->insert_record('block_hubcourse_versions', $version);
  }

  private function movefile() {
    $file = $this->results['backup_destination'];
    $this->fs->create_file_from_storedfile([
      'contextid' => $this->hubcoursecontext->id,
      'component' => 'block_hubcourse',
      'filearea' => 'course',
      'itemid' => $this->versionid,
      'filepath' => '/',
      'filename' => $file->get_filename()
    ], $file);
    $file->delete();
  }

  private function updateversion() {
    global $CFG, $DB;
    $version = new stdClass();
    $version->id = $this->versionid;
    $version->moodleversion = $CFG->version;
    $version->moodlerelease = $CFG->release;
    $version->timeuploaded = time();
    $DB->update_record('block_hubcourse_versions', $version);

    $hubcourse = new stdClass();
    $hubcourse->id = $this->hubcourseid;
    $hubcourse->stableversion = $this->versionid;
    $DB->update_record('block_hubcourses', $hubcourse);
  }

  public function execute() {
    if ($this->versionid) {
      $this->deleteoldfile();
    } else {
      $this->createversion();
    }
    $this->backup();
    $this->movefile();
    $this->updateversion();
    block_hubcourseinfo_savembzdependencies($this->courseid, $this->versionid);
  }
}
