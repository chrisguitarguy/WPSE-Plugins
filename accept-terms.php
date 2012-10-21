<?php
/*
Plugin Name: Accept Terms to Read
Plugin URI: http://wordpress.stackexchange.com/q/52793/6035
Description: Make users accept terms before they can view a given page.
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

WPSE_52793::init();

class WPSE_52793
{
    /**
     * Query variable we'll use for rewrites and catching the form handler
     *
     */
    const Q_VAR = 'wpse52793_handler';

    /**
     * Form key field name.
     *
     */
    const F_KEY = 'wpse52793_accepted';

    /**
     * Form field nonce.
     *
     */
    const NONCE = 'wpse52793_fnonce';

    /**
     * Cookie key.
     *
     */
    const COOKIE = 'wpse52793_agreed';

    /**
     * Settings key.
     *
     */
    const SETTING = 'wpse52793_options';

    /********** Basic setup fluff **********/

    private static $ins = null;

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    public static function init()
    {
        add_action('plugins_loaded', array(self::instance(), '_setup'));
        register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivate'));
    }

    /**
     * Activation hook.  Calls our rewrite method to register the rules
     * then flush the rewrite rules to get them working.
     *
     * @access  public
     * @uses    flush_rewrite_rules
     * @return  void
     */
    public static function activate()
    {
        self::instance()->rewrite();
        flush_rewrite_rules();
    }

    /**
     * Deactivation hook. Remove the rewrite rules.
     *
     * @access  public
     * @uses    flush_rewrite_rules
     * @return  void
     */
    public static function deactivate()
    {
        flush_rewrite_rules();
    }

    /**
     * Hooked into `plugins_loaded`.  Actually adds the actions.
     *
     * @access  public
     * @uses    add_action
     * @uses    add_filter
     * @return  void
     */
    public function _setup()
    {
        add_action('init', array($this, 'rewrite'));
        add_filter('query_vars', array($this, 'add_var'));
        add_action('template_redirect', array($this, 'catch_handler'));
        add_action('init', array($this, 'shortcode'));
        add_action('admin_init', array($this, 'settings'));
    }

    /**
     * Registers a new rewrite rule for the form handler.
     *
     * @access  public
     * @uses    add_rewrite_rule
     * @return  void
     */
    public function rewrite()
    {
        add_rewrite_rule(
            '^terms-handler/?$',
            'index.php?' . self::Q_VAR . '=1',
            'top'
        );
    }

    /**
     * Add our custom query variable so WordPress doesn't strip it out.
     *
     * @access  public
     * @return  array The modified query variables
     */
    public function add_var($v)
    {
        $v[] = self::Q_VAR;
        return $v;
    }

    /**
     * Hooked into template redirect.  Catches our query var for the form 
     * handler and take takes care of setting a cookie and redirecting the user
     * to the appropriate page.
     *
     * @access  public
     * @return  void
     */
    public function catch_handler()
    {
        if(!get_query_var(self::Q_VAR))
            return;

        // only allow post request, check for empty post, and make sure we
        // have a page_id
        if(
            'POST' != $_SERVER['REQUEST_METHOD'] ||
            empty($_POST) ||
            empty($_POST['page_id'])
        ) {
            wp_redirect(home_url());
            exit;
        }

        // if we're here everything should be good.
        // fetch the permalink
        $r = get_permalink(absint($_POST['page_id']));

        if(!$r)
        {
            // bad permalink for some reason, bail
            wp_redirect(home_url());
            exit;
        }

        if(
            !isset($_POST[self::NONCE]) ||
            !wp_verify_nonce($_POST[self::NONCE], self::NONCE) ||
            empty($_POST[self::F_KEY])
        ) {
            // bad nonce or they didn't check the box, try again
            wp_redirect($r);
            exit;
        }

        // whew, they've agreed.  Set a cookie, and send them back to the page.
        setcookie(
            self::COOKIE,
            '1',
            strtotime('+30 Days'), // might want to change this?
            '/',
            COOKIE_DOMAIN, // WP constant
            false,
            true
        );

        wp_redirect($r);
        exit;
    }

    public function shortcode()
    {
        add_shortcode('terms_required', array($this, 'shortcode_cb'));
    }

    public function shortcode_cb($opts, $content=null)
    {
        if(!empty($_COOKIE[self::COOKIE]))
            return $content;

        // if we're here, they haven't agreed. Show the terms.
        ob_start();
        ?>
        <div class="terms-and-conditions">
            <?php echo apply_filters('wpse52793_terms', get_option(self::SETTING, '')); ?>
        </div>
        <form method="post" action="<?php echo home_url('/terms-handler/'); ?>">
            <?php wp_nonce_field(self::NONCE, self::NONCE, false); ?>
            <input type="hidden" name="page_id" value="<?php echo get_queried_object_id(); ?>" />
            <label for="agree">
                <input type="checkbox" name="<?php echo esc_attr(self::F_KEY); ?>" value="agree" id="agree" />
                <?php esc_html_e('I agree to these terms and conditions.', 'wpse'); ?>
            </label>
            <p><input type="submit" value="<?php esc_attr_e('Submit', 'wpse'); ?>" /></p>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Hooked into `admin_init` -- registers the custom setting and adds a new
     * field and section to the Options > Reading page.
     *
     * @access  public
     * @uses    register_setting
     * @uses    add_settings_section
     * @uses    add_settings_field
     * @return  void
     */
    public function settings()
    {
        register_setting(
            'reading',
            self::SETTING,
            array($this, 'validate_cb')
        );

        add_settings_section(
            'terms-conditions',
            __('Terms and Conditions', 'wpse'),
            array($this, 'section_cb'),
            'reading'
        );

        add_settings_field(
            'terms-conditions',
            __('Terms & Conditions', 'wpse'),
            array($this, 'field_cb'),
            'reading',
            'terms-conditions',
            array('label_for' => self::SETTING)
        );
    }

    /**
     * Settings validation callback.  Checks to see if the user can post
     * unfiltered html and return the raw text or a kses filter string
     * where appropriate.
     *
     * @access  public
     * @uses    current_user_can
     * @uses    wp_filter_post_kses
     * @return  string
     */
    public function validate_cb($dirty)
    {
        return current_user_can('unfiltered_html') ?
            $dirty : wp_filter_post_kses($dirty);
    }

    /**
     * Callback for the settings section.  Displays some help text.
     *
     * @access  public
     * @uses    esc_html__
     * @return  void
     */
    public function section_cb()
    {
        echo '<p class="description">',
            esc_html__('The terms and conditions that get displayed when a user '.
                'visits a page protected by the `terms_required` shortcode.', 'wpse'),
            '</p>';
    }

    /**
     * Callback for the settings field.  Creates a new editor on the screen.
     *
     * @access  public
     * @uses    wp_editor
     * @uses    get_option
     * @return  void
     */
    public function field_cb($args)
    {
        wp_editor(
            get_option(self::SETTING, ''),
            self::SETTING,
            array(
                'wpautop'       => false,
                'textarea_rows' => 10,
            )
        );
    }
}
