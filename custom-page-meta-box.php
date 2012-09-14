<?php
/*
Plugin Name: Custom Page Meta Box
Plugin URI: http://wordpress.stackexchange.com/q/57092/6035
Description: Put meta boxes on a plugin page for fun and profit
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

WPSE57092::init();

class WPSE57092
{
    /**
     * The hook to add an action to add our meta boxes.
     *
     */
    const ADD_HOOK = 'wpse57092_add_boxes';

    /**
     * The page key for our meta boxes.  You could use this as the "group" for
     * the settings API as well or something similar.
     *
     */
    const PAGE = 'do_wpse57092_boxes';

    /**
     * The setting key.
     *
     */
    const SETTING = 'wpse57092_opts';

    public static function init()
    {
        add_action(
            'admin_menu',
            array(__CLASS__, 'page')
        );

        add_action(
            self::ADD_HOOK,
            array(__CLASS__, 'meta_box')
        );

        add_action(
            'admin_init',
            array(__CLASS__, 'settings')
        );
    }

    public static function settings()
    {
        register_setting(
            self::PAGE,
            self::SETTING,
            array(__CLASS__, 'validate')
        );

        add_settings_section(
            'default',
            __('A Settings Section', 'wpse57092'),
            '__return_false',
            self::PAGE
        );

        add_settings_field(
            'wpse57092-text',
            __('Some Field', 'wpse57092'),
            array(__CLASS__, 'field_cb'),
            self::PAGE,
            'default',
            array('label_for' => self::SETTING)
        );
    }

    public static function meta_box()
    {
        add_meta_box(
            'custom-meta-wpse57092',
            __('Just Another Meta Box', 'wpse57092'),
            array(__CLASS__, 'box_cb'),
            self::PAGE,
            'main',
            'high'
        );
    }

    public static function box_cb($setting)
    {
        // do_settings_fields doesn't do form tables for you.
        echo '<table class="form-table">'; 
        do_settings_fields(self::PAGE, 'default');
        echo '</table>';
    }

    public static function field_cb($args)
    {
        printf(
            '<input type="text" id="%1$s" name="%1$s" class="widefat" value="%2$s" />',
            esc_attr($args['label_for']),
            esc_attr(get_option($args['label_for']))
        );
        echo '<p class="description">';
        _e('Just some help text here', 'wpse57092');
        echo '</p>';
    }

    public static function page()
    {
        $p = add_options_page(
            __('WPSE 57092 Options', 'wpse57092'),
            __('WPSE 57092', 'wpse57092'),
            'manage_options',
            'wpse57092-options',
            array(__CLASS__, 'page_cb')
        );
    }

    public static function page_cb()
    {
        do_action(self::ADD_HOOK);
        ?>
        <div class="wrap metabox-holder">
            <?php screen_icon(); ?>
            <h2><?php _e('WPSE 57092 Options', 'wpse57092'); ?></h2>
            <p>&nbsp;</p>
            <form action="<?php echo admin_url('options.php'); ?>" method="post">
                <?php
                settings_fields(self::PAGE);
                do_meta_boxes(self::PAGE, 'main', self::SETTING);
                ?>
            </form>
        </div>
        <?php
    }

    public static function validate($dirty)
    {
        return esc_url_raw($dirty);
    }
}
