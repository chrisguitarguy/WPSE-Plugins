<?php
/*
Plugin Name: Content Filter Remover
Plugin URI: http://wordpress.stackexchange.com/q/66038/6035
Description: Removes a few filters from the content.
Version: 1.0
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

add_action('init', 'wpse66038_remover');
function wpse66038_remover()
{
   remove_filter('the_content', 'wpautop');
   remove_filter('the_content', 'convert_chars');
}
