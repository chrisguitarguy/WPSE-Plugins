<?php
/**
 * Plugin Name: Per Post Nav Menu
 * Plugin URI: http://wordpress.stackexchange.com/q/85243/6035
 * Description: Change nav menu based on theme location and a per-post selection.
 * Version: 1.0
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

PerPostNavMenu::init();

class PerPostNavMenu
{
    const NONCE = 'wpse85243_nav_nonce';
    const FIELD = '_wpse85243_per_post_menu';
    const LOC   = 'primary'; // the location for twenty twelve

    private static $ins = null;

    public static function instance()
    {
        if (is_null(self::$ins)) {
            self::$ins = new self;
        }

        return self::$ins;
    }

    public static function init()
    {
        add_action('plugins_loaded', array(self::instance(), '_setup'));
    }

    public function _setup()
    {
        add_action('add_meta_boxes', array($this, 'addBox'));
        add_action('save_post', array($this, 'save'), 10, 2);
        add_filter('wp_nav_menu_args', array($this, 'switchMenu'));
    }

    public function addBox($post_type)
    {
        if (!post_type_exists($post_type)) {
            return;
        }

        add_meta_box(
            'wpse85243_nav_menu',
            __('Nav Menu', 'wpse'),
            array($this, 'boxCallback'),
            $post_type,
            'side',
            'low'
        );
    }

    public function boxCallback($post)
    {
        $menu = get_post_meta($post->ID, static::FIELD, true);

        wp_nonce_field(static::NONCE . $post->ID, static::NONCE, false);

        printf(
            '<label for="%s">%s</label>',
            esc_attr(static::FIELD),
            esc_html__('Nav Menu', 'wpse')
        );

        echo '<br />';

        printf('<select name="%1$s" id="%1$s">', esc_attr(static::FIELD));

        foreach ($this->getNavMenus() as $id => $name) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($id),
                selected($menu, $id, false),
                esc_html($name)
            );
        }

        echo '</select>';
    }

    public function save($post_id, $post)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (
            !isset($_POST[static::NONCE]) ||
            !wp_verify_nonce($_POST[static::NONCE], static::NONCE . $post_id)
        ) {
            return;
        }

        $type = get_post_type_object($post->post_type);
        if (!current_user_can($type->cap->edit_post, $post_id)) {
            return;
        }

        $menu = isset($_POST[static::FIELD]) ? $_POST[static::FIELD] : false;

        if ($menu && '-' !== $menu) {
            update_post_meta($post_id, static::FIELD, absint($menu));
        } else {
            delete_post_meta($post_id, static::FIELD);
        }
    }

    public function switchMenu($args)
    {
        // we can only deal with singular pages
        if (!is_singular()) {
            return;
        }

        $switch = apply_filters(
            'per_post_nav_menus_switch',
            isset($args['theme_location']) && static::LOC === $args['theme_location'],
            $args
        );

        // if we're allowed to switch, the the `menu` argument to
        // the correct menu ID.
        if ($switch) {
            $menu = get_post_meta(get_queried_object_id(), static::FIELD, true);

            if ('-' !== $menu) {
                $args['menu'] = absint($menu);
            }
        }

        return $args;
    }

    private function getNavMenus()
    {
        $terms = get_terms('nav_menu');

        $menus = array('-' => __('Default', 'wpse'));
        if ($terms && !is_wp_error($terms)) {
            foreach($terms as $t) {
                $menus[$t->term_id] = $t->name;
            }
        }

        return apply_filters('per_post_nav_menus_list', $menus);
    }
}
