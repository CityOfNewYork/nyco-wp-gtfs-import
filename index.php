<?

/**
 * Plugin Name:  Nyco Wp GTFS Import
 * Description:  Import GTFS Transit Data
 * Author:       NYC Opportunity
 * Requirements: The plugin doesn't include dependencies. These should be added
 *               to the root Composer file for the site (composer require ...)
 */

namespace nyco\WpGtfsImport;

/**
 * Dependencies
 */

/** Plugin utilities */
require_once 'Utilities.php';

/** Configuration for the settings page of the plugin. */
require_once 'Settings.php';

/** Functionality of the import and unarchive method for the GTFS Feeds. */
require_once 'Download.php';

/** Actions for importing data into the database including data schema */
require_once 'Database.php';

/** Set up custom posts and fields for the data */
require_once 'Posts.php';

/** Setting up the saved feeds as REST endpoints. */
require_once 'Rest.php';
