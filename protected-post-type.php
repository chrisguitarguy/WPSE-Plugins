<?php
/*
Plugin Name: Protected Post Type
Plugin URI: http://wordpress.stackexchange.com/q/71804/6035
Description: An example of how to "protect" a post type behind a token.
Version: 1.0
Text Domain: wpse
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

WPSE71804::init();

class WPSE71804
{
    // post type key, whatever this happens to be.
    const TYPE = 'customer';

    // endpoint mask, 2 ^ 18
    const EP = 262144;

    // key prefix, used for options
    const PREFIX = 'wpse71804_key_';

    // container for the instance of this class
    private static $ins = null;

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    public static function init()
    {
        add_action('plugins_loaded', array(self::instance(), '_setup'));
        register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivate'));
    }

    public static function activate()
    {
        self::instance()->register();
        self::instance()->endpoint();
        flush_rewrite_rules();
    }

    public static function deactivate()
    {
        flush_rewrite_rules();
    }

    // add actions and such.
    public function _setup()
    {
        add_action('init', array($this, 'register'));
        add_action('init', array($this, 'endpoint'), 11);
        add_action('template_redirect', array($this, 'validate_key'));
    }

    // register the post type
    public function register()
    {
        // rewrite is the args to pay attention to we need 
        // to set a custom endpoint mask
        register_post_type(self::TYPE, array(
            'label'     => __('Customers', 'wpse'),
            'public'    => true,
            'rewrite'   => array(
                'slug'          => 'customer',
                'ep_mask'       => self::EP,
                'with_front'    => false,
            ),
        ));
    }

    public function endpoint()
    {
        add_rewrite_endpoint('key', self::EP);
    }

    public function validate_key()
    {
        if(!is_singular(self::TYPE) || current_user_can('manage_options'))
            return;

        if(!($_key = get_query_var('key')) || !($key = self::get_key($_key)))
        {
            global $wp_query;
            $wp_query->set_404();
        }

        // if we're here, the key is okay, let the request go through
    }

    /********** API **********/

    // create a new key
    public static function create_key()
    {
        $k = wp_generate_password(24, false);
        self::update_key($k, 'notdone');
        return $k;
    }

    // update a key
    public static function update_key($key, $val='done')
    {
        return update_option(self::PREFIX . $key, $val);
    }

    // delete a key
    public static function delete_key($key)
    {
        return delete_option(self::PREFIX . $key);
    }

    public static function get_key($key)
    {
        return get_option(self::PREFIX . $key);
    }
}
