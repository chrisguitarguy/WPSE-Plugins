<?php    
/*
Plugin Name: Remove Page Comments
Plugin URI: http://wordpress.stackexchange.com/q/48145/6035
Description: Remove support for comments from Pages
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

add_action('init', 'wpse48145_remove_comments', 99);
/**
 * Removes support for comments from Pages.
 * 
 * @uses remove_post_type_support
 */
function wpse48145_remove_comments()
{
    remove_post_type_support('page', 'comments');
}

