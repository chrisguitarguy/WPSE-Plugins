<?php    
/*
Plugin Name: Rewrite Endpoint Example
Plugin URI: http://wordpress.stackexchange.com/questions/42279/custom-post-type-permalink-endpoint
Description: An example of how to use rewrite endpoints
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


add_action('init', 'wpse42279_add_endpoints');
function wpse42279_add_endpoints()
{
    add_rewrite_endpoint('tours', EP_PAGES);
    add_rewrite_endpoint('activities', EP_PAGES);
}

add_action('template_redirect', 'wpse42279_catch_vars');
function wpse42279_catch_vars()
{
    if(get_query_var('activities'))
    {
        // do stuff!
        exit();
    }
}

add_filter('request', 'wpse42279_filter_request');
function wpse42279_filter_request($vars)
{
    if(isset($vars['tours'])) $vars['tours'] = true;
    if(isset($vars['activities'])) $vars['activities'] = true;
    return $vars;
}
