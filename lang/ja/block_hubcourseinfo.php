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
 * Japanese language strings
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'ハブコース情報';

$string['hubcourseinfo:addinstance'] = 'ブロックを追加';
$string['hubcourseinfo:managecourse'] = 'コースの情報編集とバージョン管理';
$string['hubcourseinfo:managesubjects'] = 'コースのサブジェクトを管理する';
$string['hubcourseinfo:viewlikes'] = '「いいね」を見る';
$string['hubcourseinfo:viewreviews'] = 'レビューを読む';
$string['hubcourseinfo:submitlike'] = '「いいね」をあげる';
$string['hubcourseinfo:submitreview'] = 'レヴューする';
$string['hubcourseinfo:downloadcourse'] = 'コースをダウンロードする';
$string['hubcourseinfo:importfrommajhub'] = 'MAJハブコースからデータをインポート';
$string['hubcourseinfo:truncate'] = 'ハブコースデータを削除';
$string['hubcourseinfo:managemetadatafields'] = 'Manage Metadata Fields';
$string['hubcourseinfo:deletehubcourse'] = 'Delete Hubcourse';
$string['hubcourseinfo:exportmetadataall'] = 'Export Metadata of all courses';

$string['settings:autocreateinfoblock'] = '自動作成';
$string['settings:autocreateinfoblock_decription'] = 'コースをアップロードされた後、情報ブロックを自動的に作成';
$string['settings:maxversionamount'] = '最大バージョン数';
$string['settings:maxversionamount_description'] = '一コースの最大バージョンの数';
$string['settings:subjects'] = 'サブジェクトを管理する';
$string['settings:metadatafields'] = 'Manage Metadata Fields';

$string['managesubjectslink'] = 'サブジェクトを管理するのに、ここに押してください。';
$string['managesubjects'] = 'コースサブジェクト管理';
$string['coursesubjects'] = 'コースサブジェクト';
$string['coursesubject'] = 'コースサブジェクト';
$string['newsubject'] = '追加';
$string['subjectname'] = 'サブジェクト名';
$string['editsubject'] = 'サブジェクト編集: {$a}';
$string['deletesubjectconfirm'] = 'サブジェクトを削除しますか。';
$string['truncateconfirm'] = 'ハブコースのデータを削除しますか。';
$string['majimportconfirm'] = 'MAJハブからインポート';

$string['managemetadatafields'] = 'Manage Metadata Fields';
$string['managemetadatafieldslink'] = 'Click here to manage metadata fields';
$string['metadatafields'] = 'Metadata Fields';
$string['newfield'] = 'Add New Field';
$string['metadatafieldname'] = 'Field name';
$string['deletemetadatafieldconfirm'] = 'Delete Metadata Field Confirmation';
$string['deletemetadatafieldconfirm_title'] = 'Delete metadata field confirmation: {$a}';
$string['deletemetadatafieldconfirm_description'] = 'Are you sure you want to delete this metadata field? All the values of this field will be loss.';
$string['editmetadatafield'] = 'Edit metadata field: {$a}';

$string['deletesubjectconfirm_title'] = '削除されるサブジェクト: {$a}';
$string['deletesubjectconfirm_description'] = 'このサブジェクトを削除しますか。削除されたサブジェクトは戻すことができません。このサブジェクトにあるコースはすべて「無サブジェクト」に移動されます。';
$string['truncateconfirm_title'] = 'データを削除されるハブコース：{$a}';
$string['truncateconfirm_description'] = 'このハブコースを削除しますか。ただし、削除されるのはハブコースのデータのみで、コースの存在は削除されません。';
$string['majimportconfirm_title'] = '「{$a}」コースにMAJハブのデータからインポートしますか？';
$string['majimportconfirm_description'] = 'MAJハブからデータをインポートしますか？';

$string['subject'] = 'サブジェクト';
$string['tags'] = 'タグ';
$string['tags_help'] = 'タグを分けるために、「,」記号を使ってください。';

$string['courseowner'] = 'アップロード者';
$string['blocktitle'] = 'コース情報';
$string['managecourse'] = 'コース情報を編集';
$string['downloadcourse'] = 'ダウンロード';

$string['stableversion'] = '最新バージョン';
$string['demourl'] = '元コースのＵＲＬ';
$string['timecreated'] = 'アップロード日時';
$string['timemodified'] = '更新日時';
$string['averagerating'] = '平均評価';

$string['moodleversion'] = 'ムードルのバージョン';
$string['moodleversion_version'] = 'バージョン：';
$string['moodleversion_release'] = 'リリース：';
$string['dependencies'] = '使用するプラグイン';

$string['likes'] = 'いいね';
$string['nolike'] = 'まだ「いいね」がありません。';
$string['nolike_guest'] = '「いいね」するためには、ログインしてください。';
$string['likeamount_singular'] = '{$a}人が「いいね」と言っています。';
$string['likeamount_plural'] = '{$a}人が「いいね」と言っています。';
$string['like'] = 'いいね';
$string['unlike'] = '「いいね」を削除';
$string['reviews'] = 'レビュー';
$string['noreview'] = 'まだレビューがありません。';
$string['noreview_guest'] = 'レビューを書くために、ログインしてください。';
$string['writereview'] = 'レビューを書く';
$string['editreview'] = 'レビューを編集';
$string['editmyreview'] = 'レビューを編集';
$string['readmorereview'] = 'もっと見る';
$string['downloadotherversions'] = '他のバージョンをダウンロード';
$string['download_guest'] = 'このコースをダウンロードするために、ログインしてください。';

$string['versions'] = 'バージョン';

$string['loading'] = 'しばらくお待ちください。';

$string['notknow'] = 'N/A';
$string['hubcoursenotfound'] = '問われるコースがハブに存在しません。';

$string['ratethiscourse'] = 'コースを評価する';
$string['pleaserate'] = '選択してください。';
$string['comment'] = 'コメント';
$string['submitreview'] = '提出';

$string['managehubcourse'] = 'ハブコースを編集';
$string['metadata'] = 'コース情報';
$string['editmetadata'] = '情報を編集する';
$string['exportmetadata'] = 'Export metadata';
$string['exportmetadataall'] = 'Export metadata of all courses';
$string['manageversion'] = 'バージョンを管理する';
$string['deletehubcourse'] = 'コースを削除する';
$string['siteadmin'] = 'サイト管理者';

$string['editmetadatanewcourse'] = 'あなたのコースがこのサイトにインストールされました。下記のフォームにコースの情報を入れてください。';

$string['timeuploaded'] = '作成日時';
$string['downloads'] = 'ダウンロード数';
$string['addversion'] = 'バージョンを追加';
$string['editversion'] = '編集';
$string['current'] = '現在';
$string['reset'] = 'コンテンツをリセットする';
$string['reset_description'] = 'Reset course content to the one in the version';
$string['apply'] = 'このバージョンを適用する';
$string['apply_description'] = 'Apply the version to the course content';
$string['rebuild'] = 'Rebuild';
$string['rebuild_description'] = 'Replace the version archive with the current content';
$string['rebuildasnewversion'] = 'Rebuild as a new version';
$string['rebuildasnewversion_description'] = 'Build a new version from the current content';

$string['rebuildconfirm_title'] = 'Version Rebuild Confirmation';
$string['rebuildconfirm_description'] = 'Are you sure you want to rebuild and replace the version backup file from the current course content?';
$string['rebuildnewversionconfirm_description'] = 'Are you sure you want to build a new version from the current course content?';

$string['coursefile'] = 'コースのファイル';
$string['maxfilesize'] = '最大サイズ: {$a}MB';

$string['editdelete'] = '編集・削除';
$string['deleteversion'] = 'このバージョンを削除';
$string['maxversionamountexceed'] = '最大バージョン数の{$a}になっていますので、新しいバージョンを追加できません。';
$string['cannotdeletecurrentversion'] = 'コースの現在バージョンを削除できません。';

$string['save'] = '保存';

$string['deleteconfirm_title'] = '<span class="text-danger">削除の確認</span>';
$string['deleteconfirm_description'] = 'このコースを削除しますか。<br>コースに関わるファイル、バージョン、いいねデータやレビューなどが全て削除されます。';
$string['hubcoursedeleted'] = 'コースが削除されました。';

$string['deleteversionconfirm_title'] = '<span class="text-danger">バージョン削除の確認</span>';
$string['deleteversionconfirm_description'] = 'このバージョンを削除しますか。<br>バージョンのコースファイルとダウンロード数のデータなどがすべて削除されます。';
$string['versiondeleted'] = 'バージョンが削除されました。';

$string['importfrommajhub'] = 'MAJハブからデータをインポートする';
$string['clearhubcoursedata'] = 'ハブコースのデータを削除';

$string['reviewerr_pleaserate'] = '1 から 5 までを評価してください。';
$string['reviewerr_pleasecomment'] = 'コメントを入力してください。';

$string['err_cannotsubmit'] = 'データを送信できません。';

$string['error_maxversionamountexceed'] = '最大バージョン数になっています。これ以上に追加することができません。';
$string['error_cannotreadfile'] = 'ファイルを読み込めません。';
$string['error_notcoursebackupfile'] = 'このファイルはコースバックアップファイルではありません。';
$string['error_cannotdeletestableversion'] = '現在バージョンを削除できません。';
$string['error_nomajhub'] = 'このコースはMAJハブに存在しません。';
