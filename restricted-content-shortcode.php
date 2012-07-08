<?php
/*
Plugin Name: Restricted Content Shortcode
Plugin URI: http://wordpress.stackexchange.com/q/57819/6035
Description: Adds a shortcode that hides content from non-logged in users
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

add_action('init', 'wpse57819_add_shortcode');
/**
 * Adds the shortcode
 *
 * @uses add_shortcode
 * @return null
 */
function wpse57819_add_shortcode()
{
    add_shortcode('restricted', 'wpse57819_shortcode_cb');
}


/**
 * Callback function for the shortcode.  Checks if a user is logged in.  If they
 * are, display the content.  If not, show them a link to the login form.
 *
 * @return string
 */
function wpse57819_shortcode_cb($args, $content=null)
{
    // if the user is logged in just show them the content.  You could check
    // rolls and capabilities here if you wanted as well
    if(is_user_logged_in())
        return $content;

    // If we're here, they aren't logged in, show them a message
    $defaults = array(
        // message show to non-logged in users
        'msg'    => __('You must login to see this content.', 'wpse57819'),
        // Login page link
        'link'   => site_url('wp-login.php'),
        // login link anchor text
        'anchor' => __('Login.', 'wpse57819')
    );
    $args = wp_parse_args($args, $defaults);

    $msg = sprintf(
        '<aside class="login-warning">%s <a href="%s">%s</a></aside>',
        esc_html($args['msg']),
        esc_url($args['link']),
        esc_html($args['anchor'])
    );

    return $msg;
}
