<?php
/*
Plugin Name: User One Category
Plugin URI: http://wordpress.stackexchange.com/q/65959/6035
Description: Only allow users to post in a single category and view that category.
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

WPSE65959_User_One_Cat::init();

class WPSE65959_User_One_Cat
{
    /**
     * Nonce name.
     *
     */
    const NONCE = 'wpse65959_nonce';

    /**
     * The user meta key we'll use
     *
     */
    const KEY = 'wpse65959_category';

    /**
     * The taxonomy we'll use.  Just 'category' in this case.
     *
     */
    const TAX = 'category';

    public static function init()
    {
        add_action(
            'edit_user_profile',
            array(__CLASS__, 'show_field')
        );

        add_action(
            'edit_user_profile_update',
            array(__CLASS__, 'save')
        );

        add_action(
            'add_meta_boxes_post',
            array(__CLASS__, 'remove_metabox')
        );

        add_filter(
            'pre_option_default_category',
            array(__CLASS__, 'default_cat')
        );

        add_filter(
            'request',
            array(__CLASS__, 'request')
        );
    }

    public static function show_field($user)
    {
        $terms = get_terms(self::TAX, array('hide_empty' => false));

        wp_nonce_field(self::NONCE . $user->ID, self::NONCE, false);

        echo '<h4>';
        esc_html_e('User Category', 'wpse65959');
        echo '</h4>';

        printf('<select name="%1$s" id="%1$s">', esc_attr(self::KEY));
        echo '<option value="">----</option>';
        foreach($terms as $t)
        {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($t->term_id),
                selected(get_user_meta($user->ID, self::KEY, true), $t->term_id, false),
                esc_html($t->name)
            );
        }
        echo '</select>';
    }

    public static function save($user_id)
    {
        if(
            !isset($_POST[self::NONCE]) ||
            !wp_verify_nonce($_POST[self::NONCE], self::NONCE . $user_id)
        ) return;

        if(!current_user_can('edit_user', $user_id))
            return;

        if(isset($_POST[self::KEY]) && $_POST[self::KEY])
            update_user_meta($user_id, self::KEY, absint($_POST[self::KEY]));
        else
            delete_user_meta($user_id, self::KEY);
    }

    public static function remove_metabox()
    {
        if(current_user_can('manage_options'))
            return; // this is an admin.  Admins can do what they want.

        remove_meta_box(
            'categorydiv',
            'post',
            'side'
        );
    }

    public static function default_cat($false)
    {
        if(current_user_can('manage_options'))
            return $false; // don't change default category for admins

        if($cat = get_user_meta(wp_get_current_user()->ID, self::KEY, true))
            return $cat; // we have a default category for this user.

        return $false; // no default category, return the original value
    }

    public static function request($vars)
    {
        if(current_user_can('manage_options'))
            return $vars; // admins can view whatever

        // if the user has a default category, make sure they only see that category
        if($cat = get_user_meta(wp_get_current_user()->ID, self::KEY, true))
            $vars['cat'] = $cat;

        return $vars;
    }
}
