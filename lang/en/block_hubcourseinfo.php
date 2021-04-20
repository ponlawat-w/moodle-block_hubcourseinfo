<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English language strings
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Hub Course Info';

$string['hubcourseinfo:addinstance'] = 'Add block instance';
$string['hubcourseinfo:managecourse'] = 'Manage course meta data and versions';
$string['hubcourseinfo:managesubjects'] = 'Manage course subjects';
$string['hubcourseinfo:viewlikes'] = 'View likes';
$string['hubcourseinfo:viewreviews'] = 'View reviews';
$string['hubcourseinfo:submitlike'] = 'Like a course';
$string['hubcourseinfo:submitreview'] = 'Review course';
$string['hubcourseinfo:downloadcourse'] = 'Download course file';
$string['hubcourseinfo:importfrommajhub'] = 'Import data from MAJ hub';
$string['hubcourseinfo:truncate'] = 'Truncate hubcourse data (preserve course existence)';
$string['hubcourseinfo:managemetadatafields'] = 'Manage Metadata Fields';
$string['hubcourseinfo:deletehubcourse'] = 'Delete Hubcourse';

$string['settings:autocreateinfoblock'] = 'Create an instance when course is uploaded';
$string['settings:autocreateinfoblock_decription'] = 'Automatically create an instance when new course is uploaded.';
$string['settings:maxversionamount'] = 'Maximum version amount';
$string['settings:maxversionamount_description'] = 'Maximum number of version amount in one course';
$string['settings:subjects'] = 'Manage subjects';
$string['settings:metadatafields'] = 'Manage Metadata Fields';

$string['managesubjectslink'] = 'Click here to manage course subjects in this site';
$string['managesubjects'] = 'Manage course subjects';
$string['coursesubjects'] = 'Course subjects';
$string['coursesubject'] = 'Course subject';
$string['newsubject'] = 'Add new subject';
$string['subjectname'] = 'Subject name';
$string['editsubject'] = 'Edit course subject: {$a}';
$string['deletesubjectconfirm'] = 'Delete course subject confirmation';
$string['truncateconfirm'] = 'Truncate hubcourse data confirmation';
$string['majimportconfirm'] = 'MAJ Hub import confirmation';

$string['managemetadatafields'] = 'Manage Metadata Fields';
$string['managemetadatafieldslink'] = 'Click here to manage metadata fields';
$string['metadatafields'] = 'Metadata Fields';
$string['newfield'] = 'Add New Field';
$string['metadatafieldname'] = 'Field name';
$string['deletemetadatafieldconfirm'] = 'Delete Metadata Field Confirmation';
$string['deletemetadatafieldconfirm_title'] = 'Delete metadata field confirmation: {$a}';
$string['deletemetadatafieldconfirm_description'] = 'Are you sure you want to delete this metadata field? All the values of this field will be loss.';
$string['editmetadatafield'] = 'Edit metadata field: {$a}';

$string['deletesubjectconfirm_title'] = 'Course subject delete confirmation: {$a}';
$string['deletesubjectconfirm_description'] = 'Are you sure you want to delete this course subject? The action is cannot be reverted, and all courses in the subject will be moved to non-subject course.';
$string['truncateconfirm_title'] = 'Hubcourse data truncate confirmation: {$a}';
$string['truncateconfirm_description'] = 'Are you sure you want to delete this hubcourse data? This action will preserve course existence.';
$string['majimportconfirm_title'] = 'MAJ Hub import confirmation: {$a}';
$string['majimportconfirm_description'] = 'Are you sure you want to import all data from MAJ hub to this hubcourse?';

$string['subject'] = 'Subject';
$string['tags'] = 'Tags';
$string['tags_help'] = 'Use comma (,) to separate each tag';

$string['courseowner'] = 'Uploaded by';
$string['blocktitle'] = 'Course Information';
$string['managecourse'] = 'Edit Course Data';
$string['downloadcourse'] = 'Download Course';

$string['stableversion'] = 'Latest Version';
$string['demourl'] = 'Course Demo Site';
$string['timecreated'] = 'Uploaded on';
$string['timemodified'] = 'Time Modified';
$string['averagerating'] = 'Average Ratings';

$string['moodleversion'] = 'Moodle Version';
$string['moodleversion_version'] = 'Version: ';
$string['moodleversion_release'] = 'Release: ';
$string['dependencies'] = 'Custom Plugins Required';

$string['likes'] = 'Likes';
$string['nolike'] = 'Be the first to like this course.';
$string['nolike_guest'] = 'Please sign in to like this course.';
$string['likeamount_singular'] = '{$a} person likes this course.';
$string['likeamount_plural'] = '{$a} people like this course.';
$string['like'] = 'Like this course';
$string['unlike'] = 'Unlike this course';
$string['reviews'] = 'Reviews';
$string['noreview'] = 'Be the first to review this course.';
$string['noreview_guest'] = 'Please sign in to rate and review this course.';
$string['writereview'] = 'Write a review';
$string['editreview'] = 'Edit review';
$string['editmyreview'] = 'Edit my review';
$string['readmorereview'] = 'Read more reviews';
$string['downloadotherversions'] = 'Download other versions';
$string['download_guest'] = 'Please sign in to download this course.';

$string['versions'] = 'Versions';

$string['loading'] = 'Loading…';

$string['notknow'] = 'N/A';
$string['hubcoursenotfound'] = 'Requested course is not found in hub.';

$string['ratethiscourse'] = 'Rate This Course';
$string['pleaserate'] = 'Please select';
$string['comment'] = 'Comment';
$string['submitreview'] = 'Submit Review';

$string['managehubcourse'] = 'Manage Hub Course';
$string['metadata'] = 'Metadata';
$string['editmetadata'] = 'Edit metadata';
$string['manageversion'] = 'Manage Versions';
$string['deletehubcourse'] = 'Delete this course';
$string['siteadmin'] = 'Site Administrator';

$string['editmetadatanewcourse'] = '<strong>Congratulations!</strong> Your course has been installed to this site. Please fill in form below to provide more information about your course.';

$string['timeuploaded'] = 'Time Uploaded';
$string['downloads'] = 'Downloads';
$string['addversion'] = 'Upload a new version';
$string['editversion'] = 'Edit Course Version';
$string['current'] = 'Current';
$string['reset'] = 'Reset';
$string['reset_description'] = 'Reset course content to the one in the version';
$string['apply'] = 'Apply';
$string['apply_description'] = 'Apply the version to the course content';
$string['rebuild'] = 'Rebuild';
$string['rebuild_description'] = 'Replace the version archive with the current content';
$string['rebuildasnewversion'] = 'Rebuild as a new version';
$string['rebuildasnewversion_description'] = 'Build a new version from the current content';

$string['rebuildconfirm_title'] = 'Version Rebuild Confirmation';
$string['rebuildconfirm_description'] = 'Are you sure you want to rebuild and replace the version backup file from the current course content?';
$string['rebuildnewversionconfirm_description'] = 'Are you sure you want to build a new version from the current course content?';

$string['coursefile'] = 'Course File';
$string['maxfilesize'] = 'Maximum file size: {$a}MB';

$string['editdelete'] = 'Edit / Delete';
$string['deleteversion'] = 'Delete this version';
$string['maxversionamountexceed'] = 'You have reached the maximum number of version at {$a}.';
$string['cannotdeletecurrentversion'] = 'You cannot delete current version of the course.';

$string['save'] = 'Save';

$string['deleteconfirm_title'] = '<span class="text-danger">Delete Confirmation</span>';
$string['deleteconfirm_description'] = 'Are you sure you want to delete this course?<br>Your course files, versions, likes and reviews data will be permanently deleted and cannot be reverted.';
$string['hubcoursedeleted'] = 'Your course has been deleted.';

$string['deleteversionconfirm_title'] = '<span class="text-danger">Version Delete Confirmation</span>';
$string['deleteversionconfirm_description'] = 'Are tou sure you want to delete this version?<br>Your course file and download data of this version will be permanently deleted and cannot be reverted.';
$string['versiondeleted'] = 'Your version has been deleted.';

$string['importfrommajhub'] = 'Import data from MAJ hub';
$string['clearhubcoursedata'] = 'Truncate hubcourse data';

$string['reviewerr_pleaserate'] = 'Please rate from 1 to 5';
$string['reviewerr_pleasecomment'] = 'Please write some comment';

$string['err_cannotsubmit'] = 'Cannot submit data, please contact administrator';

$string['error_maxversionamountexceed'] = 'You have reached the maximum number of version amount and cannot add more version.';
$string['error_cannotreadfile'] = 'Unable to read file. This might be caused from corrupted file.';
$string['error_notcoursebackupfile'] = 'Unable to upload file because it is not a course backup file.';
$string['error_cannotdeletestableversion'] = 'Unable to delete current version';
$string['error_nomajhub'] = 'This course is not in MAJ hub courseware.';
