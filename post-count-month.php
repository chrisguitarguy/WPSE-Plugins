<?php
/*
Plugin Name: Post Count by Month
Plugin URI: http://wordpress.stackexchange.com/q/60859/6035
Description: A short code that outputs a list of month names and post counts
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

add_action('init', 'wpse60859_register_shortcode');
/**
 * Registers the shortcode
 * 
 * @uses    add_shortcode
 */
function wpse60859_register_shortcode()
{
    add_shortcode(
        'posts_per_month',
        'wpse60859_shortcode_cb'
    );

    add_shortcode(
        'posts_per_month_last',
        'wpse60859_shortcode_alt_cb'
    );
}


/**
 * The shortcode callback function.
 *
 * Usage:
 *      [posts_per_month year="2012"]
 *
 * @uses    shortcode_atts
 * @uses    date_i18n
 */
function wpse60859_shortcode_cb($args)
{
    global $wpdb;

    $args = shortcode_atts(array(
        'year' => false
    ), $args);

    $year = absint($args['year']);

    // year is a no go?  bail.
    if(!$year)
        return '';

    $res = $wpdb->get_results($wpdb->prepare(
        "SELECT MONTH(post_date) AS post_month, count(ID) AS post_count from " .
        "{$wpdb->posts} WHERE post_status = 'publish' AND YEAR(post_date) = %d " .
        "GROUP BY post_month;", $year
    ), OBJECT_K);

    // We didn't get any results.  Something might be wrong?
    if(!$res)
        return '';

    // build the display
    $out = '<ul>';
    foreach(range(1, 12) as $m)
    {
        $month = date_i18n('F', mktime(0, 0, 0, $m, 1));
        $out .= sprintf(
            '<li>%s %d</li>',
            $month,
            isset($res[$m]) ? $res[$m]->post_count : 0
        );
    }
    $out .= '</ul>';

    return $out;
}


/**
 * Callback for displaying the last twelve months of posts
 *
 * @uses $wpdb
 */
function wpse60859_shortcode_alt_cb()
{
    global $wpdb;
    $res = $wpdb->get_results(
        "SELECT MONTH(post_date) as post_month, COUNT(ID) as post_count " .
        "FROM {$wpdb->posts} " .
        "WHERE post_date BETWEEN DATE_SUB(NOW(), INTERVAL 12 MONTH) AND NOW() " .
        "AND post_status = 'publish' " .
        "GROUP BY post_month ORDER BY post_date ASC", OBJECT_K
    );

    print_r($res);

    $cur = absint(date('n'));
    if($cur > 1)
    {
        $looper = array_merge(range($cur, 12), range(1, $cur-1));
    }
    else
    {
        $looper = range(1, 12);
    }

    $out = '<ul>';
    foreach($looper as $m)
    {
        $month = date_i18n('F', mktime(0, 0, 0, $m, 1));
        $out .= sprintf(
            '<li>%s %d</li>',
            $month,
            isset($res[$m]) ? $res[$m]->post_count : 0
        );
    }
    $out .= '</ul>';

    return $out;
}
