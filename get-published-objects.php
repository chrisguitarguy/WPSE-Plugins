<?php
/*
Plugin Name: Get Only Published Objects
Plugin URI: 
Description: 
Version: 
Text Domain: 
Domain Path: 
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


function wpse29749_get_objects_in_term( $term_ids, $taxonomies, $args = array() ) {
	global $wpdb;

	if ( ! is_array( $term_ids ) )
		$term_ids = array( $term_ids );

	if ( ! is_array( $taxonomies ) )
		$taxonomies = array( $taxonomies );

	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) )
			return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
	}

	$defaults = array( 'post_status' => 'publish', 'order' => 'ASC' );
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$order = ( 'desc' == strtolower( $order ) ) ? 'DESC' : 'ASC';

	$term_ids = array_map('intval', $term_ids );

	$taxonomies = "'" . implode( "', '", $taxonomies ) . "'";
	$term_ids = "'" . implode( "', '", $term_ids ) . "'";

    $object_ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID from $wpdb->posts WHERE ID IN (
            SELECT tr.object_id FROM $wpdb->term_relationships
            AS tr INNER JOIN $wpdb->term_taxonomy AS tt
            ON tr.term_taxonomy_id = tt.term_taxonomy_id 
            WHERE tt.taxonomy IN ($taxonomies) 
            AND tt.term_id IN ($term_ids)
        ) AND post_status = %s
        ORDER BY ID $order", $post_status ) );

	if ( ! $object_ids )
		return array();

	return $object_ids;
}

add_action('template_redirect', 'wpse29749_test');
function wpse29749_test()
{
    echo '<pre>';
    var_dump(wpse29749_get_objects_in_term(array(1), 'category', array(
        'post_status' => 'publish'
    )));
    echo '</pre>';
}
