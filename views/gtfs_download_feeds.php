<hr>

<h2>Step 2. Download Feeds</h2>

<form method="POST" action="<?= admin_url('admin.php') ?>">
  <p>
    This will fetch the feeds and save them in the specified path above. Note,
    this will overwrite existing feeds with the same archive name (<code>
    google_transit.zip</code> will override <code>google_transit.zip</code>).
  </p>

  <input type="hidden" name="action" value="wp_gtfs_download" />

  <p class="submit">
    <input type="submit" value="Download Feeds" class="button button-primary"/>
  </p>
</form>