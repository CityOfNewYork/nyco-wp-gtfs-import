<? $checked = (1 == get_option($args['id'], '')) ? 'checked' : '' ?>

<label for="<?= $args['id'] ?>">
  <input id="<?= $args['id'] ?>" type="checkbox" name="<?= $args['id'] ?>" value="1" <?= $checked ?>/> Yes
</label>

<p class="description">
  By default, feeds are downloaded and extracted into their own directories
  containing all of their fields. Ex; <code>google_transit/agency.txt</code>.
  Checking this option will extract the fields within the feeds to a flattend
  file structure. Ex; <code>google_transit_agency.txt</code>.
</p>