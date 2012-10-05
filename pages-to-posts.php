<?php
/*
Plugin Name: Pages to Posts
Plugin URI: http://wordpress.stackexchange.com/q/45561/6035
Description: Associated posts with pages three different ways.
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

WPSE45561_Pages_Posts::init();

class WPSE45561_Pages_Posts
{
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

    protected function __construct()
    {
        add_action('p2p_init', array($this, 'connections'));
    }

    public function connections()
    {
        p2p_register_connection_type(array(
            'name'      => 'page_to_posts',
            'from'      => 'page',
            'to'        => 'post',
            'admin_box' => array(
                'show'    => 'from', // only show on pages
                'context' => 'advanced', // put admin box in main col, instead of side
            ),
        ));
    }
} // end class
