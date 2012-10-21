<?php
/*
Plugin Name: CPT with Endpoints
Plugin URI: http://wordpress.stackexchange.com/q/45713/6035
Description: A custom post type with endpoints.
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

WPSE45713::init();

class WPSE45713
{
    const EP = 262144;

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
        self::instance()->endpoints();
        flush_rewrite_rules();
    }

    public static function deactivate()
    {
        flush_rewrite_rules();
    }

    public function _setup()
    {
        add_action('init', array($this, 'register'));
        add_action('init', array($this, 'endpoints'), 15);
        add_filter('request', array($this, 'filter_request'));
    }

    public function register()
    {
        register_post_type('product', array(
            'label'     => __('Products', 'wpse'),
            'public'    => true,
            'rewrite'   => array(
                'ep_mask'   => self::EP,
            ),
        ));
    }

    public function endpoints()
    {
        add_rewrite_endpoint('detailed', self::EP);
    }

    public function filter_request($vars)
    {
        if(isset($vars['detailed']))
            $vars['detailed'] = true;

        return $vars;
    }
}
