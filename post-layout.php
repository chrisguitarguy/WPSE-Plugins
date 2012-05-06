<?php    
/*
Plugin Name: Post Layout Class
Plugin URI: http://wordpress.stackexchange.com/questions/32973/using-taxonomies-to-handle-layout
Description: Adds a meta boxes allowing the post layout to be specified.  Then adds that layout to the post_class
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

add_action('add_meta_boxes', 'wpse32973_add_meta_box');
function wpse32973_add_meta_box()
{
    add_meta_box(
        'wpse32973-box',
        __('Post Layout'),
        'wpse32973_meta_box_cb',
        'post',
        'side',
        'high'
   );
}


function wpse32973_meta_box_cb($post)
{
    $meta = get_post_meta($post->ID, '_wpse32973_layout', true);
    
    // what options you'd like to have for layouts.  value => label
    $opts = array(
        'right_thumb' => __('Right Thumbnail'),
        'left_thumb'  => __('Left Thumbnail')
   );
    
    wp_nonce_field('wpse32973_nonce', 'wpse32973_nonce', false);
    echo '<select name="wpse32973_layout">';
    foreach($opts as $val => $label)
    {
        echo '<option ' . selected($val, $meta, false) . ' value="' .
              esc_attr($val) . '">' . esc_html($label) . '</option>';
    }
    echo '</select>';
}


add_action('edit_post', 'wpse32973_save');
function wpse32973_save($post_id)
{
    if(!isset($_POST['wpse32973_nonce']) ||
        !wp_verify_nonce($_POST['wpse32973_nonce'], 'wpse32973_nonce')) return;
    
    if(!current_user_can('edit_post', $post_id)) 
        return;
    
    if(isset($_POST['wpse32973_layout']))
    {
        update_post_meta(
            $post_id,
            '_wpse32973_layout',
            esc_attr(strip_tags($_POST['wpse32973_layout']))
        );
    }
}


add_filter('post_class', 'wpse32973_post_class', 10, 3);
function wpse32973_post_class($classes, $class, $post_id)
{
    $layout = get_post_meta($post_id, '_wpse32973_layout', true);
    if(!empty($layout))
    {
        $classes[] = esc_attr($layout);
    }
    return $classes;
}
