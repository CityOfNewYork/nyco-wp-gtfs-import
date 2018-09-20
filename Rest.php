<?

namespace nyco\WpGtfsImport\Rest;

/**
 * Dependencies
 */

use WP_REST_Request;
use WP_REST_Response;

/**
 * Constants
 */

const DATA_DIR = 'gtfs-data/';
const FILE_FORMAT = '.txt';

/**
 * Functions
 */

/**
 * [register_rest_routes description]
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */
function register_rest_routes($dir) {
  register_rest_route('api/v1', "/$dir/stations", array(
    'methods' => 'GET',
    'callback' => function(WP_REST_Request $request) use ($dir) {
      return request_response_stations($request, $dir);
    })
  );

  register_rest_route('api/v1', "/$dir/(?P<file>[a-zA-Z_-]+)", array(
    'methods' => 'GET',
    'callback' => function(WP_REST_Request $request) use ($dir) {
      return request_response_gtfs($request, $dir);
    })
  );
};

/**
 * Scans the gtfs-data directory for files to turn into endpoints.
 */
add_action('rest_api_init', function() {
  $uploads = wp_upload_dir();
  $uploads = $uploads['basedir'] . '/';
  $data_dir_full = $uploads . DATA_DIR;

  if (!is_dir($data_dir_full)) return;

  // For each of the directories in the data upload, create an endpoint for
  // it's each of it's file contents. Only if it is a directory.
  $files = scandir($data_dir_full);
  for ($i = 0; $i < sizeof($files); $i++) {
    $data_dir_sub = $data_dir_full . $files[$i];
    $dir = $files[$i];

    if (is_dir($data_dir_sub)) {
      register_rest_routes($dir);
    }
  }
});

/**
 * Callback for the enpoint registration, it scans the directory for the
 * requested file and returns a JSON response of the data's content.
 * @param  [object] $request The WP REST Request object.
 * @param  [string] $dir     The main directory.
 * @return [json]            The JSON response for the request.
 */
function request_response_gtfs($request, $dir) {
  $dir = get_request_dir($dir);
  $file = $request['file'];

  // Get the file contents
  $data = file_get_contents("$dir/$file.txt");

  // Convert the CSV into associative array
  $data = csv_to_json($data);

  // Format the response
  $response = new WP_REST_Response($data);

  // Add a custom status code
  $response -> set_status(200);
  return $response;
}

/**
 * [request_response_stations description]
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */
function request_response_stations($request, $dir) {
  $dir = get_request_dir($dir);

  // $data_routes = csv_to_json(file_get_contents("$dir/$routes.txt"));
  // $data_trips = csv_to_json(file_get_contents("$dir/$trips.txt"));
  // $data_stop_times = csv_to_json(file_get_contents("$dir/$stop_times.txt"));
  $data_stops = csv_to_json(file_get_contents("$dir/stops.txt"));

  $data = $data_stops;

  // for each stop
  // for ($i = 0; $i < sizeof($data); $i++) {
  //   for ($x = 0; $x < sizeof($data_stop_times); $x++) {
  //   //   get trip_id
  //     $trip_id = $data['trip_id'];
  //     $trips = array();
  //   //   for each trip
  //     for ($y = 0; $y < sizeof($data_stop_times); $y++) {
  //   //     if trip contains trip_id
  //       if ($data_stop_times[$y]['trip_id'] === $trip_id)
  //         $trips.push($data_stop_times[$y]);
  //   //       save trip_ids
  //     }
  //   //   for each trip
  //     for ($z = 0; $z < sizeof($data_trips); $z++) {
  //       if ($data_trips[$z][])
  //   //     if route contains trip_ids

  //   //       save routes
  //     }
  //   }
  // }

  for ($i = 0; $i < sizeof($data); $i++) {
    $data[$i]['trips'] = [];
    foreach ($data_stop_times as $st) {
      // $data[$i]['trips'].push(
      //   array_filter($data_trips, function($t) use ($st) {
      //     $t['stop_id'] === $st['stop_id'];
      //   })
      // );
    }
    // $data['trips'] = each($data_stop_times, function($stop_time) use ($data_trips) {
    //   return filter($data_trips, ['stop_id' => $stop_time['stop_id']]);
    // });
  }

  // Format the response
  $response = new WP_REST_Response($data);

  // Add a custom status code
  $response -> set_status(200);
  return $response;
}

/**
 * Utilities
 */

/**
 * Convert the CSV data into associative array.
 * @return [type] [description]
 */
function csv_to_json($data) {
  $data = array_map('str_getcsv', explode("\n", $data));
  $head = array_shift($data);
  return array_map('array_combine', array_fill(0, count($data), $head), $data);
}

/**
 * Get the directory path of the data request.
 * @param  [string] $dir The folder/endpoint of the request.
 * @return [string]      The full path to the directory in uploads.
 */
function get_request_dir($dir) {
  // Get the uploads directory
  $uploads = wp_upload_dir();
  $uploads = $uploads['basedir'];

  return $uploads . '/' . DATA_DIR . $dir;
}