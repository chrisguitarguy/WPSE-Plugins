<?php    
/*
Plugin Name: autop XMLRPC Content
Plugin URI: http://wordpress.stackexchange.com/questions/44849/how-to-enable-wpautop-for-xmlrpc-content
Description: Adds a new XMLRPC method that adds `autop` to post content
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

add_filter('xmlrpc_methods', 'wpse44849_xmlrpc_methods');
/**
 * Filters the XMLRPC method to include our own custom method
 */
function wpse44849_xmlrpc_methods($method)
{
    $methods['post_autop'] = 'wpse44849_autop_callback';
    return $methods;
}


/**
 * Callback function for our custom XML RPC method. Stolen from the
 */
function wpse44849_autop_callback($args)
{

    $post_ID     = absint($args[0]);
    $username    = $args[1];
    $password    = $args[2];
    
    $user = wp_authenticate($username, $password);

    // not a valid user name/password?  bail.
    if(!$user || is_wp_error($user))
    {
        return false;
    }
    
    $post = get_posts(array('p' => $post_ID));
    
    // no posts?  bail.
    if(empty($post))
    {
        return false;
    }
    
    $post = $post[0];
    
    // the magic happens here
    $post->post_content = wpautop($post->post_content);
    
    return (array) $post;
}
