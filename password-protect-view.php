<?php
/**
 * Plugin Name: Password Protecte View
 * Plugin URI: http://wordpress.stackexchange.com/q/77865/6035
 * Description: Show a user a completely different page if the post password is required.
 * Text Domain: wpse
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: GPL-2.0+
 *
 * Copyright 2012 Christopher Davis <http://christopherdavis.me>
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
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

!defined('ABSPATH') && exit;

add_filter('template_include', 'wpse77865_hijack_template');
/**
 * Hooked into `template_redirect`.  Checks to see if we're on a singular page
 * and if it's password protected show the user a completely different page.
 *
 * @param   string $tmp The template
 * @uses    locate_template
 * @uses    is_singular
 * @uses    post_password_required
 * @return  string
 */
function wpse77865_hijack_template($tmp)
{
    if (
        is_singular() &&
        post_password_required(get_queried_object()) &&
        ($pw = locate_template('password.php'))
    ) {
        // if we're here, we are on a singular page
        // need a password and locate_template actually found
        // password.php in our child or parent theme.
        $tmp = $pw;
    }

    return $tmp;
}
