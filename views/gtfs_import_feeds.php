<? use nyco\WpGtfsImport\Utilities as Utilities; ?>

<hr>

<h2>Step 3. Import Feeds</h2>

<p>
  Backup your database before importing feeds. Fields will be imported into
  into their corresponding <code>wp_gtfs_feed_[field]</code> table.
</p>

<form method="POST" action="<?= admin_url('admin.php') ?>">
  <input type="hidden" name="action" value="wp_gtfs_import_all" />

  <? wp_nonce_field('admin_action_wp_gtfs_import_all', 'wp_gtfs_import_all_nonce') ?>

  <p class="submit">
    <input type="submit" value="Import All Feeds" class="button button-primary"/>
  </p>
</form>

<h2>Import Feeds Individually</h2>

<p>
  The import all fields may timeout based on server settings, so you may need
  to import fields individually.
</p>

<? Utilities\parse_directories(function($path, $name) { ?>
  <form method="POST" action="<?= admin_url('admin.php') ?>">
    <table class="form-table"><tbody><tr>
      <th><?= $name ?></th>
      <td>
        <input type="hidden" name="action" value="wp_gtfs_import_<?= $name ?>" />
        <? wp_nonce_field('admin_action_wp_gtfs_import_' . $name, 'wp_gtfs_import_' . $name . '_nonce') ?>
        <input type="submit" value="Import Feed" class="button button-primary"/>
      </td>
    </tr></tbody>
  </form>
<? }, null) ?>