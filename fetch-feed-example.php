<?php
/*
Plugin Name: Fetch Feed Example
Plugin URI: http://wordpress.stackexchange.com/q/60754/6035
Description: an example of how to use fetch_feed
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


/**
 * An example of how to use fetch feed
 *
 * @uses    fetch_feed
 * @return  bool|string False on failure, the permalink on success
 */
function wpse60754_fetch_feed($feed_url)
{
    $feed = fetch_feed($feed_url);

    if(is_wp_error($feed))
        return false;

    $items = $feed->get_items(0, 1);

    if(count($items))
        return $items[0]->get_permalink();
    else
        return false;
}
