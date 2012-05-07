<?php    
/*
Plugin Name: Add Message to Posts
Plugin URI: http://wordpress.stackexchange.com/q/51338/6035
Description: Add a message before and after the post content
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

add_filter('the_content', 'wpse51338_filter_content');
/*
 * Filter the content to add a message before and after it.
 */
function wpse51338_filter_content($content)
{
    // not a singular post?  just return the content
    if(!is_singular())
        return $content;

    $msg = sprintf(
        '<p class="special-message">%s</p>',
        __('Here is an awesome message!') // this is your message
    );
    return $msg . $content . $msg;
}
