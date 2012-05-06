<?php    
/*
Plugin Name: View Any Post Status
Plugin URI: http://wordpress.stackexchange.com/questions/33020/preview-post-custom-post-types-in-archive
Description: Allows administrators to view any post status on the front end of a WordPress Site
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

add_action('pre_get_posts', 'wpse33020_pre_get_posts');
/*
 * Modifies the posts query on the front of the site.  If viewing an archive
 * or page related to a `slider` post type the admin will be able to see posts
 * of any status
 */
function wpse33020_pre_get_posts($query_obj)
{
    // get out of here if this is the admin area
    if(is_admin()) return;
    
    // if this isn't an admin, bail
    if(!current_user_can('manage_options')) return;
    
    // if this isn't your slide post type, bail
    if(!isset($query_obj->query_vars['post_type']) || 
        'slider' != $query_obj->query_vars['post_type']) return;
    
    // change our query object to include any post status
    $query_obj->query_vars['post_status'] = 'any';
}
