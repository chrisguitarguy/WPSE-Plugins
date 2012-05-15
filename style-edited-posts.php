<?php    
/*
Plugin Name: Style Edited Posts
Plugin URI: http://wordpress.stackexchange.com/q/52122/6035
Description: Adds a class to a post based on a meta box value
Text Domain: wpse52122
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

add_action('add_meta_boxes_post', 'wpse52122_meta_box');
/*
 * Adds a meta box on the post editing screen.
 *
 * @uses add_meta_box
 */
function wpse52122_meta_box()
{
    add_meta_box(
        'wpse52122-box',
        __('Add Custom Style', 'wpse52122'),
        'wpse52122_meta_box_cb',
        'post',
        'side',
        'low'
    );
}


/*
 * Callback function for the meta box added above. This is what renders the 
 * boxes HTML.
 *
 * @uses get_post_meta
 * @uses wp_nonce_field
 * @uses checked
 */
function wpse52122_meta_box_cb($post)
{
    $meta = get_post_meta($post->ID, '_wpse52122_style', true);
    wp_nonce_field('wpse52122_nonce', 'wpse52122_nonce', false);
    echo '<p>';
    echo '<label for="wpse52122_style">' . __('Edited?', 'wpse52122') . '</label> ';
    echo '<input type="checkbox" name="wpse52122_style" id="wpse52122_style" ' .
         checked($meta, 'on', false) . ' />';
    echo '</p>';
}


add_action('save_post', 'wpse52122_save_post');
/*
 * Hooked in `save_post` this is the function that will take care of saving the
 * checkbox rendered above
 *
 * @uses update_post_meta
 * @uses wp_verify_nonce
 * @uses current_user_can
 */
function wpse52122_save_post($post_id)
{
    // no auto save
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
        return;

    // can the current user edit this post?
    if(!current_user_can('edit_post', $post_id))
        return;

    // Do we have our nonce?
    if(!isset($_POST['wpse52122_nonce']) ||
        !wp_verify_nonce($_POST['wpse52122_nonce'], 'wpse52122_nonce')) return;

    // if the the box is set, save meta key with 'on' as the value.
    $val = isset($_POST['wpse52122_style']) && $_POST['wpse52122_style'] ?
                'on' : 'off';
    update_post_meta($post_id, '_wpse52122_style', esc_attr($val));
}


add_filter('post_class', 'wpse52122_post_class', 10, 3);
/*
 * Hooked into `post_class` this function adds the class 'edited' to the
 * post_class array if the meta value `_wpse52122_style` is set to 'on'
 *
 * @uses get_post_meta
 * @return array The Post classes
 */
function wpse52122_post_class($classes, $class, $post_id)
{
    if('on' == get_post_meta($post_id, '_wpse52122_style', true))
    {
        $classes[] = 'edited';
    }
    return $classes;
}


add_filter('post_class', 'wpse52122_post_class_alt', 11, 3);
/*
 * Filter the post_class and add the class edited if the post_date_gmt & 
 * post_modified_gmt don't match up.
 *
 * @uses get_post
 * @return array The post classes
 */
function wpse52122_post_class_alt($classes, $class, $post_id)
{
    $post = get_post($post_id);
    if(!$post) return $classes;
    $created = strtotime($post->post_date_gmt);
    $mod = strtotime($post->post_modified_gmt);
    if($mod > $created)
    {
        $classes[] = 'edited-alt';
    }
    return $classes;
}
