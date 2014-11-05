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
 * This is built using the bootstrapbase template to allow for new theme's using
 * Moodle's new Bootstrap theme engine
 *
 * @package     theme_essential
 * @copyright   2013 Julian Ridden
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


?>
<img src="/mod/rtw/pix/footerbgr.png" style="width: 99%; position: fixed; bottom: 0px; left: 0px; z-index: -1;"/>
<div id="rtw-footer">©2014 smartfuneducation.com</div>
<script type="text/javascript">
        function ajaxLoadingBegin() {
            jQuery("body").addClass("loading");
        }
        jQuery(document).on({
            //ajaxStart: function() { jQuery("body").addClass("loading");    },
            ajaxStop: function() { jQuery("body").removeClass("loading"); }    
        });
        jQuery(document).ready(function () {
            var offset = 220;
            var duration = 500;
            jQuery(window).scroll(function () {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery('.back-to-top').fadeIn(duration);
                } else {
                    jQuery('.back-to-top').fadeOut(duration);
                }
            });

            jQuery('.back-to-top').click(function (event) {
                event.preventDefault();
                jQuery('html, body').animate({scrollTop: 0}, duration);
                return false;
            });
            //jQuery("a.colorbox").colorbox();
            /*jQuery('.navbar').affix({
                offset: {
                    top: $('header').height()
                }
            });*/
            //$('.breadcrumb').jBreadCrumb();
            //$('body').fitVids();
        });
    </script>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
<div class="blockui"><!-- Place at bottom of page --></div>