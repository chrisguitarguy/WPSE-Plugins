<?php
/*
Plugin Name: Admin Notice Example
Plugin URI: http://wordpress.stackexchange.com/q/72637/6035
Description: How to add admin notices.
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


add_action('admin_notices', 'wpse72637_show_names');
/**
 * An example of how to show an admin notice. Shows active plugins.
 *
 * @uses    get_plugins
 * @return  void
 */
function wpse72637_show_names()
{                  
    $paths = array();

    foreach(get_plugins() as $p_basename => $plugin)
    {
        $paths[] = "{$plugin['Name']}: " .
            (is_plugin_active($p_basename) ? 'Active' : 'Disabled');
    }

    echo '<div class="updated"><p>', implode(' --- ', $paths), '<p></div>';
}

