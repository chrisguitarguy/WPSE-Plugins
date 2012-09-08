<?php
/*
Plugin Name: Fix Rel Canonical
Plugin URI: http://wordpress.stackexchange.com/q/64535/6035
Description: Fix WordPress' rel canonical link for rewrite rules.
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

register_activation_hook(__FILE__, function() {
    wpse64535_add_rewrite_rules();
    flush_rewrite_rules();
});

add_action('init', 'wpse64535_add_rewrite_rules');
function wpse64535_add_rewrite_rules()
{
    add_rewrite_rule(
        '^roller-derbies/([^/]+)/?$',
        'index.php?pagename=states&var_state=$matches[1]',
        'top'
    );

    add_rewrite_rule(
        '^roller-derbies/([^/]+)/([^/]+)/?$',
        'index.php?pagename=cities&var_state=$matches[1]&var_city=$matches[2]',
        'top'
    );
}

add_filter('query_vars', 'wpse64535_add_query_vars');
function wpse64535_add_query_vars($vars)
{
    $vars[] = 'var_state';
    $vars[] = 'var_city';
    return $vars;
}

add_action('template_redirect', 'wpse64535_maybe_fix');
function wpse64535_maybe_fix()
{
    if(get_query_var('var_state') || get_query_var('var_city'))
    {
        remove_action('wp_head', 'rel_canonical');
        add_action('wp_head', 'wpse64535_fix_canonical');
    }
}

function wpse64535_fix_canonical()
{
    $link = home_url('roller-derbies/');

    // might want to validate these before just using them?
    if($state = get_query_var('var_state'))
        $link .= "{$state}/";

    if($city = get_query_var('var_city'))
        $link .= "{$city}/";

    echo '<link rel="canonical" href="' . esc_url($link) . '" />';
}
