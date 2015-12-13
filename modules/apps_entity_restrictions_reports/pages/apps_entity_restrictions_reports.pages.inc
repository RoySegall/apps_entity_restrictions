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
  drupal_add_library('apps_entity_restrictions_reports', 'line-chart');

  return views_embed_view('apps_entity_request', 'application_logs', $app->identifier());
}
