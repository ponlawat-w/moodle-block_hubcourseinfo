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
 * Upgrading block
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

/**
 * Upgrade handlers
 * @param int $oldversion
 * @return bool
 * @throws ddl_exception
 * @throws ddl_field_missing_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_block_hubcourseinfo_upgrade($oldversion) {
    global $CFG, $DB;

    $dbmanager = $DB->get_manager();

    if ($oldversion < 2018070500) {

        // ADD TABLE block_hubcourse_subjects
        $table = new xmldb_table('block_hubcourse_subjects');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, '', 'id');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        if ($dbmanager->table_exists($table)) {
            $dbmanager->drop_table($table);
        }
        $dbmanager->create_table($table);
        if (!$dbmanager->table_exists($table)) {
            return false;
        }

        // TABLE block_hubcourses
        $table = new xmldb_table('block_hubcourses');

        // ADD FIELD block_hubcourses.subject
        $field = new xmldb_field('subject', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'stableversion');
        if ($dbmanager->field_exists($table, $field)) {
            $dbmanager->drop_field($table, $field);
        }
        $dbmanager->add_field($table, $field);
        if (!$dbmanager->field_exists($table, $field)) {
            return false;
        }

        // ADD FIELD block_hubcourses.tags
        $field = new xmldb_field('tags', XMLDB_TYPE_CHAR, '500', null, XMLDB_NOTNULL, null, '', 'subject');
        if ($dbmanager->field_exists($table, $field)) {
            $dbmanager->drop_field($table, $field);
        }
        $dbmanager->add_field($table, $field);
        if (!$dbmanager->field_exists($table, $field)) {
            return false;
        }

        upgrade_block_savepoint(true, 2018070500, 'hubcourseinfo');
    }

    if ($oldversion < 2018121204) {
        $table = new xmldb_table('block_hubcourse_versions');
        $field = new xmldb_field('moodleversion', XMLDB_TYPE_FLOAT, '11', false, true, false, 0, 'hubcourseid');
        $dbmanager->change_field_type($table, $field);

        upgrade_block_savepoint(true, 2018121204, 'hubcourseinfo');
    }

    if ($oldversion < 2021041500) {
      $table = new xmldb_table('block_hubcourse_metafields');
      
      if (!$dbmanager->table_exists($table)) {
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, true, true, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '300', null, true, null, null, 'id');
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, true, null, 'text', 'name');
        $table->add_field('required', XMLDB_TYPE_INTEGER, '1', null, true, null, null, 'type');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbmanager->create_table($table);
      }

      $table = new xmldb_table('block_hubcourse_metavalues');

      if (!$dbmanager->table_exists($table)) {
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, true, true, null, null);
        $table->add_field('hubcourseid', XMLDB_TYPE_INTEGER, '11', 'null', true, null, null, 'id');
        $table->add_field('fieldid', XMLDB_TYPE_INTEGER, '11', null, true, null, null, 'fieldid');
        $table->add_field('value', XMLDB_TYPE_TEXT, null, null, true, null, null, 'hubcourseid');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('fieldid_idx', XMLDB_INDEX_NOTUNIQUE, ['fieldid']);
        $table->add_index('hubcourseid_idx', XMLDB_INDEX_NOTUNIQUE, ['hubcourseid']);
        $dbmanager->create_table($table);
      }

      // For merging from branch `maj-extrafields`
      $majextrafields = [
        ['field' => 'leadauthor_roman', 'name' => 'Lead Author Name (Roman)', 'required' => 1],
        ['field' => 'leadauthor_jp', 'name' => 'Lead Author Name (Japanese)', 'required' => 1],
        ['field' => 'leadauthor_email', 'name' => 'Lead Author E-mail', 'required' => 1],
        ['field' => 'leadauthor_aff_roman', 'name' => 'Lead Author Affiliation (Roman)', 'required' => 0],
        ['field' => 'leadauthor_aff_jp', 'name' => 'Lead Author Affiliation (Japanese)', 'required' => 0],
        ['field' => 'coauthor_roman', 'name' => 'Co-Author Name (Roman)', 'required' => 0],
        ['field' => 'coauthor_jp', 'name' => 'Co-Author Name (Japanese)', 'required' => 0],
        ['field' => 'coauthor_email', 'name' => 'Co-Author E-mail', 'required' => 0],
        ['field' => 'coauthor_aff_roman', 'name' => 'Co-Author Affiliation (Roman)', 'required' => 0],
        ['field' => 'coauthor_aff_jp', 'name' => 'Co-Author Affiliation (Japanese)', 'required' => 0],
        ['field' => 'author3', 'name' => 'Third Co-Author', 'required' => 0],
        ['field' => 'author4', 'name' => 'Fourth Co-Author', 'required' => 0],
        ['field' => 'author5', 'name' => 'Fifth Co-Author', 'required' => 0],
        ['field' => 'author_etc', 'name' => 'Other Contributors', 'required' => 0],
        ['field' => 'keywords', 'name' => 'Keywords', 'required' => 0]
      ];
      $table = new xmldb_table('block_hubcourses');
      foreach ($majextrafields as $majfield) {
        if ($dbmanager->field_exists($table, $majfield['field'])) {
          // get data from maj extra field
          $hubcourses = $DB->get_records_sql("SELECT * FROM {block_hubcourses} WHERE {$majfield['field']} != ?", ['']);
          
          $metadatafield = $DB->get_record('block_hubcourse_metafields', ['name' => $majfield['name']]);
          if (!$metadatafield || !$metadatafield->id) {
            $metadatafield = new stdClass();
            $metadatafield->id = 0;
            $metadatafield->name = $majfield['name'];
            $metadatafield->type = 'text';
            $metadatafield->required = $majfield['required'];
            $metadatafield->id = $DB->insert_record('block_hubcourse_metafields', $metadatafield);
            if (!$metadatafield->id) {
              continue;
            }
          }
          
          // if data exists, create metadata field and transfer
          if (count($hubcourses) > 0) {
            // transfer data
            foreach ($hubcourses as $hubcourse) {
              $metadatavalue = new stdClass();
              $metadatavalue->id = 0;
              $metadatavalue->hubcourseid = $hubcourse->id;
              $metadatavalue->fieldid = $metadatafield->id;
              $metadatavalue->value = $hubcourse->{$majfield['field']} ? $hubcourse->{$majfield['field']} : '';
              $DB->insert_record('block_hubcourse_metavalues', $metadatavalue);
            }
          }

          // delete field from table block_hubcourses
          $dbmanager->drop_field($table, new xmldb_field($majfield['field']));
        }
      }

      upgrade_block_savepoint(true, 2021041500, 'hubcourseinfo');
    }

    return true;
}
