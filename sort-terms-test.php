<?php
/*
Plugin Name: Sort Terms Test
Plugin URI: http://wordpress.stackexchange.com/q/72703/6035
Description: See if we can order object terms.
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

!defined('ABSPATH') && exit;

add_action('init', 'wpse72703_modify_tags', 100);
function wpse72703_modify_tags()
{
    global $wp_taxonomies;
    $wp_taxonomies['post_tag']->sort = true;
}

// uncomment the next line to see term order in action.
//add_action('template_redirect', 'wpse72703_check_order');
function wpse72703_check_order()
{
    if(!is_singular('post'))
        return;

    $post = get_queried_object();
    $terms = wp_get_object_terms($post->ID, 'post_tag', array(
        'orderby' => 'term_order',
    ));

    echo '<pre>';
    var_dump($terms);
    echo '</pre>';
    die;
}
