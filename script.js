require(['jquery'], function($) {
    $(document).ready(function() {
        var $hubcourse_likesection = $('#block-hubcourseinfo-likesection');
        var hubcourseid = $hubcourse_likesection.attr('hubcourseid');

        var hubcourse_backupsectionstr;

        var hubcourse_liking = false;

        $hubcourse_likesection.on('click', '#block-hubcourseinfo-like-a', function() {

            if (hubcourse_liking) {
                return;
            }

            hubcourse_liking = true;
            hubcourse_backupsectionstr = $hubcourse_likesection.html();
            $hubcourse_likesection.find('#block-hubcourseinfo-like-a')
                .html(M.str.block_hubcourseinfo.loading);

            $.ajax(M.cfg.wwwroot + '/blocks/hubcourseinfo/api/like.php?hubcourseid=' + hubcourseid, {
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $hubcourse_likesection.html(response.html);
                    } else {
                        $hubcourse_likesection.html(hubcourse_backupsectionstr);
                    }
                    hubcourse_liking = false;
                },
                error: function(response) {
                    $hubcourse_likesection.html(hubcourse_backupsectionstr);
                    hubcourse_liking = false;
                }
            });
        });
    });
});