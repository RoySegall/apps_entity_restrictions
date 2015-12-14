<?php

/**
 * @file
 *
 * Drush integration for generating dummy hits.
 */

/**
 * Implements hook_drush_command().
 */
function apps_entity_restrictions_reports_drush_command() {
  $items = array();

  $items['generate-dummy-hits'] = array(
    'description' => dt('Generate a dummy hits'),
    'arguments' => array(
      'application_id' => dt('The application id.'),
    ),
    'options' => array(
      'months' => dt('Specify the amount of months back from now to start hit the server. i.e - 6 months backward.'),
      'hits' => dt('How many hits per day should an application have.'),
      'good_hits' => dt('Define how many of the hits should be good hits. Bad hits will be calculate as the amount left to reach 100%'),
    ),
    'examples' => array(
      'drush reports_generate 1 --months=6 --hits=20 --good_hits=40' => dt('Will generate 20 hits per day for the past 6 months when 40% are good hits when 60% is bad hits.'),
    ),
    'aliases' => array('reports_generate'),
  );

  return $items;
}

function drush_apps_entity_restrictions_reports_generate_dummy_hits($id) {
  $months = drush_get_option('months', 6);
  $hits = drush_get_option('hits', 100);
  $good_hits = drush_get_option('good_hits', rand(40, 60));
  $date = new DateTime();

  // Get the current timestamp and the timestamp {$months} ago.
  $current = time();
  $first_day_time = $date
    ->setTimestamp(strtotime('-' . $months . ' month'))
    ->setTime(0,0)
    ->getTimestamp();

  // Iterate form the first timestamp and jump by 24 hours every time. This will
  // ensure we will generate hits for each day at the time period we got.
  for ($midnight_of_the_day = $first_day_time; $midnight_of_the_day < $current; $midnight_of_the_day = $midnight_of_the_day + 86400) {

    // Calculate how many request per hour we need to generate.
    // todo calculate.
    $request_per_hour = 4;
    $good_hits_per_hour = ($request_per_hour * $good_hits) / 100;
    $bad_hits_per_hour = ($request_per_hour * (100 - $good_hits)) / 100;

    for ($hour = $midnight_of_the_day; $hour < ($midnight_of_the_day + 86400); $hour = $hour + 3600) {
      // Going through each hour in the day and generate a dummy view.

      // Get {$request_per_hour} dummy times in the current hour randomly.

      // Iterate through those times and decide randomly if they need to be bad
      // or good hits according to the ratio we have. i.e if we have 4 good
      // requests and 2 bad request the list should be:
      // [good, good, bad, good, good, bad].

      // Generating dummy message text for the log.
    }
  }

}
