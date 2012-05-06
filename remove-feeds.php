<?php    
/*
Plugin Name: Remove Feeds
Plugin URI: http://wordpress.stackexchange.com/questions/33072/how-to-remove-feeds-from-wordpress-totally
Description: Remove all feeds from WordPress
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


add_action('wp_head', 'wpse33072_wp_head', 1);
/**
 * Remove feed links from wp_head
 */
function wpse33072_wp_head()
{
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
}


foreach(array('rdf', 'rss', 'rss2', 'atom') as $feed)
{
    add_action('do_feed_' . $feed, 'wpse33072_remove_feeds', 1);
}
unset($feed);
/**
 * prefect actions from firing on feeds when the `do_feed` function is 
 * called
 */
function wpse33072_remove_feeds()
{
    // redirect the feeds! don't just kill them
    wp_redirect(home_url(), 302);
    exit();
}


add_action('init', 'wpse33072_kill_feed_endpoint', 99);
/**
 * Remove the `feed` endpoint
 */
function wpse33072_kill_feed_endpoint()
{
    // This is extremely brittle.
    // $wp_rewrite->feeds is public right now, but later versions of WP
    // might change that
    global $wp_rewrite;
    $wp_rewrite->feeds = array();
}


register_activation_hook(__FILE__, 'wpse33072_activation');
/**
 * Activation hook
 */
function wpse33072_activation()
{
    wpse33072_kill_feed_endpoint();
    flush_rewrite_rules();
}
