<?php
/**
 * Plugin Name: Custom Author Base
 * Plugin URI: http://wordpress.stackexchange.com/q/77228/6035
 * Description: Change the author base to something of your choosing.
 * Version: 1.0
 * Text Domain: custom-author-base
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: GPL-2.0+
 *
 * Copyright 2012 Christopher Davis <http://christopherdavis.me>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

!defined('ABSPATH') && exit;

Custom_Author_Base::init();

class Custom_Author_Base
{
    const SETTING = 'author_base';

    private static $ins = null;

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    public static function init()
    {
        add_action('plugins_loaded', array(self::instance(), '_setup'));
    }

    public function _setup()
    {
        add_action('init', array($this, 'set_base'));
        add_action('admin_init', array($this, 'fields'));
        add_action('load-options-permalink.php', array($this, 'maybe_save'));
    }

    public function set_base($base=null)
    {
        global $wp_rewrite;

        is_null($base) && $base = get_option(self::SETTING);

        if ($base) {
            $wp_rewrite->author_base = $base;
        }
    }

    public function fields()
    {
        add_settings_field(
            self::SETTING,
            __('Author Base', 'custom-author-base'),
            array($this, 'field_cb'),
            'permalink',
            'optional',
            array('label_for' => self::SETTING)
        );
    }

    public function maybe_save()
    {
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            return;
        }

        if (!empty($_POST[self::SETTING])) {
            $res = sanitize_title_with_dashes($_POST[self::SETTING]);
            update_option(self::SETTING, $res);
            $this->set_base($res);
        } else {
            delete_option(self::SETTING);
        }
    }

    public function field_cb()
    {
        printf(
            '<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" />',
            esc_attr(self::SETTING),
            esc_attr(get_option(self::SETTING))
        );
    }
}
