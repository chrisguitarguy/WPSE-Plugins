<?php    
/*
Plugin Name: One Post Home
Plugin URI: http://wordpress.stackexchange.com/questions/41420/showing-latest-post-without-301-redirect
Description: Only display the latest post on your WordPress homepage
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

add_filter('parse_query', 'wpse41420_one_post');
/**
 * Modifies the $wp_query object when in parses the query variables
 * this avoids having to use query_posts and modifies things before they
 * get sent to the DB.
 * 
 * @uses is_main_query to make sure we screw up any other wp_query objects
 * @uses is_home to see if this is the main blog page
 */
function wpse41420_one_post($query)
{
    // make sure we're modifying the main query on the home page
    if(!$query->is_main_query() || !is_home()) return;
    
    // Set up one post per page
    $query->query_vars['posts_per_page'] = 1;
    
    // ignore stick posts
    $query->query_vars['ignore_sticky_posts'] = 1;
}
