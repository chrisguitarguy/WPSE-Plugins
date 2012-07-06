<?php
/*
Plugin Name: Fix Page Templates
Plugin URI: http://wordpress.stackexchange.com/questions/57568/how-to-rename-a-template-file
Description: When an old page template is specified, replace it with a new one
Author: Christopher Davis
Author URI: http://christopherdavis.me
License: GPL2

    Copyright 2012 Christopher Davis

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_filter('page_template', 'wpse57568_page_template');
function wpse57568_page_template($t)
{
    $old_slug = 'pages/t.php'; // replace this
    $new_slug = 'pages/new.php'; // replace this

    $page_id = get_queried_object_id();
    $template = get_post_meta($page_id, '_wp_page_template', true);
    if($template && 'default'!= $template && $old_slug == $template)
    {
        if(file_exists(trailingslashit(STYLESHEETPATH) . $new_slug))
        {
            $t = trailingslashit(STYLESHEETPATH) . $new_slug;
        }
        elseif(file_exists(trailingslashit(TEMPLATEPATH) . $new_slug))
        {
            $t = trailingslashit(TEMPLATEPATH) . $new_slug;
        }
    }
    return $t;
}
