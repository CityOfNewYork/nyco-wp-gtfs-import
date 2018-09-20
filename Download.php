<?

namespace nyco\WpGtfsImport\Download;

if (!is_admin()) return;

/**
 * Dependencies
 */

use ZipArchive;
use nyco\WpGtfsImport\Utilities as Utilities;

/**
 * Constants
 */

const FEED_OPTION_ID = 'gtfs_feed';
const FLATTEN_OPTION_ID = 'gtfs_flatten_extracted_files';
const ARCHIVE_FORMAT = '.zip';
const FILE_FORMAT = '.txt';

/**
 * This is the action that loops through the feed urls, calls methods to
 * download and unpackage them, then redirects back to the admin page.
 * The wp_gtfs_import_download form is set in the Settings.php file.
 * @param  [string] $feed_dir The directory to dump the feeds to.
 */
add_action('admin_action_wp_gtfs_download', function() {
  $uploads = wp_upload_dir();
  $uploads = $uploads['basedir'] . '/';
  $feed_dir = Utilities\get_data_dir();

  if (!is_dir($feed_dir)) {
    mkdir($feed_dir);
  }

  // error handling
  // if ! return;

  // success
  $feeds = explode(',', get_option(FEED_OPTION_ID));

  // Get the feed archive from the url and save it to our feed directory
  // Unzip the archive
  foreach ($feeds as $feed) {
    $feed = trim($feed);
    $archive = get_feed_archive($feed, $feed_dir . basename($feed));
    unzip_feed_archive($archive);
  }

  // Redirect back to the page
  wp_redirect($_SERVER['HTTP_REFERER']);
  exit();
});

/**
 * This uses the file_put_contents to download the GTFS data from remote urls
 * @param  [string] $read  The url of the file.
 * @param  [string] $write The path to write the file to.
 * @return [string]        Returns the write path back.
 */
function get_feed_archive($read, $write) {
  $handle = fopen($read, 'r');
  file_put_contents($write, $handle);
  fclose($handle);

  return $write;
}

/**
 * Uses the ZipArchive class to unzip the downloaded files
 * @param  [string] $archive The path of the archive
 */
function unzip_feed_archive($archive) {
  $zip = new ZipArchive;
  $data_dir = Utilities\get_data_dir();
  $flatten = (1 == get_option(FLATTEN_OPTION_ID, 0));
  $res = $zip -> open($archive);

  if ($res != TRUE) {
    return null;
  }

  $dir = str_replace(ARCHIVE_FORMAT, '', $archive);

  $zip->extractTo($dir);

  if ($flatten) {
    for ($i = 0; $i < $zip -> numFiles; $i++) {
      $name = $zip -> getNameIndex($i);
      rename(
        $dir . '/' . $name,
        $data_dir . basename($dir) . '_' . $name
      );
    }
  }

  $zip->close();
}
