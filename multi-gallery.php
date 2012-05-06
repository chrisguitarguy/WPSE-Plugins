<?php    
/*
Plugin Name: Multi Gallery
Plugin URI: http://wordpress.stackexchange.com/questions/36779/displaying-a-combination-of-galleries
Description: Easily display a combination of multiple "gallery" shortcodes
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

add_action('init', 'wpse36779_add_shortcode');
/**
 * Adds the shortcode
 * 
 * @ uses add_shortcode
 */
function wpse36779_add_shortcode()
{
    add_shortcode('multigallery', 'wpse36779_shortcode_cb');
}

/**
 * The shortcode callback function
 *
 * @ uses do_shortcode
 */
function wpse36779_shortcode_cb($atts)
{
    $atts = shortcode_atts(
        array(
            'id' => false
        ),
        $atts 
   );
    
    if(!$atts['id'])
    {   
        // no list of ids? Just send back a gallery
        return do_shortcode('[gallery]');
    }
    else
    {
        $ids = array_map('trim', explode(',', $atts['id']));
        $out = '';
        foreach($ids as $id)
        {
            if($id)
                $out .= do_shortcode(sprintf('[gallery id="%d"]', absint($id));
        }
    }
    return $out;
}
