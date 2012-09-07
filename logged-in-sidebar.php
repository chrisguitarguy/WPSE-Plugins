<?php
/*
Plugin Name: Logged in Sidebar
Plugin URI: http://wordpress.stackexchange.com/q/64492/6035
Description: Give logged in users their own sidebar. Maybe.
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

add_action('widgets_init', 'wpse64492_register');
function wpse64492_register()
{
    register_sidebar(array(
        'name'  => __('Logged In Sidebar', 'wpse64492'),
        'id'    => 'logged-in'
    ));
}

add_filter('sidebars_widgets', 'wpse64492_switch');
function wpse64492_switch($widgets)
{
    if(is_admin())
        return $widgets;

    $key = 'sidebar-1'; // the sidebar you want to change!

    if(isset($widgets[$key]) && is_user_logged_in() && isset($widgets['logged-in']))
        $widgets[$key] = $widgets['logged-in'];

    return $widgets;
}
