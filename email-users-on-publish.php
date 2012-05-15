<?php    
/*
Plugin Name: Email Users on Publish
Plugin URI: http://wordpress.stackexchange.com/q/52135/6035
Description: Email users (from a custom field) when a post is published
Author: Christopher Davis
Author URI: http://christopherdavis.me
Text Domain: wpse52135
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

// replace 'publish_post' with 'publish_your_post_type'
add_action('publish_post', 'wpse52135_transition', 10, 2);
/*
 * When a post moves from 'draft' to 'publish, send an email
 *
 * @uses get_post_meta
 * @uses update_post_mtea
 * @uses wp_mail
 */
function wpse52135_transition($post_id, $post)
{
    // store the fact that we sent an email in a custom field if that
    // field is present, don't resend
    if(get_post_meta($post_id, '_wpse52135_sent_mail', true))
        return;

    $email = get_post_meta($post_id, 'wpse52135_email', true);

    // No email?  bail.
    if(!$email || !is_email($email)) return;

    // email subject
    $subject = sprintf(
        __('New Post: %s', 'wpse52135'),
        esc_attr(strip_tags($post->post_title))
    );

    // email body
    $msg = sprintf(
        __("Check out our new post: %s\n\n%s", 'wpse52135'),
        esc_attr(strip_tags($post->post_title)),
        get_permalink($post)
    );

    // send the email
    wp_mail(
        $email,
        $subject,
        $msg
    );

    update_post_meta($post_id, '_wpse52135_sent_mail', true);
}
