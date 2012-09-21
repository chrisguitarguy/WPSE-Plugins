<?php
/*
Plugin Name: Rewrite Single Category
Plugin URI: http://wordpress.stackexchange.com/q/65855/6035
Description: How to rewrite a single category with rewrite rules.
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


add_action('init', 'wpse65855_rewrite');
function wpse65855_rewrite()
{
    add_rewrite_rule(
        '^photos/?$',
        'index.php?taxonomy=category&term=photos',
        'top'
    );
}

register_activation_hook(__FILE__, function() {
    wpse65855_rewrite();
    flush_rewrite_rules();
});
