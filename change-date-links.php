<?php
/*
Plugin Name: Change Date Permalink Structure
Plugin URI: http://wordpress.stackexchange.com/q/39274/6035
Description: Give the data permalink structure a "base"
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

Custom_Date_Base::init();

class Custom_Date_Base
{
    const SETTING = 'custom_date_base';

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
        add_action('admin_init', array($this, 'settings'));
        add_action('load-options-permalink.php', array($this, 'save'));
        add_action('init', array($this, 'set_date_base'));
    }

    public function settings()
    {
        add_settings_field(
            'custom-date-base',
            __('Date Base', 'custom-date-base'),
            array($this, 'field_cb'),
            'permalink',
            'optional',
            array('label_for' => self::SETTING)
        );
    }

    public function field_cb()
    {
        printf(
            '<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" />',
            esc_attr(self::SETTING),
            esc_attr(get_option(self::SETTING))
        );
    }

    // apparently options-permalink only halfways uses the settings api?
    public function save()
    {
        // make sure it's actually an update request.
        if('POST' != $_SERVER['REQUEST_METHOD'])
            return;

        // since this fires before the actual update stuff,
        // validate the permalink nonce.
        check_admin_referer('update-permalink');

        if(!empty($_POST[self::SETTING]))
        {
            update_option(
                self::SETTING,
                sanitize_title_with_dashes($_POST[self::SETTING])
            );
        }
        else
        {
            // remove it.
            delete_option(self::SETTING);
        }
    }

    public function set_date_base()
    {
        if($db = get_option(self::SETTING))
        {
            global $wp_rewrite;

            // current date permastruct
            $date_s = $wp_rewrite->get_date_permastruct();

            // get the "front" -- stuff before rewrite tags in post links
            $front = isset($wp_rewrite->front) ? $wp_rewrite->front : '/';

            // build some regex. We need to account for the global rewrite 
            // "front" as well as a possible "/date" that WP appends for
            // %post_id% permalink structure where the numbers of a Post ID
            // might conflict with with year/month/day numbers.
            $regex = '#^' . preg_quote($front, '#') . '(/date)?#ui';

            $wp_rewrite->date_structure = preg_replace($regex, "/{$db}/", $date_s);
        }
    }
}
