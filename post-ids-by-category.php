<?php
/*
Plugin Name: Get Post IDs by Category
Plugin URI: http://wordpress.stackexchange.com/q/71471/6035
Description: Get all post ids for a given category.
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

function wpse71471_get_post_ids($cat, $taxonomy='category')
{
    return get_posts(array(
        'numberposts'   => -1, // get all posts.
        'tax_query'     => array(
            array(
                'taxonomy'  => $taxonomy,
                'field'     => 'id',
                'terms'     => is_array($cat) ? $cat : array($cat),
            ),
        ),
        'fields'        => 'ids', // only get post IDs.
    ));
}
