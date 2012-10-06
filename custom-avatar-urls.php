<?php
/*
Plugin Name: Custom Avatar URLs
Plugin URI: http://wordpress.stackexchange.com/q/67312/6035
Description: Custom urls for author avatars.
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

WPSE67312_Avatars::init();

class WPSE67312_Avatars
{
    // we'll need a nonce key
    const NONCE = 'wpse67312_nonce';

    // The meta key where the avatar URL is stored.
    const META_KEY = 'wpse67312_avatar';

    /***** Singleton pattern to add hooks and such *****/

    private static $ins = null;

    public static function init()
    {
        add_action('plugins_loaded', array(__CLASS__, 'instance'));
    }

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    /**
     * Helper to fetch avatar urls.
     *
     * @param   int $user_id The user ID for which to fetch the avatar
     * @uses    get_user_meta To fetch the avatar URL.
     * @return  string The avatar url
     */
    public static function get_avatar($user_id)
    {
        return get_user_meta($user_id, self::META_KEY, true);
    }

    /**
     * Constructor.  Where all the real actions get added.
     *
     * @uses    add_action
     * @uses    add_filter
     * @return  void
     */
    protected function __construct()
    {
        add_action('edit_user_profile', array($this, 'field'));
        add_action('show_user_profile', array($this, 'field'));
        add_action('edit_user_profile_update', array($this, 'save'));
        add_action('personal_options_update', array($this, 'save'));
        add_filter('get_avatar', array($this, 'filter_avatar'), 10, 5);
    }

    public function field($user)
    {
        // Might want to hide this from authors?
        // if(!current_user_can('manage_options'))
        //      return;

        wp_nonce_field(self::NONCE . $user->ID, self::NONCE, false);
        echo '<h4>', esc_html__('Avatar URL', 'wpse'), '</h4>';
        printf(
            '<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" />',
            esc_attr(self::META_KEY),
            esc_attr(self::get_avatar($user->ID))
        );
    }

    public function save($user_id)
    {
        if(
            !isset($_POST[self::NONCE]) ||
            !wp_verify_nonce($_POST[self::NONCE], self::NONCE . $user_id)
        ) return; // nonce is no good, bail

        if(!current_user_can('edit_user', $user_id))
            return; // current user can't edit this user, bail

        if(!empty($_POST[self::META_KEY]))
        {
            // we have data! save it!
            update_user_meta(
                $user_id, 
                self::META_KEY,
                esc_url_raw($_POST[self::META_KEY])
            );
        }
        else
        {
            // empty field, delete the old value
            delete_user_meta($user_id, self::META_KEY);
        }
    }

    public function filter_avatar($avatar, $id_or_email, $size, $default, $alt)
    {
        // do the dance to get a user
        $id = false;
        if(is_numeric($id_or_email))
        {
            $id = $id_or_email;
        }
        elseif(is_object($id_or_email))
        {
            if(!empty($id_or_email->user_id))
                $id = $id_or_email->user_id; // comment
        }
        elseif($user = get_user_by('email', $id_or_email))
        {
            $id = $user->ID;
        }

        if($id && ($avt = self::get_avatar($id)))
        {
            $avatar = sprintf(
                '<img src="%1$s" alt="%2$s" title="%2$s" width="%3$s" />',
                esc_url($avt),
                esc_attr($alt),
                esc_attr($size)
            );
        }

        return $avatar;
    }
}
