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

require_once(dirname(__FILE__) . '/pagesettings.php');

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?> class="no-js">
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>"/>
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google web fonts -->
    <?php require_once(dirname(__FILE__) . '/fonts.php'); ?>
    <!-- iOS Homescreen Icons -->
    <?php require_once(dirname(__FILE__) . '/iosicons.php'); ?>
    <!-- Start Analytics -->
    <?php require_once(dirname(__FILE__) . '/analytics.php'); ?>
    <!-- End Analytics -->
</head>

<body <?php echo $OUTPUT->body_attributes($bodyclasses); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner">
    <nav role="navigation">
        <div class="navbar<?php echo ($oldnavbar) ? ' oldnavbar' : ''; ?>">
            <div class="container-fluid navbar-inner">
                <div class="row-fluid">
                    <div class="custommenus pull-left" style="width: 600px; position: relative;">
                        <a href="/" class="logo"></a>
                        <?php
                         global $cm;
                        if(isset($cm)) {
                        ?>
                        <a href="/mod/rtw/ajax.php?id=<?php echo $cm->id ?>&c=common&a=forum" class="nav_forum colorbox">
                            <img style="height: 30px" src="/theme/essential/pix/forum.png"/>
                            Thảo luận
                        </a>
                        <a href="/mod/rtw/ajax.php?id=<?php echo $cm->id ?>&c=common&a=resources" class="nav_resources colorbox">
                            <img style="height: 30px" src="/theme/essential/pix/docs.png"/>
                            Tài liệu
                        </a>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="pull-right" >
                        <?php
                        if(isset($cm)) {
                        ?>
                        <a href="/mod/rtw/ajax.php?id=<?php echo $cm->id ?>&c=common&a=help" class="click_help colorbox">
                            <img width="42" height="58" src="/theme/essential/pix/thanden.png"/>
                        </a>
                        <?php
                        }
                        ?>
                        <div class="usermenu" style="margin-top: 15px;">
                            <?php echo $OUTPUT->custom_menu_user(); ?>
                        </div>
                        <div class="messagemenu" style="margin-top: 15px;">
                            <?php echo $OUTPUT->custom_menu_messages(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>