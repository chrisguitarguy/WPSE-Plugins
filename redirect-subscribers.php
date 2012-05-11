<?php    
/*
Plugin Name: Redirect Subscribers
Plugin URI: http://wordpress.stackexchange.com/q/51831/6035
Description: Redirects all `subscriber level users to the home page
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

add_action('admin_init', 'wpse51831_init');
/*
 * Don't allow users with the `subscriber` role to view the admin area
 *
 * @uses current_user_can
 * @uses wp_redirect
 * @uses home_url
 */
function wpse51831_init()
{
    if(!current_user_can('edit_posts'))
    {
        wp_redirect(home_url());
        exit;
    }
}

add_filter('show_admin_bar', 'wpse51831_hide_admin_bar');
/*
 * hide the admin bar for `subscribers`
 *
 * @uses current_user_can
 * @return bool Whether or not to hide the admin bar
 */
function wpse51831_hide_admin_bar($bool)
{
    if(!current_user_can('edit_posts'))
    {
        $bool = false;
    }
    return $bool;
}
