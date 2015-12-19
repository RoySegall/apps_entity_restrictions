<?php

/**
 * @file
 *
 * Contain menu items callbacks.
 */

/**
 * Provide reports relate to the application.
 *
 * @param AppsEntityRestriction $app
 *   The application instance.
 *
 * @return string
 *   The content of the body.
 */
function apps_entity_restrictions_reports_report_page(AppsEntityRestriction $app) {
  // Add library.
  drupal_add_library('apps_entity_restrictions_reports', 'chartist-js');

  if (!$months_and_days = AppsEntityRestrictionsReports::getViewsDays($app)) {
    drupal_set_message(t('It seems there is not request for this app.'), 'warning');
    return '';
  }

  $months = array_keys($months_and_days);
  usort($months, array('AppsEntityRestrictionsReports', 'orderMonths'));

  // Get the closest month to now and display it.
  $month = reset($months);

  $hits = AppsEntityRestrictionsReports::calculateHits($month, $months_and_days[$month], $app);
  drupal_add_js(
    array(
      'chart' => array(
        'months' => $months,
        'days' => $months_and_days[$month],
        'hits' => $hits,
        'basePath' => url('aprr/' . $app->identifier(), array('absolute' => TRUE)),
      ),
    ),
  'setting');

  $links = array();
  foreach ($months as $month) {
    $links[] = array('title' => $month, 'href' => $month);
  }

  $content = '';
  $content .= theme('ctools_dropdown', array('title' => t('Navigate to other months'), 'links' => $links));
  $content .= theme('aer_reports_graph');
  $content .= views_embed_view('apps_entity_request', 'application_logs', $app->identifier());

  return $content;
}
