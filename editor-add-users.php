<?php    
/*
Plugin Name: Editors Create Users
Plugin URI: http://wordpress.stackexchange.com/questions/42003/allow-roles-below-admin-to-add-subscribers-only
Description: Gives editors the ability to create users (but only with the role subscriber)
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

register_activation_hook(__FILE__, 'wpse42003_activation');
/*
 * Activation hook. Adds the capability for editors to create users
 *
 * @uses get_role
 */
function wpse42003_activation()
{
    foreach(array('editor', 'your_custom_role') as $r)
    {
        $role = get_role($r);
        if($role)
            $role->add_cap('create_users');
    }
}


register_deactivation_hook(__FILE__, 'wpse42003_deactivation');
/* 
 * Deactivation hook.  Removes the editor's ability to create users
 *
 * @uses get_role
 */
function wpse42003_deactivation()
{
    foreach(array('editor', 'your_custom_role') as $r)
    {
        $role = get_role($r);
        if($role)
            $role->remove_cap('create_users');
    }
}


add_filter('editable_roles', 'wpse42003_filter_roles');
/*
 * Modify the editable roles to include only subscribers.
 *
 * @uses wp_get_current_user
 */
function wpse42003_filter_roles($roles)
{
    $user = wp_get_current_user();
    if(in_array('editor', $user->roles) || in_array('your_custom_role', $user->roles))
    {
        $tmp = array_keys($roles);
        foreach($tmp as $r)
        {
            if('subscriber' == $r) continue;
            unset($roles[$r]);
        }
    }
    return $roles;
}
