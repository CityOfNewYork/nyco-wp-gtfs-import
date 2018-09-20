<?

namespace nyco\WpGtfsImport\Settings;

if (!is_admin()) return;

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
$pages = [
  [
    'page_title' => TITLE,
    'menu_title' => TITLE,
    'capability' => CAPABILITY,
    'menu_slug' => ID,
    'extra_action' => function() {
      echo '<hr>';
      echo '<h2>Download Feeds</h2>';
      echo '<form method="POST" action="' . admin_url('admin.php') . '">
        <p>This will save the feeds in the specified path. Note, this will
        overwrite existing feeds with the same archive name
        (<code>google_transit.zip</code> will override
        <code>google_transit.zip</code>).</p>
        <input type="hidden" name="action" value="wp_gtfs_download" />
        <p class="submit">
          <input type="submit" value="Download Feeds" class="button button-primary"/>
        </p>
      </form>';

      // echo '<hr>';
      // echo '<h2>Import Feeds</h2>';
      // echo '<p>Backup your database before importing feeds. Click "Import GTFS"
      //   to import the fields into into their corresponding <code>
      //   wp_gtfs_feed_[field]</code> table.</p>';

      // echo '<form method="POST" action="' . admin_url('admin.php') . '">
      //   <input type="hidden" name="action" value="wp_gtfs_import_all" />
      //   <p class="submit">
      //     <input type="submit" value="Import All Feeds" class="button button-primary"/>
      //   </p>
      // </form>';

      // echo '<h2>Import Feeds Individually</h2>';
      // Utilities\parse_directories(function($path, $name) {
      //   echo '<form method="POST" action="' . admin_url('admin.php') . '">
      //     <table class="form-table"><tbody><tr>
      //       <th>' . $name . '</th>
      //       <td>
      //         <input type="hidden" name="action" value="wp_gtfs_import_' . $name . '" />
      //         <input type="submit" value="Import Feed" class="button button-primary"/>
      //       </td>
      //     </tr></tbody>
      //   </form>';
      // }, null);
    }
  ]
];

/** @var array The Settings sections. */
$sections = [
  [
    'id' => 'gtfs_feeds',
    'title' => 'Feeds',
    'callback' => function() {},
    'page' => ID
  ]
];

/** @var array The settings fields. */
$settings = [
  [
    'id' => 'gtfs_feed',
    'title' => 'GTFS Feeds (URL)',
    'callback' => function ($args) {
      echo "<input
        type='text'
        name='" . $args['id'] . "'
        size=40 id='" . $args['id'] . "'
        value='" . get_option($args['id'], '') . "'
        placeholder='" . $args['placeholder'] . "'
      />";
      echo '<p class="description">Enter a comma separated list of GTFS feeds
        to import here and click "Save Changes." For example;';
      echo '<pre class="code">';
      echo '<div>http://web.mta.info/developers/data/nyct/subway/google_transit.zip,</div>';
      echo '<div>http://web.mta.info/developers/data/nyct/bus/google_transit_bronx.zip,</div>';
      echo '<div>http://web.mta.info/developers/data/nyct/bus/google_transit_brooklyn.zip,</div>';
      echo '<div>http://web.mta.info/developers/data/nyct/bus/google_transit_manhattan.zip,</div>';
      echo '<div>http://web.mta.info/developers/data/nyct/bus/google_transit_queens.zip,</div>';
      echo '<div>http://web.mta.info/developers/data/nyct/bus/google_transit_staten_island.zip</div>';
      echo '</pre></p>';
    },
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_feed',
      'placeholder' => ''
    ],
    'type' => 'string',
    'description' => '',
    'sanitize_callback' => function($args) {
      return $args;
    },
    'show_in_rest' => false,
    'default' => ''
  ],
  [
    'id' => 'gtfs_downlod_path',
    'title' => 'Download Path',
    'callback' => function ($args) {
      echo "<input
        type='text'
        name='" . $args['id'] . "'
        size=40 id='" . $args['id'] . "'
        value='" . get_option($args['id'], '') . "'
        placeholder='" . $args['placeholder'] . "'
      />";
      echo '<p class="description">Enter a path in your upload
        directory to download feeds to. By default this will be
        <code>gtfs-data/</code>. Include the trailing slash.</p>';
    },
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_downlod_path',
      'placeholder' => 'gtfs-data/'
    ],
    'type' => 'integer',
    'description' => '',
    'sanitize_callback' => function($args) {
      return $args;
    },
    'show_in_rest' => false,
    'default' => 'gtfs-data'
  ],
  [
    'id' => 'gtfs_flatten_extracted_files',
    'title' => 'Flatten Extracted Files',
    'callback' => function ($args) {
      $checked = (1 == get_option($args['id'], '')) ? 'checked' : '';
      echo '<label for="' . $args['id'] . '">
        <input
          id="' . $args['id'] . '"
          type="checkbox"
          name="' . $args['id'] . '"
          value="1"
          ' . $checked . '
        /> Yes</label>';
      echo '<p class="description">By default, feeds are downloaded and
        extracted into their own directories containing all of their fields.
        Ex; <code>google_transit/agency.txt</code>.
        Checking this option will extract the fields within the feeds to a
        flattend file structure.
        Ex; <code>google_transit_agency.txt</code>.</p>';
    },
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_flatten_extracted_files',
      'placeholder' => ''
    ],
    'type' => 'integer',
    'description' => '',
    'sanitize_callback' => function($args) {
      return $args;
    },
    'show_in_rest' => false,
    'default' => 'gtfs-data'
  ],
  [
    'id' => 'gtfs_fields',
    'title' => 'GTFS Fields to Import',
    'callback' => function ($args) {
      $value = get_option($args['id'], '');
      $options = [
        'agency', 'stops', 'routes', 'trips', 'stop_times', 'calendar',
        'calendar_dates', 'fare_attributes', 'fare_rules', 'shapes',
        'frequencies', 'feed_info'
      ];
      foreach ($options as $option) {
        $checked = (1 == $value[$option]) ? 'checked' : '';
        echo '<label for="' . $args['id'] . '_' . $option . '">
          <input
            id="' . $args['id'] . '_' . $option . '"
            type="checkbox"
            name="' . $args['id'] . '[' . $option . ']"
            value="1"
            ' . $checked . '
          />' . ucwords(str_replace('_', ' ', $option)) . '
        </label><br>';
      }
      echo '<br>';
      echo '<p class="description">Select the fields within the feeds you would
        like to import into your database.</p>';
    },
    'page' => ID,
    'section' => $sections[0]['id'],
    'args' => [
      'id' => 'gtfs_fields',
      'placeholder' => ''
    ],
    'type' => 'integer',
    'description' => '',
    'sanitize_callback' => function($args) {
      return $args;
    },
    'show_in_rest' => false,
    'default' => ''
  ]
];


/**
 * Initialization
 */

/**
 * Triggered before any other hook when a user accesses the admin area.
 * Creates the plugin settings sections then the plugin settings fields
 * and registers them.
 * @param  [array] $sections An array of sections to create.
 * @param  [array] $settings An array of settings to create.
 */
add_action('admin_init', function() use ($sections, $settings) {
  for ($i = 0; $i < sizeof($sections); $i++) {
    add_settings_section(
      $sections[$i]['id'],
      $sections[$i]['title'],
      $sections[$i]['callback'],
      $sections[$i]['page']
    );
  }

  for ($x = 0; $x < sizeof($settings); $x++) {
    add_settings_field(
      $settings[$x]['id'],
      $settings[$x]['title'],
      $settings[$x]['callback'],
      $settings[$x]['page'],
      $settings[$x]['section'],
      $settings[$x]['args']
    );

    register_setting(ID, $settings[$x]['id'], array(
      'type' => $settings[$x]['type'],
      'description' => $settings[$x]['description'],
      'sanitize_callback' => $settings[$x]['sanitize_callback'],
      'show_in_rest' => $settings[$x]['show_in_rest'],
      'default' => $settings[$x]['default']
    ));
  }
});

/**
 * This action is used to add extra submenus and menu options to the admin
 * panel's menu structure. It runs after the basic admin panel menu structure
 * is in place. Adds the plugin menu page and the page content.
 * @param  [array] $pages An array of pages to create.
 */
add_action('admin_menu', function() use ($pages) {
  for ($i = 0; $i < sizeof($pages); $i++) {
    add_options_page(
      $pages[$i]['page_title'],
      $pages[$i]['menu_title'],
      $pages[$i]['capability'],
      $pages[$i]['menu_slug'],
      function() use ($pages, $i) {
        echo '<div class="wrap">';
        echo '  <h1>' . TITLE . '</h1>';
        echo '  <form method="post" action="options.php">';

        do_settings_sections(ID);
        settings_fields(ID);
        submit_button();

        echo '  </form>';

        if (isset($pages[$i]['extra_action'])) {
          $pages[$i]['extra_action']();
        }

        echo '</div>';
      }
    );
  }
});
