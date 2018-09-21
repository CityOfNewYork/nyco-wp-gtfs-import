<?php

namespace nyco\WpGtfsImport\Utilities;

/**
 * Constants
 */

const FEED_OPTION_ID = 'gtfs_feed';
const FIELDS_OPTION_ID = 'gtfs_fields';
const DATA_DIR = 'gtfs-data/';
const DATA_DIR_OPTION_ID = 'gtfs_downlod_path';

/**
 * Functions
 */

/**
 * [get_data_dir description]
 * @return [type] [description]
 */
function get_data_dir() {
  $uploads = wp_upload_dir();
  $uploads = $uploads['basedir'] . '/';
  return $uploads . get_option(DATA_DIR_OPTION_ID, DATA_DIR);
}

/**
 * [parse_directories description]
 * @param  [type] $create_table [description]
 * @param  [type] $insert_data  [description]
 * @return [type]               [description]
 */
function parse_directories($cb1 = null, $cb2 = null) {
  $uploads = wp_upload_dir();
  $uploads = $uploads['basedir'] . '/';
  $data_dir_path = get_data_dir();

  if (!is_dir($data_dir_path)) {
    return;
  }

  // We need the feed settings to make sure we aren't operating in other directories
  $feeds = explode(',', get_option(FEED_OPTION_ID));
  for ($i = 0; $i < sizeof($feeds); $i++) {
    $feeds[$i] = str_replace('.zip', '', basename($feeds[$i]));
  }

  $fields = array_keys(get_option(FIELDS_OPTION_ID, []));

  // For each of the files in the data direcory...
  foreach (glob($data_dir_path . '*', GLOB_ONLYDIR) as $folder) {
    if (in_array(basename($folder), $feeds)) {
      $data_files = array_diff(scandir($folder), ['.', '..']);

      if (null !== $cb1) {
        $cb1($folder, basename($folder));
      }

      // For each file, execute the callback.
      foreach ($data_files as $file) {
        if (in_array(str_replace('.txt', '', $file), $fields)) {
          $data_file_path = $folder . '/' . $file;
          $data_file_name = $file;

          if (null !== $cb2) {
            $cb2($data_file_path, $data_file_name);
          }
        }
      }
    }
  }
}
