<?php    
/*
Plugin Name: Mask Outbound Links
Plugin URI: http://wordpress.stackexchange.com/questions/36168/mask-and-track-outbound-links
Description: Change all external links so they get routed through a redirect.
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

register_activation_hook(__FILE__, 'wpse36168_activation');
/**
 * Activation hook.  flushes rewrite rules and adds ours.
 */
function wpse36168_activation()
{
    flush_rewrite_rules();
    wpse36168_add_rewrite_rule();
}


add_action('init', 'wpse36168_add_rewrite_rule');
/**
 * Add our rewrite rule
 */
function wpse36168_add_rewrite_rule()
{
    add_rewrite_rule(
        '^go/(.*?)$',
        'index.php?go=$matches[1]',
        'top'
    );
}


add_filter('query_vars', 'wpse36168_add_go_var');
/**
 * Tell WP not to strip out or "go" query var
 */
function wpse36168_add_go_var($vars)
{
    $vars[] = 'go';
    return $vars;
}


add_action('template_redirect', 'wpse36168_catch_external');
/**
 * Catch external links from our "go" url and redirect them
 */
function wpse36168_catch_external()
{
    if($url = get_query_var('go'))
    {
        wp_redirect(esc_url($url), 302);
        exit();
    }
}


add_filter('the_content', 'wpse36168_replace_links', 1);
/**
 * Replace external links with our "go" links
 */
function wpse36168_replace_links($content)
{
    $content = preg_replace_callback(
        '%<a.*?href="(.*?)"[^<]+</a>%i',
        'wpse36168_maybe_replace_links',
        $content
   );
    return $content;
}


/*
 * Callback function for preg_replace_callback
 */
function wpse36168_maybe_replace_links($matches)
{
    if(!preg_match(sprintf('#^%s#i', home_url()), $matches[1]))
    {
        $url = $matches[1];
        // http:// we'll add it later
        $url = str_replace('http://', '', $url);
        $url = sprintf('/go/%s', $url);
        return str_replace($matches[1], home_url($url), $matches[0]);
    }
    else
    {
        return $matches[0];
    }
}

