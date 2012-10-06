<?php
/*
Plugin Name: Per User Feeds
Plugin URI: http://wordpress.stackexchange.com/q/46074/6035
Description: Allow users to create their own feeds.
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


Per_User_Feeds::init();

class Per_User_Feeds
{
    // Where we'll store the user cats
    const META_KEY = '_per_user_feeds_cats';

    // Nonce for the form fields
    const NONCE = '_user_user_feeds_nonce';

    // Taxonomy to use
    const TAX = 'category';

    // The query variable for the rewrite
    const Q_VAR = 'puf_feed';

    // container for the instance of this class
    private static $ins = null;

    // container for the terms allowed for this plugin
    private static $terms = null;

    public static function init()
    {
        add_action('plugins_loaded', array(__CLASS__, 'instance'));
        register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
    }

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    protected function __construct()
    {
        add_action('show_user_profile', array($this, 'field'));
        add_action('edit_user_profile', array($this, 'field'));
        add_action('personal_options_update', array($this, 'save'));
        add_action('edit_user_profile_update', array($this, 'save'));
        add_action('init', array($this, 'rewrite'));
        add_filter('query_vars', array($this, 'query_var'));
        add_action('template_redirect', array($this, 'catch_feed'));
    }

    public function field($user)
    {
        wp_nonce_field(self::NONCE . $user->ID, self::NONCE, false);

        echo '<h4>', esc_html__('Feed Categories', 'per-user-feed'), '</h4>';

        if($terms = self::get_terms())
        {
            $val = self::get_user_terms($user->ID);
            printf('<select name="%1$s[]" id="%1$s" multiple="multiple">', esc_attr(self::META_KEY));
            echo '<option value="">', esc_html__('None', 'per-user-feed'), '</option>';
            foreach($terms as $t)
            {
                printf(
                    '<option value="%1$s" %3$s>%2$s</option>',
                    esc_attr($t->term_id),
                    esc_html($t->name),
                    in_array($t->term_id, $val) ? 'selected="selected"' : ''
                );
            }
            echo '</select>';
        }
    }

    public function save($user_id)
    {
        if(
            !isset($_POST[self::NONCE]) ||
            !wp_verify_nonce($_POST[self::NONCE], self::NONCE . $user_id)
        ) return;

        if(!current_user_can('edit_user', $user_id))
            return;

        if(!empty($_POST[self::META_KEY]))
        {
            $allowed = array_map(function($t) {
                return $t->term_id;
            }, self::get_terms());

            // PHP > 5.3: Make sure the items are in our allowed terms.
            $res = array_filter(
                (array)$_POST[self::META_KEY],
                function($i) use ($allowed) {
                    return in_array($i, $allowed);
                }
            );

            update_user_meta($user_id, self::META_KEY, array_map('absint', $res));
        }
        else
        {
            delete_user_meta($user_id, self::META_KEY);
        }
    }

    public function rewrite()
    {
        add_rewrite_rule(
            '^user-feed/(\d+)/?$',
            'index.php?' . self::Q_VAR . '=$matches[1]',
            'top'
        );
    }

    public function query_var($v)
    {
        $v[] = self::Q_VAR;
        return $v;
    }

    public function catch_feed()
    {
        $user_id = get_query_var(self::Q_VAR);

        if(!$user_id)
            return;

        if($q = self::get_user_query($user_id))
        {
            global $wp_query;
            $wp_query = $q;

            // kind of lame: anon function on a filter...
            add_filter('wp_title_rss', function($title) use ($user_id) {
                $title = ' - ' . __('User Feed', 'per-user-feed');

                if($user = get_user_by('id', $user_id))
                    $title .= ': ' . $user->display_name;

                return $title;
            });
        }

        // maybe want to handle the "else" here?

        // see do_feed_rss2
        load_template( ABSPATH . WPINC . '/feed-rss2.php' );
        exit;
    }

    /***** Helpers *****/

    /**
     * Get the categories available for use with this plugin.
     *
     * @uses    get_terms
     * @uses    apply_filters
     * @return  array The categories for use
     */
    public static function get_terms()
    {
        if(is_null(self::$terms))
            self::$terms = get_terms(self::TAX, array('hide_empty' => false));

        return apply_filters('per_user_feeds_terms', self::$terms);
    }

    /**
     * Get the feed terms for a given user.
     *
     * @param   int $user_id The user for which to fetch terms
     * @uses    get_user_meta
     * @uses    apply_filters
     * @return  mixed The array of allowed term IDs or an empty string
     */
    public static function get_user_terms($user_id)
    {
        return apply_filters('per_user_feeds_user_terms',
            get_user_meta($user_id, self::META_KEY, true), $user_id);
    }

    /**
     * Get a WP_Query object for a given user.
     *
     * @acces   public
     * @uses    WP_Query
     * @return  object WP_Query
     */
    public static function get_user_query($user_id)
    {
        $terms = self::get_user_terms($user_id);

        if(!$terms)
            return apply_filters('per_user_feeds_query_args', false, $terms, $user_id);

        $args = apply_filters('per_user_feeds_query_args', array(
            'tax_query' => array(
                array(
                    'taxonomy'  => self::TAX,
                    'terms'     => $terms,
                    'field'     => 'id',
                    'operator'  => 'IN',
                ),
            ),
        ), $terms, $user_id);

        return new WP_Query($args);
    }

    /**
     * Activation hook.
     *
     * @uses    flush_rewrite_rules
     * @return  void
     */
    public static function activate()
    {
        self::instance()->rewrite();
        flush_rewrite_rules();
    }
}
