<?php    
/*
Plugin Name: Registration Redirect
Plugin URI: http://wordpress.stackexchange.com/q/45134/6035
Description: Don't allow users to view the default registration page
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

add_action('login_form_register', 'wpse45134_catch_register');
/**
 * Redirects visitors to `wp-login.php?action=register` to 
 * `site.com/register`
 */
function wpse45134_catch_register()
{
    wp_redirect(home_url('/register'));
    exit(); // always call `exit()` after `wp_redirect`
}


add_action('login_form_lostpassword', 'wpse45134_filter_option');
add_action('login_form_retrievepassword', 'wpse45134_filter_option');
/**
 * Simple wrapper around a call to add_filter to make sure we only
 * filter an option on the login page.
 */
function wpse45134_filter_option()
{
    // use __return_zero because pre_option_{$opt} checks
    // against `false`
    add_filter('pre_option_users_can_register', '__return_zero');
}
