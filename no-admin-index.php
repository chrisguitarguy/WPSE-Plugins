<?php
/**
 * Plugin Name: No Admin Index
 * Plugin URI: http://wordpress.stackexchange.com/q/206466/6035
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: MIT
 *
 * Copyright (c) 2015 Christopher Davis <http://christopherdavis.me>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @category    WordPress
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2015 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

!defined('ABSPATH') && exit;

function _wpse206466_can_view()
{
    // or any other admin level capability
    return current_user_can('manage_option');
}

add_action('load-index.php', 'wpse206466_load_index');
function wpse206466_load_index()
{
    if (!_wpse206466_can_view()) {
        $qs = empty($_GET) ? '' : '?'.http_build_query($_GET);
        wp_safe_redirect(admin_url('profile.php').$qs);
        exit;
    }
}

add_action('admin_menu', 'wpse206466_remove_index');
function wpse206466_remove_index()
{
    if (!_wpse206466_can_view()) {
        remove_menu_page('index.php');
    }
}
