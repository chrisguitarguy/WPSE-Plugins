<?php
/*
Plugin Name: Page behind Login
Plugin URI: http://wordpress.stackexchange.com/q/64899/6035
Description: Hide a page from users who aren't logged in.
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

add_action('template_redirect','wpse64899_check_if_logged_in');
/**
 * Hooked into `template_redirect`.  Checks to see if the user is logged in
 * and whether or not we're on the appropriate page.  Non-logged-in users on
 * the page will get sent to wp-login with an appropriate `redirect_to` param.
 *
 * @uses    add_query_arg
 * @uses    is_user_logged_in
 * @uses    is_page
 * @uses    site_url
 * @uses    wp_redirect
 * @return  null
 */
function wpse64899_check_if_logged_in()
{
    $pageid = 2; // or whatever you want it to be
    if(!is_user_logged_in() && is_page($pageid))
    {
        $url = add_query_arg(
            'redirect_to',
            get_permalink($pagid),
            site_url('wp-login.php')
        );
        wp_redirect($url);
        exit;
    }
}
