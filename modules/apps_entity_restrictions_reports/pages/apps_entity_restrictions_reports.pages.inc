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

  $months_and_days = AppsEntityRestrictionsReports::getViewsDays($app);
  $hits = AppsEntityRestrictionsReports::calculateHits(array());
  drupal_add_js(
    array(
      'chart' => array(
        'months' => array_keys($months_and_days),
        'days' => $months_and_days,
        'hits' => $hits,
      ),
    ),
  'setting');

  $content = theme('aer_reports_graph');
  $content .= views_embed_view('apps_entity_request', 'application_logs', $app->identifier());

  return $content;
}