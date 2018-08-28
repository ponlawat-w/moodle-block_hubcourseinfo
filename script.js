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
 * Script for hubcourseinfo block
 *
 * Mainly used for handling like action
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(['jquery'], function ($) {
    $(document).ready(function () {
        var $hubcourse_likesection = $('#block-hubcourseinfo-likesection');
        var hubcourseid = $hubcourse_likesection.attr('hubcourseid');

        var hubcourse_backupsectionstr;

        var hubcourse_liking = false;

        $hubcourse_likesection.on('click', '#block-hubcourseinfo-like-a', function () {

            if (hubcourse_liking) {
                return;
            }

            hubcourse_liking = true;
            hubcourse_backupsectionstr = $hubcourse_likesection.html();
            $hubcourse_likesection.find('#block-hubcourseinfo-like-a')
                .html(M.str.block_hubcourseinfo.loading);

            $.ajax(M.cfg.wwwroot + '/blocks/hubcourseinfo/api/like.php?hubcourseid=' + hubcourseid, {
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        $hubcourse_likesection.html(response.html);
                    } else {
                        $hubcourse_likesection.html(hubcourse_backupsectionstr);
                    }
                    hubcourse_liking = false;
                },
                error: function (response) {
                    $hubcourse_likesection.html(hubcourse_backupsectionstr);
                    hubcourse_liking = false;
                }
            });
        });
    });
});