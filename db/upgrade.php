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

    if ($oldversion < 2021020600) {
        $table = new xmldb_table('block_hubcourses');
        $fields = [
            new xmldb_field('leadauthor_roman', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'description'),
            new xmldb_field('leadauthor_jp', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'leadauthor_roman'),
            new xmldb_field('leadauthor_email', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'leadauthor_jp'),
            new xmldb_field('leadauthor_aff_roman', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'leadauthor_email'),
            new xmldb_field('leadauthor_aff_jp', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'leadauthor_aff_roman'),
            new xmldb_field('coauthor_roman', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'leadauthor_aff_jp'),
            new xmldb_field('coauthor_jp', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'coauthor_roman'),
            new xmldb_field('coauthor_email', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'coauthor_jp'),
            new xmldb_field('coauthor_aff_roman', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'coauthor_email'),
            new xmldb_field('coauthor_aff_jp', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'coauthor_aff_roman'),
            new xmldb_field('author3', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'coauthor_aff_jp'),
            new xmldb_field('author4', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'author3'),
            new xmldb_field('author5', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'author4'),
            new xmldb_field('author_etc', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'author5'),
            new xmldb_field('keywords', XMLDB_TYPE_CHAR, '100', false, XMLDB_NOTNULL, false, '', 'author_etc')
        ];

        foreach ($fields as $field) {
            if (!$dbmanager->field_exists($table, $field)) {
                $dbmanager->add_field($table, $field);
            }
        }

        upgrade_block_savepoint(true, 2021020600, 'hubcourseinfo');
    }

    return true;
}
