<?php
/*
Plugin Name: Fix WPSEO Date
Plugin URI: http://wordpress.stackexchange.com/q/57195/6035
Description: Change the timezone of WordPress SEO's sitemap dates
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

add_filter('date_i18n', 'wpse57195_filter_date', 10, 4);
/**
 * Changes the date on WordPress SEO's sitemaps
 *
 * @return string The Date
 */
function wpse57195_filter_date($date, $format, $timestamp, $gmt)
{
    // if this isn't a sitemap page, bail
    if(!get_query_var('sitemap'))
        return $date;

    // W3C Time format with -05:00 for central time
    // NOTE: this doesn't account for daylight saving time
    $f = 'Y-m-d\TH:i:s-05:00';
    return $gmt ? gmdate($f, $timestamp) : date($f, $timestamp);
}
