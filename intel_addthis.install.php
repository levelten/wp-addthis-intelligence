<?php

/**
 * Fired when the plugin is installed and contains schema info and updates.
 *
 * @link       getlevelten.com/blog/tom
 * @since      1.0.0
 *
 * @package    Intel
 */


function intel_addthis_install() {

}

/**
 * Implements hook_uninstall();
 *
 * Delete plugin settings
 *
 */
function intel_addthis_uninstall() {
  // remove any custom events settings for plugin intel_events
  $event_custom = get_option('intel_intel_events_custom', array());
  $event_info = intel_addthis()->intel_intel_event_info();
  $save = 0;
  foreach ($event_info as $k => $v) {
    if (isset($event_custom[$k])) {
      unset($event_custom[$k]);
      $save = 1;
    }
  }
  if ($save) {
    update_option('intel_intel_events_custom', $event_custom);
  }
}