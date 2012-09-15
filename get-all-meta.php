<?php
/*
Plugin Name: Get All Meta
Plugin URI: http://wordpress.stackexchange.com/q/65225/6035
Description: Get all meta key value pairs for a given post type.
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

add_action('template_redirect', 'wpse65225_test');
function wpse65225_test()
{
    echo '<pre>';
    var_dump(wpse65225_get_all_meta('post'));
    echo '</pre>';
}

function wpse65225_get_all_meta($type)
{
    global $wpdb;

    $res = $wpdb->get_results($wpdb->prepare(
        "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id IN
        (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $type
    ), ARRAY_A);

    return $res;
}
