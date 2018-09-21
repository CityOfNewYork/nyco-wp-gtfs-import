<input type="text" style="display: block; width: 100%" name="<?= $args['id'] ?>" id="<?= $args['id'] ?>" value="<?= get_option($args['id'], '') ?>" placeholder="<?= $args['placeholder'] ?>" />

<p class="description">
  Enter a comma separated list of GTFS feeds to import here and click "Save Changes." For example;
<pre class="code">
http://web.mta.info/developers/data/nyct/subway/google_transit.zip,
http://web.mta.info/developers/data/nyct/bus/google_transit_bronx.zip,
http://web.mta.info/developers/data/nyct/bus/google_transit_brooklyn.zip,
http://web.mta.info/developers/data/nyct/bus/google_transit_manhattan.zip,
http://web.mta.info/developers/data/nyct/bus/google_transit_queens.zip,
http://web.mta.info/developers/data/nyct/bus/google_transit_staten_island.zip
</pre>
</p>