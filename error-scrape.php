<?php
/**
 * Plugin Name: Error Scrape
 * Plugin URI: http://wordpress.stackexchange.com/q/24278/6035
 * Description: Add a link to "error scrape" plugins on the plugin list table.
 * Text Domain: wpse
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

add_filter('plugin_action_links', 'wpse24278_add_scrape', 10, 2);
/**
 * Add an "Error Scrape" action to inactive plugins.
 *
 * @uses    is_plugin_active
 * @return  array
 */
function wpse24278_add_scrape($actions, $plugin)
{
    global $status, $page, $s;

    // leave activate plugins alone
    if (is_plugin_active($plugin)) {
        return $actions;
    }

    // build the url, identical to the activate URL, see the
    // plugings list table for more information.
    $url = add_query_arg(array(
        'action'            => 'error_scrape',
        'plugin'            => $plugin,
        'plugin_status'     => $status,
        'paged'             => $page,
        's'                 => $s,
    ), admin_url('plugins.php'));

    // add our action.
    $actions['error_scrape'] = sprintf(
        '<a href="%s" title="%s" class="edit">%s</a>',
        wp_nonce_url($url, 'plugin-activation-error_' . $plugin), // see `wp-admin/plugins.php` for the nonce name
        esc_attr__('Check for Errors', 'wpse'),
        esc_html__('Error Scrape', 'wpse')
    );

    return $actions;
}
