<?php
/**
 * Plugin Name: Taxonomy Post Class
 * Plugin URI: http://wordpress.stackexchange.com/q/66408/6035
 * Description: Add a taxonomy slug to a posts class.
 * Version: 1.0
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

Taxonomy_Post_Class::init();

class Taxonomy_Post_Class
{
    /**
     * The post type to which you want to add classes. CHANGE THIS.
     *
     */
    const TYPE = 'post';

    /**
     * the taxonomy whose slugs you want to add. CHANGE THIS.
     *
     */
    const TAX = 'category';

    private static $ins = null;

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    public static function init()
    {
        add_filter('post_class', array(self::instance(), 'add_class'), 10, 3);
    }

    public function add_class($classes, $cls, $post_id)
    {
        if (self::TYPE !== get_post_type($post_id)) {
            return $classes;
        }

        return array_merge($classes, $this->getSlugs($post_id));
    }

    private function getSlugs($post_id)
    {
        $terms = get_the_terms($post_id, self::TAX);

        if (!$terms || is_wp_error($terms)) {
            return array();
        }

        return wp_list_pluck($terms, 'slug');
    }
}
