<?php
/**
 * Plugin Name: Data Attr Caption
 * Plugin URI: http://wordpress.stackexchange.com/q/81532/6035
 * Description: Replace WP's caption with `data-caption` instead.
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: GPL-2.0+
 *
 * Copyright 2013 Christopher Davis <http://christopherdavis.me>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

!defined('ABSPATH') && exit;

add_filter('img_caption_shortcode', 'wpse81532_caption', 10, 3);
/**
 * The `img_caption_shortcode` callback. Actually generates the caption and
 * image to be inserted.  This is mostly copied from WP's core image shortcode
 * callback with some modifications to suit our needs.
 *
 * @param   string $ns An emptry string, not used
 * @param   array $args The shortcode args
 * @param   string|null $content The content passed to the caption shortcode.
 * @return  string
 */
function wpse81532_caption($na, $atts, $content)
{
    extract(shortcode_atts(array(
        'id'    => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    ), $atts));

    if (1 > (int) $width || empty($caption)) {
        return $content;
    }

    // add the data attribute
    $res = str_replace('<img', '<img data-caption="' . esc_attr($caption) . '"', $content);

    // the next bit is more tricky: we need to append our align class to the 
    // already exists classes on the image.
    $class = 'class=';
    $cls_pos = stripos($res, $class);

    if ($cls_pos === false) {
        $res = str_replace('<img', '<img class="' . esc_attr($align) . '"', $res);
    } else {
        $res = substr_replace($res, esc_attr($align) . ' ', $cls_pos + strlen($class) + 1, 0);
    }

    return $res;
}
