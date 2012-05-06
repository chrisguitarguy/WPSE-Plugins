<?php    
/*
Plugin Name: iFrame your WordPress
Plugin URI: http://wordpress.stackexchange.com/questions/32725/how-do-i-create-an-html-embed-code-to-publish-wp-posts-on-other-non-wp-websites
Description: Simple way to create an embeddable version of your WordPress site
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

register_activation_hook(__FILE__ , 'wpse32725_activation');
/**
 * Activation hook to flush rewrite rules
 * 
 * @uses flush_rewrite_rules
 */
function wpse32725_activation()
{
    wpse32725_add_rewrite();
    flush_rewrite_rules();
}


add_action('init', 'wpse32725_add_rewrite');
/**
 * Adds the rewrite rule for our iframe
 * 
 * @uses add_rewrite_rule
 */
function wpse32725_add_rewrite()
{
    add_rewrite_rule(
        '^iframe$',
        'index.php?iframe=true',
        'top'
   );
    
    // shortcode for our iframe
    add_shortcode('iframe-code', 'wpse32725_iframe_code');
}


add_filter('query_vars', 'wpse32725_filter_vars');
/**
 * adds our iframe query variable so WP knows what it is and doesn't
 * just strip it out
 */
function wpse32725_filter_vars($vars)
{
    $vars[] = 'iframe';
    return $vars;
}


add_action('template_redirect', 'wpse32725_catch_iframe');
/**
 * Catches our iframe query variable.  If it's there, we'll stop the 
 * rest of WP from loading and do our thing.  If not, everything will
 * continue on its merry way.
 * 
 * @uses get_query_var
 * @uses get_posts
 */
function wpse32725_catch_iframe()
{
    // no iframe? bail
    if(! get_query_var('iframe')) return;
    
    // Here we can do whatever need to do to display our iframe.
    // this is a quick example, but maybe a better idea would be to include
    // a file that contains your template for this?
    $posts = get_posts(array('numberposts' => 5));
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <?php /* stylesheets and such here */ ?>
    </head>
    <body>
        <ul>
            <?php foreach($posts as $p): ?>
                <li>
                    <a href="<?php echo esc_url(get_permalink($p)); ?>"><?php echo esc_html($p->post_title); ?></a>
                </li>
            <?php endforeach; ?>
        <ul>
    </body>
    </html>
    <?php
    // finally, call exit(); and stop wp from finishing eg. loading the
    // templates
    exit();
}


function wpse32725_iframe_code()
{
    return sprintf(
        '<code>&lt;iframe src="%s"&gt;&lt;/iframe&gt;</code>',
        esc_url(home_url('/iframe/'))
   );
}
