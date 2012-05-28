<?php    
/*
Plugin Name: Gallery for One Member
Plugin URI: http://wordpress.stackexchange.com/questions/32840/if-user-is-logged-in-only-show-certain-page
Description: When a user registers, give them access to one gallery (post type) and no others.
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

register_activation_hook(__FILE__, 'wpse32840_activation');
function wpse32840_activation()
{
    flush_rewrite_rules();
}   

add_action('init', 'wpse32840_register_post_type');
function wpse32840_register_post_type()
{
    register_post_type(
        'client_gallery',
        array(
            'label'                 => __('Client Galleries'), // probably needs better lables
            'public'                => true, // allow people to see this on the front end
            'exclude_from_search'   => true, // cant be brought up via a search
            'show_in_nav_menus'     => false, // not in nav menus (we'll do this manually)
            'rewrite'               => array('slug' => 'gallery')
        )
    );      
}

/**
 * When we register a user, add a 'client_gallery' post
 * If that fails, set the gallery id to 0, so we can set it later.
 */
add_action('user_register', 'wpse32840_capture_register');
function wpse32840_capture_register($user_id)
{
    $userobj = get_user_by('id', $user_id);
    
    $gallery_id = wp_insert_post(
        array(
            'post_type'     => 'client_gallery',
            'post_title'    => sprintf('%s gallery', $userobj->user_login)
        ), 
        true
    );
    
    if(is_wp_error($gallery_id)) $gallery_id = 0;
    
    update_user_meta($user_id, 'wpse32840_gallery_id', absint($gallery_id));
}

/**
 * We should also be able to give users acces to gallery manually,
 * so let's add some fields to the user profile page
 */
add_action('edit_user_profile', 'wpse32840_user_profile');
function wpse32840_user_profile($user)
{
    $gallery_id = get_user_meta($user->ID, 'wpse32840_gallery_id', true);
    
    // get all of our galleries!
    $galleries = get_posts(
        array(
            'post_type'     => 'client_gallery',
            'numberposts'   => -1,
            'post_status'   => 'any'
        )
    );
    
    // no galleries?  bail.
    if(!$galleries) return;
    
    // nonce
    wp_nonce_field('wpse32840_nonce', 'wpse32840_nonce', false);
    
    // our fields
    echo '<h3>' . __('Gallery') . '</h3>';
    echo '<select name="wpse32840_gallery">';
    echo '<option ' . selected($gallery_id, 0, false). ' value="0">No gallery</option>';
    foreach($galleries as $g)
    {
        echo '<option ' . selected($gallery_id, $g->ID, false) . ' value="' . 
              absint($g->ID) . '">' . esc_html($g->post_title) . '</option>';
    }
    echo '</select>';
}


/**
 * Save the user's gallery ID from the user edit page.
 */
add_action('edit_user_profile_update', 'wpse32840_save_user');
function wpse32840_save_user($user_id)
{
    if(!isset($_POST['wpse32840_nonce']) || !wp_verify_nonce($_POST['wpse32840_nonce'], 'wpse32840_nonce')) 
        return;
    
    if(!isset($_POST['wpse32840_gallery' ]))
        return;
    
    update_user_meta($user_id, 'wpse32840_gallery_id', absint($_POST['wpse32840_gallery']));
}


/**
 * Hook into template redirect to stop normal visitor from accessing galleries
 */
add_action('template_redirect', 'wpse32840_check_user');
function wpse32840_check_user()
{
    // not on a client gallery?  bail
    if(!is_singular('client_gallery')) return;
    
    // is the is an admin?
    if(current_user_can('manage_options')) return;
    
    // thrown non logged in users back to the home page
    if(!is_user_logged_in())
    {
        wp_redirect(home_url(), 302);
        exit();
    }
    
    $user = wp_get_current_user();
    $gallery_id = get_queried_object_id();
    
    // if the user isn't an admin or isn't assigned this gallery, send them back to the home page
    if($gallery_id != get_user_meta($user->ID, 'wpse32840_gallery_id', true))
    {
        wp_redirect(home_url(), 302);
        exit();
    }
    // By here, the user is authenticated, let them continue on their merry way.
}


/**
 * Filter the nav menu so logged in users only see certain things
 */
add_filter('wp_nav_menu_items', 'wpse32840_filter_nav', 10, 2);
function wpse32840_filter_nav($items, $args)
{
    /**
     * you can check for theme location here via $args... check to see if this is the main nav
     * in twenty eleven, we can check to see if this is the "primary" menu
     * Other thigns will be different.  If we're not in the primary menu, bail
     */
    if('primary' != $args->theme_location) return $items;
    
    // Not logged in?  return the menu
    if(!is_user_logged_in()) return $items;
    
    // if this is an admin, return the menu unaltered.
    if(current_user_can('manage_options')) return $items;
    
    // our user is logged in an not an admin, build them a new menu
    
    // get our current user
    $user = wp_get_current_user();
    
    // get the users gallery
    $gallery_id = get_user_meta($user->ID, 'wpse32840_gallery_id', true);
    $gallery = get_post(absint($gallery_id));
    
    $items = '<li class="menu-item"><a href="' . esc_url(home_url()) . '">Home</a></li>';
    $items .= '<li class="menu-item"><a href="' . esc_url(get_permalink($gallery)) . '">Gallery</a></li>';
    return $items;
}
