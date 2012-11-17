<?php
/*
Plugin Name: Secondary Content
Plugin URI: http://wordpress.stackexchange.com/q/72922/6035
Description: Add a meta box with secondary content.
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

!defined('ABSPATH') && exit;

Secondary_Content::init();

class Secondary_Content
{
    // meta key we'll use to save things.
    const META = '_secondary_content';

    // nonce name to check
    const NONCE = '_s_content_nonce';

    // post type to which we'll add the box
    const TYPE = 'page';

    private static $ins = null;

    public static function init()
    {
        add_action('plugins_loaded', array(self::instance(), '_setup'));
    }

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    public function _setup()
    {
        add_action('add_meta_boxes_' . self::TYPE, array($this, 'add_box'));
        add_action('save_post', array($this, 'save'), 10, 2);
    }

    /**
     * Adds a meta box to the `page` post type.
     *
     * @uses    add_meta_box
     * @return  void
     */
    public function add_box()
    {
        add_meta_box(
            'secondary-content',
            __('Secondary Content', 'wspe'),
            array($this, 'box_cb'),
            self::TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Metabox callback function.
     *
     * @access  public
     * @param   object $post The current $post
     * @uses    get_post_meta
     * @uses    wp_editor
     * @return  void
     */
    public function box_cb($post)
    {
        wp_nonce_field(self::NONCE . $post->ID, self::NONCE, false);

        wp_editor(
            get_post_meta($post->ID, self::META, true),
            self::META
        );
    }

    /**
     * Hooked into `save_post`.  Makes sure this is the request we want and the
     * user has permission, then saves the custom field.
     *
     * @access  public
     * @param   int $post_id
     * @param   object $post
     * @uses    wp_verify_nonce
     * @uses    current_user_can
     * @uses    update_post_meta
     * @uses    delete_post_meta
     * @return  void
     */
    public function save($post_id, $post)
    {
        if(
            self::TYPE != $post->post_type ||
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        ) return;

        if(
            !isset($_POST[self::NONCE]) ||
            !wp_verify_nonce($_POST[self::NONCE], self::NONCE . $post_id)
        ) return;

        if(!current_user_can('edit_post', $post_id))
            return;

        if(!empty($_POST[self::META]))
        {
            update_post_meta(
                $post_id,
                self::META,
                current_user_can('unfiltered_html') ?
                    $_POST[self::META] : wp_filter_post_kses($_POST[self::META])
            );
        }
        else
        {
            delete_post_meta($post_id, self::META);
        }
    }

    /**
     * Meant to be used as a template tag. A simple helper to spit out our
     * secondary content.
     *
     * @access  public
     * @param   object $post
     * @param   bool $echo (optional) defaults to true.
     * @uses    get_post_meta
     * @return  string
     */
    public static function content($post, $echo=true)
    {
        $res = apply_filters('secondary_content',
            get_post_meta($post->ID, self::META, true));

        if($echo)
            echo $res;

        return $res;
    }
}
