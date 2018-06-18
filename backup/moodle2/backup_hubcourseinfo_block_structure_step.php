<?php

class backup_hubcourseinfo_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {

        $hubcourses = new backup_nested_element('block_hubcourses', array('id'), array(
            'instanceid', 'contextid', 'courseid', 'userid', 'stableversion', 'demourl', 'description', 'timecreated', 'timemodified'
        ));

        $versions = new backup_nested_element('block_hubcourse_versions', array('id'), array(
            'hubcourseid', 'moodleversion', 'description', 'userid', 'timeuploaded', 'fileid'
        ));

        $dependencies = new backup_nested_element('block_hubcourse_dependencies', array('id'), array(
            'versionid', 'requiredpluginname', 'requiredpluginversion'
        ));

        $downloads = new backup_nested_element('block_hubcourse_downloads', array('id'), array(
            'versionid', 'userid', 'timedownloaded'
        ));

        $likes = new backup_nested_element('block_hubcourse_likes', array('id'), array(
            'hubcourseid', 'userid', 'timecreated'
        ));

        $reviews = new backup_nested_element('block_hubcourse_reviews', array('id'), array(
            'hubcourseid', 'versionid', 'userid', 'rate', 'comment', 'commentformat', 'timecreated'
        ));

        $hubcourses->add_child($versions);
        $hubcourses->add_child($likes);
        $hubcourses->add_child($reviews);

        $versions->add_child($dependencies);
        $versions->add_child($downloads);

        $hubcourses->set_source_table('block_hubcourses', ['instanceid' => backup::VAR_BLOCKID]);

        $versions->set_source_table('block_hubcourse_versions', ['hubcourseid' => backup::VAR_PARENTID]);
        $likes->set_source_table('block_hubcourse_likes', ['hubcourseid' => backup::VAR_PARENTID]);
        $reviews->set_source_table('block_hubcourse_reviews', ['hubcourseid' => backup::VAR_PARENTID]);

        $dependencies->set_source_table('block_hubcourse_dependencies', ['versionid' => backup::VAR_PARENTID]);
        $downloads->set_source_table('block_hubcourse_downloads', ['versionid' => backup::VAR_PARENTID]);

        $hubcourses->annotate_ids('user', 'userid');
        $versions->annotate_ids('user', 'userid');
        $downloads->annotate_ids('user', 'userid');
        $likes->annotate_ids('user', 'userid');
        $reviews->annotate_ids('user', 'userid');
        $reviews->annotate_ids('block_hubcourse_versions', 'versionid');

        $versions->annotate_files('block_hubcourse', 'course', 'id');

//        $versions->set_source_sql('SELECT * FROM {block_hubcourse_versions} WHERE hubcourseid = (SELECT id FROM {block_hubcourses} WHERE instanceid = ?)', [backup::VAR_BLOCKID]);
//        $likes->set_source_sql('SELECT * FROM {block_hubcourse_likes} WHERE hubcourseid = (SELECT id FROM {block_hubcourses} WHERE instanceid = ?)', [backup::VAR_BLOCKID]);
//        $reviews->set_source_sql('SELECT * FROM {block_hubcourse_reviews} WHERE hubcourseid = (SELECT id FROM {block_hubcourses} WHERE instanceid = ?)', [backup::VAR_BLOCKID]);
//
//        $dependencies->set_source_sql('SELECT * FROM {block_hubcourse_dependencies} WHERE versionid IN (SELECT id FROM {block_hubcourse_versions} WHERE hubcourseid = (SELECT id FROM {block_hubcourses} WHERE instanceid = ?))', [backup::VAR_BLOCKID]);
//        $downloads->set_source_sql('SELECT * FROM {block_hubcourse_downloads} WHERE versionid IN (SELECT id FROM {block_hubcourse_versions} WHERE hubcourseid = (SELECT id FROM {block_hubcourses} WHERE instanceid = ?))', [backup::VAR_BLOCKID]);

        return $this->prepare_block_structure($hubcourses);
    }
}