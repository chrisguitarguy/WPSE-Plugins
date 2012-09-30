<?php
/*
Plugin Name: Allow GPX Files
Plugin URI: http://wordpress.stackexchange.com/q/66651/6035
Description: Allow uploads of zip files through the WP Uploader.
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

add_filter('upload_mimes', 'wpse66651_allow_gpx');
/**
 * Allow the uploading of .gpx files.
 *
 * @param   array $mimes The allowed mime types in file extension => mime format
 * @return  array
 */
function wpse66651_allow_gpx($mimes)
{
    $mimes['gpx'] = 'application/xml';
    return $mimes;
}

//add_filter('the_content', 'wpse66651_display_files');
/**
 * Example of how to add links to attachments to the bottom of posts
 * automatically.
 *
 * @param   string $c the post content
 * @return  string The content + the list of files.
 */
function wpse66651_display_files($c)
{
    global $post;

    if(!in_the_loop())
        return $c;

    $attachments = get_posts(array(
        'post_parent'    => $post->ID,
        'post_type'      => 'attachment',
        'post_mime_type' => 'application/xml',
        'nopaging'       => true,
    ));

    if(!$attachments)
        return $c;

    $list = '<ul class="gpx-list">';
    foreach($attachments as $a)
        $list .= '<li>' . wp_get_attachment_link($a->ID) . '</li>';
    $list .= '</ul>';

    return $c . $list;
}
