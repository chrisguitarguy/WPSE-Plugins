<?php
/*
Plugin Name: Order Category Pages by Author
Plugin URI: http://wordpress.stackexchange.com/q/56168/6035
Description: Change the order of posts on category pages to be by author.
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

add_action('pre_get_posts', 'wpse56168_order_author');
/**
 * Change the order of posts only on the category pages.
 *
 * @param   WP_Query $q The current WP_Query object
 * @author  Christopher Davis <http://christopherdavis.me>
 * @return  void
 */
function wpse56168_order_author($q)
{
    if($q->is_main_query() && $q->is_category())
    {
        $q->set('orderby', 'author');
        $q->set('order', 'ASC'); // alphabetical, ascending
    }
}

/**
 * Extract the authors from a WP_Query object.
 *
 * @param   WP_Query $q
 * @return  array An array of WP_User objects.
 */
function wpse56168_extract_authors(WP_Query $q)
{
    // this is PHP 5.3+, you'll have to use a named function with PHP < 5.3
    $authors = array_map(function($p) {
        return isset($p->post_author) ? $p->post_author : 0;
    }, $q->posts);

    return get_users(array(
        'include'   => array_unique($authors),
    ));
}
