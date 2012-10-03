<?php
/*
Plugin Name: User Subdomains
Plugin URI: http://wordpress.stackexchange.com/q/66456/6035
Description: Change things around the site based on the current subdomain.
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

WPSE66456::init();

class WPSE66456
{
    // container for an instance of this class
    private static $ins;

    // The current user, based on subdomain.
    private $user = null;

    /***** Singleton Pattern *****/

    public static function init()
    {
        add_action('plugins_loaded', array(__CLASS__, 'instance'), 0);
    }

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    /**
     * Constructor.  Actions really get added here.
     *
     */
    protected function __construct()
    {
        $this->set_current_user($_SERVER['HTTP_HOST']);
        add_filter('bloginfo', array($this, 'set_tagline'), 10, 2);
        add_filter('pre_user_login', array($this, 'filter_login'));
        add_action('init', array($this, 'check_user'), 1);
    }

    protected function set_current_user($host)
    {
        if(!is_null($this->user))
            return;

        list($user, $host) = explode('.', $host, 2);

        // gets tricky here.  Where is the real site? Is it at the root domain?
        // For the purposes of this tutorial, let's assume that we're using a
        // nacked root domain for the main, no user site.

        // Make sure the $host is still a valid domain, if not we're on the root
        if(strpos($host, '.') === false)
        {
            $this->user = false;
        }
        else
        {
            if($u = get_user_by('slug', $user))
            {
                // we have a user!
                $this->user = $u;
            }
            else
            {
                // invalid user name.  Send them back to the root.
                wp_redirect("http://{$host}", 302);
                exit;

                // Or you could die here and show an error...
                // wp_die(__('Invalid User'), __('Invalid User'));
            }
        }
    }

    public function set_tagline($c, $show)
    {
        if('description' != $show || !$this->user)
            return $c;

        return 'Hello, ' . esc_html($this->user->display_name) . '!';
    }

    public function check_user()
    {
        if($this->user === false || current_user_can('manage_options'));
            return; // on the root domain or the user is an admin

        $user = wp_get_current_user();

        if(!$user || $user != $this->user)
        {
            wp_redirect(home_url());
            exit;
        }
    }

    public function filter_login($login)
    {
        // replace anything that isn't a-z and 0-9 and a dash
        $login = preg_replace('/[^a-z0-9-]/u', '', strtolower($login));

        // domains can't begin with a dash
        $login = preg_replace('/^-/u', '', $login);

        // domains can't end with a dash
        $login = preg_replace('/-$/u', '', $login);

        // probably don't want users registering the `www` user name...
        if('www' == $login)
            $login = 'www2';

        return $login;
    }
} // end WPSE66456
