<?php    
/*
Plugin Name: Checking for CURL Support [WPSE 51312]
Description: Deactive a plugin if CUR
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

register_activation_hook(__FILE__, 'wpse51312_activation');
function wpse51312_activation()
{
    if(!function_exists('curl_exec'))
    {
        // Deactivate the plugin
        deactivate_plugins(__FILE__);

        // Show the error page, Maybe this shouldn't happen?
        wp_die(
            __('You must enable cURL support to use INSERT PLUGIN NAME'),
            __('Error')
        );
    }
}
