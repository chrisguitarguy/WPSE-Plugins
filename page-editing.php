<?php    
/*
Plugin Name: Page by Page Editing
Plugin URI: http://wordpress.stackexchange.com/questions/30211/access-on-specific-pages-in-wordpress-for-a-specific-user
Description: Give editors permission to edit only certain pages
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

add_action('edit_user_profile', 'wpse30211_user_profile');
function wpse30211_user_profile($user)
{
    // only show this on editor pages
    if(!in_array('editor', $user->roles)) return;
    
    // get the pages.
    $pages = get_posts(
        array(
            'post_type'     => 'page',
            'numberposts'   => -1,
            'post_status'   => 'any',
       )
   );
    
    // Bail if we don't have pages.
    if(!$pages) return;
    
    // Which pages can our user edit?
    $allowed = get_user_meta($user->ID, 'wpse30211_pages', true);
    if(!is_array($allowed) || empty($allowed)) $allowed = array();
    
    // nonce-i-fy things
    wp_nonce_field('wpse30211_nonce', 'wpse30211_nonce');
    
    // section heading...
    echo '<h3>' . __('Grant this User permission to edit...') . '</h3>';
    echo '<select multiple="multiple" name="wpse30211[]">';
    echo '<option value="0">None</option>';
    foreach($pages as $p)
    {
        // for use in checked() later...
        $selected = in_array($p->ID, $allowed) ? 'on' : 'off';
        echo '<option ' . selected('on', $selected, false) . ' value="' . esc_attr($p->ID) . '">' . esc_html($p->post_title) . '</option>';
    }
    echo '</select>';
}

add_action('edit_user_profile_update', 'wpse30211_user_save');
function wpse30211_user_save($user_id)
{
    // verify our nonce
    if(!isset($_POST['wpse30211_nonce']) || !wp_verify_nonce($_POST['wpse30211_nonce'], 'wpse30211_nonce'))
        return;
        
    // make sure our fields are set
    if(!isset($_POST['wpse30211'])) 
        return;
        
    $save = array();
    foreach($_POST['wpse30211'] as $p)
    {
        $save[] = $p;
    }
    update_user_meta($user_id, 'wpse30211_pages', $save);
}

add_action('load-post.php', 'wpse30211_kill_edit');
function wpse30211_kill_edit()
{
    $post_id = isset($_REQUEST['post']) ? absint($_REQUEST['post']) : 0;
    if(!$post_id) return;
    
    // bail if this isn't a page
    if('page' !== get_post_type($post_id)) return;
    
    $user = wp_get_current_user();
    $allowed = get_user_meta($user->ID, 'wpse30211_pages', true);
    if(!is_array($allowed) || empty($allowed)) $allowed = array();
    
    // if the user can't edit this page, stop the loading...
    if(!in_array($post_id, $allowed))
    {
        wp_die(
            __('User cannot edit this page'),
            __("You can't edit this post"),
            array('response' => 403)
       );
    }
}

add_action('pre_post_update', 'wpse30211_stop_update');
function wpse30211_stop_update($post_id)
{
    // not a page? bail.
    if('page' !== get_post_type($post_id)) return;
    
    $user = wp_get_current_user();
    $allowed = get_user_meta($user->ID, 'wpse30211_pages', true);
    if(!is_array($allowed) || empty($allowed)) $allowed = array();
    
    if(!in_array($post_id, $allowed)) 
    {
        wp_die(
            __('User cannot edit this page'),
            __("You can't edit this post"),
            array('response' => 403)
       );
    }
}
