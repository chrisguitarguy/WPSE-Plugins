<?php
/**
 * Plugin Name: User Contact Rel Author
 * Plugin URI: http://wordpress.stackexchange.com/q/83193/6035
 * Description: Two example implementations of rel="author"
 * Text Domain: wpse
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * License: MIT
 *
 * Copyright (c) 2012 Christopher Davis <http://christopherdavis.me>
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
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

!defined('ABSPATH') && exit;


add_filter('user_contactmethods', 'wpse83193_user_contactmethods');
/**
 * Adds a Google+ field to the contact methods area in the user's profile.
 *
 * @param   array $contact key => label pairs of contact methods
 * @return  array Same as the input: key => label pairs
 */
function wpse83193_user_contactmethods($contact)
{
    $contact['wpse83193_google'] = __('Google+', 'wpse');
    return $contact;
}

add_action('wp_head', 'wpse83193_output_contactmethods');
/**
 * Spit out the rel=author link tag in the <head> section.
 *
 * @uses    is_singular
 * @uses    get_user_meta
 * @return  void
 */
function wpse83193_output_contactmethods()
{
    if (!is_singular()) {
        return;
    }

    if ($rel = get_user_meta(get_queried_object()->post_author, 'wpse83193_google', true)) {
        printf('<link rel="author" href="%s" />', esc_url($rel));
    }
}
