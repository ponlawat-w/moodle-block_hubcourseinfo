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
 * Block admin settings page
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_configcheckbox(
            'block_hubcourseinfo/autocreateinfoblock',
            get_string('settings:autocreateinfoblock', 'block_hubcourseinfo'),
            get_string('settings:autocreateinfoblock_decription', 'block_hubcourseinfo'),
            true, true, false
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'block_hubcourseinfo/maxversionamount',
            get_string('settings:maxversionamount', 'block_hubcourseinfo'),
            get_string('settings:maxversionamount_description', 'block_hubcourseinfo'),
            '3', PARAM_INT)
    );

    $settings->add(
        new admin_setting_configempty(
            'block_hubcourseinfo/subjects',
            get_string('settings:subjects', 'block_hubcourseinfo'),
            html_writer::link(
                new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'),
                html_writer::tag('i', '', ['class' => 'fa fa-pencil']) . ' ' . get_string('managesubjectslink', 'block_hubcourseinfo'),
                ['class' => 'text-primary', 'target' => '_blank', 'style' => 'margin-left: 15px;'])
        )
    );

    $settings->add(
      new admin_setting_configempty(
        'block_hubcourseinfo/metadatafields',
        get_string('settings:metadatafields', 'block_hubcourseinfo'),
        html_writer::link(
          new moodle_url('/blocks/hubcourseinfo/admin/metadatafields.php'),
          html_writer::tag('i', '', ['class' => 'fa fa-pencil']) . ' ' . get_string('managemetadatafieldslink', 'block_hubcourseinfo'),
          ['class' => 'text-primary', 'target' => '_blank', 'style' => 'margin-left: 15px;'])
      )
    );

  $settings->add(
    new admin_setting_configempty(
      'block_hubcourseinfo/exportmetadataall',
      get_string('exportmetadataall', 'block_hubcourseinfo'),
      html_writer::link(
        new moodle_url('/blocks/hubcourseinfo/metadata/export.php'),
        html_writer::tag('i', '', ['class' => 'fa fa-upload']) . ' ' . get_string('exportmetadataall', 'block_hubcourseinfo'),
        ['class' => 'btn btn-success', 'style' => 'margin-left: 15px']
      )
    )
  );
}
