<?php

namespace NYCO\GTFS\Posts;

/**
 * Dependencies
 */

use NYCO\GTFS\Database as Database;

/**
 * Constants
 */

const FIELDS_OPTION_ID = 'gtfs_fields';
const POST_TYPE_PREFIX = 'gtfs_';

/**
 * Create custom post types based on the fields we'd like to import.
 */
add_action('init', function () {
  $types = array_keys(get_option(FIELDS_OPTION_ID, []));

  foreach ($types as $type) {
    $slug = $type;
    $nicename = ucwords(str_replace('_', ' ', $type));

    register_post($slug, $nicename);

    // Create Advanced Custom Fields
    if (function_exists('acf_add_local_field_group')) {
      add_field_group($slug, $nicename);
    }
  }
});

/**
 * Register the fields custom post type
 * @param [string] $slug     The feed field name (ex. stop_times)
 * @param [string] $nicename The feed field nice name (ex. Stop Times)
 */
function register_post($slug, $nicename) {
  register_post_type(
    POST_TYPE_PREFIX . $slug,
    array(
      'labels' => array(
        'name' => __('GTFS ' . $nicename),
        'singular_name' => __($nicename),
        'all_items' => __('All ' . $nicename),
        'add_new' => __('Add New'),
        'add_new_item' => __('Add New ' . $nicename),
        'edit' => __('Edit'),
        'edit_item' => __('Edit ' . $nicename),
        'new_item' => __('New ' . $nicename),
        'view_item' => __('View ' . $nicename),
        'search_items' => __('Search ' . $nicename),
      ),
      'description' => __('GTFS ' . $nicename),
      'public' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_rest' => true,
      'hierarchical' => false,
      'supports' => array('title', 'slug'),
      'capability_type' => 'post',
      'menu_icon' => 'dashicons-location'
    )
  );
}

/**
 * Create an Advanced Custom Field group then create the fields
 * @param [string] $slug     The feed field name (ex. stop_times)
 * @param [string] $nicename The feed field nice name (ex. Stop Times)
 */
function add_field_group($slug, $nicename) {
  acf_add_local_field_group(array(
    'key' => __("{$slug}_fields"),
    'title' => __("$nicename Fields"),
    'fields' => array(),
    'location' => array(
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => POST_TYPE_PREFIX . $slug,
        )
      )
    )
  ));

  add_fields($slug, $nicename);
}

/**
 * Add Advanced Custom Fields from based on the schema
 * @param [string] $slug     The slug of the feed field
 */
function add_fields($slug) {
  $skip = ['id', 'PRIMARY'];
  $schema = Database\get_schema($slug);

  foreach ($schema as $field) {
    $field = explode(' ', $field);
    $name = $field[0];
    $label = ucwords(str_replace('_', ' ', $field[0]));

    if (in_array($name, $skip)) {
      continue;
    }

    acf_add_local_field(array(
      'key' => $name,
      'label' => _($label),
      'name' => $name,
      'type' => 'text',
      'parent' => "{$slug}_fields"
    ));
  }
}
