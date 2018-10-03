<?php

namespace nyco\WpGtfsImport\Database;

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

const FIELDS_OPTION_ID = 'gtfs_fields';
const DATABASE_VERSION_OPTION_ID = 'gtfs_import_db_ver';
const DATABASE_VERSION = "0.0.1";
const FEED_TABLE = 'gtfs_feed';
const FILE_FORMAT = '.txt';
const SCHEMA = array(
  'agency' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'agency_id VARCHAR(255)',
    'agency_name VARCHAR(255)',
    'agency_url VARCHAR(255)',
    'agency_timezone VARCHAR(255)',
    'agency_lang VARCHAR(255)',
    'agency_phone VARCHAR(255)',
    'agency_fare_url VARCHAR(255)',
    'agency_email VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'stops' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'stop_id VARCHAR(255)',
    'stop_code VARCHAR(255)',
    'stop_name VARCHAR(255)',
    'stop_desc VARCHAR(255)',
    'stop_lat DECIMAL(8,6)',
    'stop_lon DECIMAL(8,6)',
    'zone_id VARCHAR(255)',
    'stop_url VARCHAR(255)',
    'location_type INT(1)',
    'parent_station VARCHAR(255)',
    'wheelchair_boarding INT(1)',
    'PRIMARY KEY (id)'
  ],
  'routes' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'route_id VARCHAR(255)',
    'agency_id VARCHAR(255)',
    'route_short_name VARCHAR(255)',
    'route_long_name VARCHAR(255)',
    'route_desc VARCHAR(255)',
    'route_type INT(1)',
    'route_url VARCHAR(255)',
    'route_color VARCHAR(255)',
    'route_text_color VARCHAR(255)',
    'route_sort_order VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'trips' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'route_id VARCHAR(255)',
    'service_id VARCHAR(255)',
    'trip_id VARCHAR(255)',
    'trip_headsign VARCHAR(255)',
    'trip_short_name VARCHAR(255)',
    'direction_id INT(1)',
    'block_id VARCHAR(255)',
    'shape_id VARCHAR(255)',
    'wheelchair_accessible INT(1)',
    'bikes_allowed INT(1)',
    'PRIMARY KEY (id)'
  ],
  'stop_times' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'trip_id VARCHAR(255)',
    'arrival_time VARCHAR(255)',
    'departure_time VARCHAR(255)',
    'stop_id VARCHAR(255)',
    'stop_sequence VARCHAR(255)',
    'stop_headsign VARCHAR(255)',
    'pickup_type INT(1)',
    'drop_off_type INT(1)',
    'shape_dist_traveled INT(1)',
    'timepoint INT(1)',
    'PRIMARY KEY (id)'
  ],
  'calendar' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'service_id VARCHAR(255)',
    'monday VARCHAR(255)',
    'tuesday VARCHAR(255)',
    'wednesday VARCHAR(255)',
    'thursday VARCHAR(255)',
    'friday VARCHAR(255)',
    'saturday VARCHAR(255)',
    'sunday VARCHAR(255)',
    'start_date VARCHAR(255)',
    'end_date VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'calendar_dates' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'service_id VARCHAR(255)',
    'date VARCHAR(255)',
    'exception_type INT(1)',
    'PRIMARY KEY (id)'
  ],
  'fare_attributes' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'fare_id VARCHAR(255)',
    'price VARCHAR(255)',
    'currency_type VARCHAR(255)',
    'payment_method INT(1)',
    'transfers INT(1)',
    'agency_id VARCHAR(255)',
    'transfer_duration VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'fare_rules' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'fare_id VARCHAR(255)',
    'route_id VARCHAR(255)',
    'origin_id VARCHAR(255)',
    'destination_id VARCHAR(255)',
    'contains_id VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'shapes' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'shape_id VARCHAR(255)',
    'shape_pt_lat VARCHAR(255)',
    'shape_pt_lon VARCHAR(255)',
    'shape_pt_sequence VARCHAR(255)',
    'shape_dist_traveled VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'frequencies' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'trip_id VARCHAR(255)',
    'start_time VARCHAR(255)',
    'end_time VARCHAR(255)',
    'headway_secs VARCHAR(255)',
    'exact_times INT(1)',
    'PRIMARY KEY (id)'
  ],
  'transfers' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'from_stop_id VARCHAR(255)',
    'to_stop_id VARCHAR(255)',
    'transfer_type VARCHAR(255)',
    'min_transfer_time VARCHAR(255)',
    'PRIMARY KEY (id)'
  ],
  'feed_info' => [
    'id mediumint(9) NOT NULL AUTO_INCREMENT',
    'feed_publisher_name VARCHAR(255)',
    'feed_publisher_url VARCHAR(255)',
    'feed_lang VARCHAR(255)',
    'feed_start_date VARCHAR(255)',
    'feed_end_date VARCHAR(255)',
    'feed_version VARCHAR(255)',
    'PRIMARY KEY (id)'
  ]
);

/**
 * Functions
 */

/**
 * Returns the schema for database fields
 * @param  [string] $field The array key in the schema to return
 * @return [array]         The field schema
 */
function get_schema($field = null) {
  if (null !== $field) {
    return SCHEMA[$field];
  }
  return SCHEMA;
}

/**
 * This is the "Import All Action"
 */
add_action('admin_action_wp_gtfs_import_all', function () {
  $nonce = 'wp_gtfs_import_all_nonce';
  $action = 'wp_gtfs_import_all';

  if (wp_verify_nonce($_POST[$nonce], $action)) {
    exit;
  }

  import_action(true);
});

/**
 * This adds actions for importing the data for individual feeds based
 * on the downloaded data.
 */
Utilities\parse_directories(function ($path, $name) {
  add_action('admin_action_wp_gtfs_import_' . $name, function () use ($name) {
    $nonce = 'wp_gtfs_import_' . $name . '_nonce';
    $action = 'wp_gtfs_import_' . $name;

    if (wp_verify_nonce($_POST[$nonce], $action)) {
      exit;
    }

    import_action($name);
  });
}, null);

/**
 * [import_action description]
 * @param  [string] $name The name of the feed to import
 */
function import_action($name) {
  // Create database tables
  $tables = array_keys(get_option(FIELDS_OPTION_ID, []));
  foreach ($tables as $table) {
    create_table($table);
  }

  // Populate the database with data
  Utilities\parse_directories(null, function ($path, $file) use ($name) {
    if ($name || basename(str_replace($file, '', $path)) === $name) {
      import_data($path, $file);
    }
  });

  // Redirect back to the page
  wp_redirect($_SERVER['HTTP_REFERER']);
  exit();
}

/**
 * Creates a table for the plugin, adds option to database to version table
 * @param  [string] $name The postfix for the table name.
 */
function create_table($name) {
  global $wpdb;

  $table = $wpdb->prefix . FEED_TABLE . '_' . $name;
  $schema = implode(SCHEMA[$name], ' ');

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table ($schema) $charset_collate;";

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta($sql);

  add_option(DATABASE_VERSION_OPTION_ID, DATABASE_VERSION);
}

/**
 * This will read the a csv files and import it into it's corresponding
 * table based on it's name.
 * @param  [string] $path The full path of the file (including filename)
 * @param  [string] $name The full filename
 */
function import_data($path, $name) {
  global $wpdb;

  // Open up the file
  $handle = fopen($path, 'r');

  // Use wc to figure out the longest line in the file
  $longest = explode(' ', shell_exec('wc -L ' . $path));
  $length = (int)$longest[0] + 4; // add a little padding for EOL chars

  // Create the table name
  // Get the head/column information for the table from the first line
  // Get the values to use for the INSERT INTO statement
  $table = $wpdb->prefix . FEED_TABLE . '_' . str_replace(FILE_FORMAT, '', $name);
  $head = trim(fgets($handle));
  $head = explode(',', $head);
  $insert = implode($head, ', ');

  // Create the UPDATE command for the ON DUPLICATE KEY flag using values
  $update = '';
  foreach ($head as $column) {
    $update = $update . "$column=VALUES($column), ";
  }
  $update = rtrim($update, ', ');

  // Loop through the rows and execute a query to insert into table
  while (($data = fgetcsv($handle, $length)) !== false) {
    for ($i = 0; $i < sizeof($data); $i++) {
      $data[$i] = esc_sql($data[$i]);
    }

    $values = "'" . implode("', '", $data) . "'";

    $query = $wpdb->prepare("
      INSERT INTO $table ($insert)
      VALUES ($values)
      ON DUPLICATE KEY UPDATE $update");

    $wpdb->query($query);
  }

  fclose($handle);
}
