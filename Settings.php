<?php

namespace nyco\WpGtfsImport\Settings;

if (!is_admin()) {
  return;
}

/**
 * Dependencies
 */

use nyco\WpGtfsImport\Utilities as Utilities;

/**
 * Constants
 */

const TITLE = 'GTFS Import';
const ID = 'gtfs_import';
const CAPABILITY = 'manage_options';

/**
 * Variables
 */

/** @var array The setting pages. */
$pages = array(
  [
    'page_title' => TITLE,
    'menu_title' => TITLE,
    'capability' => CAPABILITY,
    'menu_slug' => ID,
    'extra_action' => function () {
      gtfs_import_settings_template(array('id' => 'gtfs_download_feeds'));
      gtfs_import_settings_template(array('id' => 'gtfs_import_feeds'));
    }
  ]
);

/** @var array The Settings sections. */
$sections = array(
  [
    'id' => 'gtfs_feeds',
    'title' => 'Step 1. Feed Settings',
    'callback' => function () {
    },
    'page' => ID
  ]
);

/** @var array The settings fields. */
$settings = array(
  [
    'id' => 'gtfs_feed',
    'title' => 'GTFS Feeds (URL)',
    'callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_settings_template',
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_feed',
      'placeholder' => 'URL(S)'
    ],
    'type' => 'string',
    'description' => '',
    'sanitize_callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_sanitize_callback',
    'show_in_rest' => false,
    'default' => ''
  ],
  [
    'id' => 'gtfs_download_path',
    'title' => 'Download Path',
    'callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_settings_template',
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_download_path',
      'placeholder' => 'gtfs-data/'
    ],
    'type' => 'integer',
    'description' => '',
    'sanitize_callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_sanitize_callback',
    'show_in_rest' => false,
    'default' => 'gtfs-data'
  ],
  [
    'id' => 'gtfs_flatten_extracted_files',
    'title' => 'Flatten Extracted Files',
    'callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_settings_template',
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_flatten_extracted_files',
      'placeholder' => ''
    ],
    'type' => 'integer',
    'description' => '',
    'sanitize_callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_sanitize_callback',
    'show_in_rest' => false,
    'default' => 'gtfs-data'
  ],
  [
    'id' => 'gtfs_fields',
    'title' => 'GTFS Fields to Import',
    'callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_settings_template',
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_fields',
      'placeholder' => ''
    ],
    'type' => 'integer',
    'description' => '',
    'sanitize_callback' => 'nyco\WpGtfsImport\Settings\gtfs_import_sanitize_callback',
    'show_in_rest' => false,
    'default' => ''
  ]
);


/**
 * Functions
 */

/**
 * Import templates based on argument callback parameters
 * @param  [array] $args Arguments supplied via WP callback
 */
function gtfs_import_settings_template($args) {
  $template = __DIR__ . '/views/' . $args['id'] . '.php';
  if (file_exists($template)) {
    require $template;
  }
}

/**
 * Sanitize function for WP callback
 * @param  [array] $args Arguments supplied via WP callback
 * @param  [array]       Arguments supplied via WP callback
 */
function gtfs_import_sanitize_callback($args) {
  return $args;
}

/**
 * Triggered before any other hook when a user accesses the admin area.
 * Creates the plugin settings sections then the plugin settings fields
 * and registers them.
 * @param  [array] $sections An array of sections to create.
 * @param  [array] $settings An array of settings to create.
 */
add_action('admin_init', function () use ($sections, $settings) {
  foreach ($sections as $section) {
    add_settings_section(
      $section['id'], $section['title'], $section['callback'], $section['page']
    );
  }

  foreach ($settings as $setting) {
    add_settings_field(
      $setting['id'], $setting['title'], $setting['callback'], $setting['page'],
      $setting['section'], $setting['args']
    );

    register_setting(ID, $setting['id'], array(
      'type' => $setting['type'],
      'description' => $setting['description'],
      'sanitize_callback' => $setting['sanitize_callback'],
      'show_in_rest' => $setting['show_in_rest'],
      'default' => $setting['default']
    ));
  }
});

/**
 * This action is used to add extra submenus and menu options to the admin
 * panel's menu structure. It runs after the basic admin panel menu structure
 * is in place. Adds the plugin menu page and the page content.
 * @param  [array] $pages An array of pages to create.
 */
add_action('admin_menu', function () use ($pages) {
  foreach ($pages as $page) {
    add_options_page(
      $page['page_title'], $page['menu_title'], $page['capability'],
      $page['menu_slug'],
      function () use ($page) {
        echo '<div class="wrap">';
        echo '  <h1>' . TITLE . '</h1>';
        echo '  <form method="post" action="options.php">';

        do_settings_sections(ID);
        settings_fields(ID);
        submit_button();

        echo '  </form>';

        if (isset($page['extra_action'])) {
          $page['extra_action']();
        }

        echo '</div>';
      }
    );
  }
});
