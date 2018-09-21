<? $value = get_option($args['id'], ''); ?>

<? $options = [
  'agency', 'stops', 'routes', 'trips', 'stop_times', 'calendar',
  'calendar_dates', 'fare_attributes', 'fare_rules', 'shapes',
  'frequencies', 'feed_info'
] ?>

<? foreach ($options as $option) : ?>
  <? $checked = (1 == $value[$option]) ? 'checked' : '' ?>
  <label for="<?= $args['id'] ?>_<?= $option ?>">
    <input id="<?= $args['id'] ?>_<?= $option ?>" type="checkbox" name="<?= $args['id'] ?>[<?= $option ?>]" value="1" <?= $checked ?>/>
    <?= ucwords(str_replace('_', ' ', $option)) ?>
  </label>
  <br>
<? endforeach ?>

<br>

<p class="description">
  Select the fields within the feeds you would like to import into your database.
</p>