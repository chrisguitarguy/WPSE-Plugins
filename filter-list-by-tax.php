<?php
/*
Plugin Name: Filter Posts by Tax
Plugin URI: http://wordpress.stackexchange.com/q/63444/6035
Description: Get posts, then sort them into taxonomies
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

function wpse63444_get_posts($post_type, $terms, $tax)
{
    $posts = get_posts(array(
        'post_type'     => $post_type,
        'meta_key'      => 'number', // the meta key
        'order_by'      => 'meta_value_num',
        'order'         => 'ASC', // might have to tweak the order a bit
        'numberposts'   => -1, // get ALL THE POSTS
        'tax_query'     => array(
            array(
                'taxonomy'          => $tax,
                'field'             => 'slug',
                'terms'             => $terms,
                'include_children'  => false,
            ),
        ),
    ));

    if(!$posts)
        return array(); // bail if we didn't get any posts

    $res = array();

    foreach($terms as $t)
    {
        // PHP < 5.3 will need something different here
        $res[$t] = array_filter($posts, function($p) use ($t, $tax) {
            if(has_term($t, $tax, $p))
                return $p; // the post has this term, use it
        });
    }

    return $res;
}


/*
Usage example:

$res = wpse63444_get_posts('post', array('cat-a', 'cat-b', 'cat-c'), 'category');

if($res)
{
    foreach($res as $cat => $posts)
    {
        if(!$posts)
            continue;

        echo '<h1>', get_term_by('slug', $cat, 'category')->name, '</h1>';
        foreach($posts as $p)
            echo '<h2>', $p->post_title, ' ', get_post_meta($p->ID, 'number', true), '</h2>';
    }
}
*/
