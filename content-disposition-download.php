<?php    
/*
Plugin Name: Download Example
Plugin URI: http://wordpress.stackexchange.com/questions/27232/get-wordpress-login-functions-without-printing-anything
Description: An example of how to do file downloads in WordPress with `Content-Disposition`
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


add_action('init', 'wpse27232_add_rewrite');
/**
 * Adds the rewrite rule for the download.
 * 
 * @uses add_rewrite_rule
 */
function wpse27232_add_rewrite()
{
    add_rewrite_rule(
        '^download/?$',
        'index.php?file_download=true',
        'top'
   );
}


add_filter('query_vars', 'wpse27232_query_vars');
/**
 * Filter our query vars so WordPress recognizes 'file_download'
 */
function wpse27232_query_vars($vars)
{
    $vars[] = 'file_download';
    return $vars;
}


add_action('template_redirect', 'wpse27232_catch_file_dl');
/**
 * Catches when the file_download query variable is present.  Sends the content
 * header, the file, and then exits.
 */
function wpse27232_catch_file_dl()
{
    // No query var?  bail.
    if(!get_query_var('file_download')) return;
    
    // change this, obviously. Should be a path to the pdf file
    // I wrote this as a plugin, hence `plugin_dir_path`
    $f = plugin_dir_path(__FILE__) . 'your-file.pdf';
    if(file_exists($f) && is_user_logged_in())
    {
        // Do your additional checks and setup here
        
        // Send the headers
        header('Content-Type: aplication/pdf');
        header('Content-Disposition: attachment; filename=' . basename($f));
        header('Content-Length: ' . filesize($f));
        // You may want to make sure the content buffer is clear here

        // read the pdf output
        readfile($f);
        exit();
    }
    else
    {
        global $wp_query;
        $wp_query->is_404 = true;
    }
}


register_activation_hook(__FILE__, 'wpse27232_activation');
function wpse27232_activation()
{
    wpse27232_add_rewrite();
    flush_rewrite_rules();
}
