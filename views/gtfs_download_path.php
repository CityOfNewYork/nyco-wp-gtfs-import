<input type="text" name="<?= $args['id'] ?>" id="<?= $args['id'] ?>" value="<?= get_option($args['id'], '') ?>" placeholder="<?= $args['placeholder'] ?>"/>

<p class="description">
  Enter a path in your upload directory to download feeds to. By default this will be <code>gtfs-data/</code>. Include the trailing slash.
</p>