<?php    
/*
Plugin Name: Fix Github SSL
Description: Allow WordPress to make requests to Github without worrying about verifying the SSL cert
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

add_action('http_request_args', 'cd_allow_github_ssl', 10, 2);
function cd_allow_github_ssl($args, $url) 
{
	if(preg_match('#^https://github.com#i', $url))
	{
		$args['sslverify'] = false;
	}
	return $args;
}
