<?php
/*
Plugin Name: Pages on Home
Plugin URI: http://wordpress.stackexchange.com/q/70606/6035
Description: An example of how to put pages (or any other post type) on the home page.
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

add_action('pre_get_posts', 'wpse70606_pre_posts');
/**
 * Change that query! No need to return anything $q is an object passed by 
 * reference {@link http://php.net/manual/en/language.oop5.references.php}.
 *
 * @param   WP_Query $q The query object.
 * @return  void
 */
function wpse70606_pre_posts($q)
{
    // bail if it's the admin, not the main query or isn't the (posts) page.
    if(is_admin() || !$q->is_main_query() || !is_home())
        return;

    // whatever type(s) you want.
    $q->set('post_type', array('post', 'page'));
}
