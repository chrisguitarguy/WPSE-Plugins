<?php
/*
Plugin Name: WP_Filesystem Example
Plugin URI: http://wordpress.stackexchange.com/q/74395/6035
Description: How to use the WP Filesystem API
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

!defined('ABSPATH') && exit;

add_action('admin_menu', 'wpse74395_add_menu_page');
function wpse74395_add_menu_page()
{
    add_options_page(
        __('Filesystem', 'wpse'),
        __('Filesystem', 'wpse'),
        'manage_options',
        'wpse-filesystem',
        'wpse74395_page_cb'
    );
}

function wpse74395_page_cb()
{
    if(wpse74395_use_filesystem())
        return;

    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('Filesystem Practice', 'wpse'); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('wpse74395-nonce'); ?>
            <p><input name="save" type="submit" value="<?php esc_attr_e('Go!', 'wpse'); ?>" class="button-primary" /></p>
        </form>
    </div>
    <?php
}

function wpse74395_use_filesystem()
{
    if(empty($_POST['save']))
        return false;

    // can we do this?
    check_admin_referer('wpse74395-nonce');

    // try to get the filesystem credentials.
    $url = wp_nonce_url(admin_url('options-general.php?page=wpse-filesystem'), 'wpse74395-nonce');
    if(false === ($creds = request_filesystem_credentials($url)))
    {
        // we didn't get creds...
        // The user will see a form at this point, so stop the
        // rest of the page.
        return true; 
    }

    // use WP_Filesystem to check initialize the global $wp_filesystem
    if(!WP_Filesystem($creds))
    {
        // didn't work, try again!
        request_filesystem_credentials($url);
        return true;
    }

    global $wp_filesystem;

    // get your file path
    $fp = WP_CONTENT_DIR . '/test.txt';

    // do your thing.
    if($wp_filesystem->exists($fp))
    {
        $res = $wp_filesystem->get_contents($fp);
        var_dump($res);
        return true;
    }

    return false;
}
