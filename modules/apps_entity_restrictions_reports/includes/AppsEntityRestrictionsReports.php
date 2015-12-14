<?php

/**
 * @file
 * Contain AppsEntityRestrictionsReports
 */

class AppsEntityRestrictionsReports {

  const BASIC_CACHE_KEY = 'aer_app_views_';

  /**
   * Check if the user have any permission to view reports.
   *
   * @param AppsEntityRestriction $app
   *   The application instance.
   *
   * @param stdClass $account
   *   The user object. Optional.
   *
   * @return bool
   */
  public static function reportsAccess(AppsEntityRestriction $app, stdClass $account = NULL) {
    if (empty($account)) {
      global $user;
      $account = user_load($user->uid);
    }

    return $account->uid == $app->getUid() ? user_access('view any application reports', $account) : user_access('view owned application reports', $account);
  }

  /**
   * Calculate all the months which an entity have views.
   *
   * @param AppsEntityRestriction $app
   *   The app object.
   *
   * @return array
   *   List of months.
   */
  public static function getViewsDays(AppsEntityRestriction $app) {

    if (!$evcs = self::getAppViews($app)) {
      return;
    }

    $months = array();

    foreach ($evcs as $evc) {
      $day = format_date($evc->created, 'custom', 'd');
      $month = format_date($evc->created, 'custom', 'm');

      if (empty($months[$month])) {
        $months[$month] = array();
      }

      if (in_array($day, $months[$month])) {
        continue;
      }

      $months[$month][] = $day;
    }

    return ($months);
  }

  /**
   * Get all the views relate to app.
   *
   * @param AppsEntityRestriction $app
   *   The application instance.
   *
   * @return EntityViewCount[]
   */
  public static function getAppViews(AppsEntityRestriction $app) {
    $evcs = &drupal_static(__FUNCTION__);

    if (!isset($evcs)) {

      if ($cache = cache_get(self::BASIC_CACHE_KEY . $app->identifier())) {
        $evcs = $cache->data;
      }
      else {
        $query = db_select('entity_view_count', 'evc');
        $result = $query
          ->fields('evc', array('created'))
          ->orderBy('created')
          ->execute()
        ->fetchAllAssoc('created');

        if (!$result) {
          return;
        }

        cache_set(self::BASIC_CACHE_KEY . $app->identifier(), $result, 'cache');
      }
    }

    return $evcs;
  }

}
