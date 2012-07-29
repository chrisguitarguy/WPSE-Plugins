<?php
/*
Plugin Name: Easy Stick Post (for WPSE 58818)
Plugin URI: http://wordpress.stackexchange.com/q/58818/6035
Description: Add a stick/unstick post link to the admin bar of WordPress.
Version: 1.0
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

class WPSE_58818_Stick_Post
{
    /**
     * Ajax nonce.
     *
     * @since   1.0
     */
    const NONCE = 'wpse58818_nonce_';

    /**
     * Unstick ajax action
     *
     * @since   1.0
     */
    const UNSTICK = 'wpse58818_unstick';

    /**
     * Stick Ajax action
     *
     * @since   1.0
     */
    const STICK = 'wpse58818_stick';

    /**
     * Adds actions and such.
     *
     * @since   1.0
     * @access  public
     * @uses    add_action
     */
    public static function init()
    {
        add_action(
            'template_redirect',
            array(__CLASS__, 'template_r')
        );

        // Ajax actions
        add_action(
            'wp_ajax_' . self::STICK,
            array(__CLASS__, 'stick')
        );

        add_action(
            'wp_ajax_' . self::UNSTICK,
            array(__CLASS__, 'unstick')
        );
    }

    /**
     * Hooked into `template_redirect`.  Adds the admin bar stick/unstick
     * button if we're on a single post page and the current user can edit
     * the post
     * 
     * @since   1.0
     * @access  public
     * @uses    add_action
     */
    public static function template_r()
    {
        if(
            !is_single() ||
            !current_user_can('edit_post', get_queried_object_id())
        ) return; // not a single post or the user can't edit it

        // Hook into admin_bar_menu to add stuff
        add_action(
            'admin_bar_menu',
            array(__CLASS__, 'menu'),
            100
        );

        // Hook into the footer and spit out some JavaScript
        add_action(
            'wp_footer',
            array(__CLASS__, 'footer')
        );
    }

    /**
     * Hooked into `admin_bar_menu`.  Adds our stick/unstick node.
     *
     * @since   1.0
     * @access  public
     */
    public static function menu($mb)
    {
        // get the current post ID
        $post_id = get_queried_object_id();

        $mb->add_node(array(
            'id'    => 'wpse58818-sticker',
            'meta'  => array(
                'class' => 'wpse58818-sticker',
                'title' => is_sticky($post_id) ? 'unstick' : 'stick'
            ),
            'title' => is_sticky($post_id) ? __('Unstick') : __('Stick'),
            'href'  => self::get_url($post_id)
        ));
    }

    /**
     * Hooked into `wp_footer`.  Spits out a bit of JS to stick/unstick a post
     *
     * @since   1.0
     * @access  public
     */
    public static function footer()
    {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.wpse58818-sticker a').on('click', function(e) {
                e.preventDefault();
                var action = $(this).attr('title');
                var that = this;
                $.get(
                    $(this).attr('href'),
                    {},
                    function(data) {
                        if('0' == data)
                        {
                            console.log(data);
                            alert('<?php echo esc_js(__('An error occurred')); ?>');
                            return;
                        }

                        $(that).attr('href', data);
                        if('stick' == action) {
                            $(that).html('<?php echo esc_js(__('Unstick')); ?>');
                            $(that).attr('title', 'unstick');
                        } else {
                            $(that).html('<?php echo esc_js(__('Stick')); ?>');
                            $(that).attr('title', 'stick');
                        }
                    }
                );
            });
        });
        </script>
        <?php
    }

    /**
     * Ajax callback for the stick function
     *
     * @since   1.0
     * @access  public
     */
    public static function stick()
    {
        $post_id = self::can_ajax();

        stick_post($post_id);

        echo self::get_url($post_id);
        die();
    }

    /**
     * Ajax callback for the unstick function
     *
     * @since   1.0
     * @access  public
     * @uses    unstick_post
     */
    public static function unstick()
    {
        $post_id = self::can_ajax();

        // nonces checked, everything is good to go. Unstick!
        unstick_post($post_id);

        echo self::get_url($post_id);
        die();
    }

    /**
     * Check to see if the current user can ajax.  Returns the post ID to 
     * stick/unstick if successful. Kills the program otherwise
     *
     * @since   1.0
     * @access  protected
     */
    protected static function can_ajax()
    {
        $post_id = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : '';

        if(
            !$post_id ||
            !check_ajax_referer(self::NONCE . $post_id, 'nonce', false)
        ) die('0');
        
        if(!current_user_can('edit_post', $post_id))
            die('0');

        return $post_id;
    }

    /**
     * Get an Ajax URL to use for a given post
     *
     * @since   1.0
     * @access  protected
     */
    protected static function get_url($post_id)
    {
        return add_query_arg(array(
            'post_id' => absint($post_id),
            'action'  => is_sticky($post_id) ? self::UNSTICK : self::STICK,
            'nonce'   => wp_create_nonce(self::NONCE . $post_id)
        ), admin_url('admin-ajax.php'));
    }
}  // end class

WPSE_58818_Stick_Post::init();
