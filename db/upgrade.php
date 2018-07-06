<?php
defined('MOODLE_INTERNAL') or die();

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

    return true;
}