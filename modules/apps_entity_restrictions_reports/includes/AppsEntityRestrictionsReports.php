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
      return array();
    }

    $months = array();

    foreach ($evcs as $evc) {
      $day = date('d', $evc->created);
      $month = date('m/y', $evc->created);

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

  /**
   * Calculate the hits for each day.
   *
   * @param $months_and_days
   *   The days and months which we need to build upon the hits.
   *
   * @return array
   *   The total, good and bad hits info.
   */
  public static function calculateHits(array $months_and_days) {
    return array(
      [6, 9, 1,2,2,10,6,3,4,5,6,7,7,8,4,2,4],
      [15, 1, 1,12,3,4,5,6,17,7,8,14,2,4,12,1,6],
      [5, 9, 1,2,3,4,5,6,7,7,8,4,2,4,2,10,6]
    );
  }

  /**
   * Hold list of logs messages.
   *
   * @return array
   */
  public static function logsList() {
    return array(
      'property_operation_not_allowed' => array(
        'general' => 'The app made a bad @method request against @property for @entity_type.',
        'object' => 'The app made a bad @method request against @property for @entity_type:@entity_id.',
      ),
      'general_operation_not_allowed' => array(
        'general' => 'The app made a bad @method request against a @entity_type endpoint.',
        'object' => 'The app made a bad @method request against @entity_type:@entity_id.',
      ),
      'general_operation_allowed' => array(
        'general' => 'The app made a good @method request against a @entity_type.',
        'object' => 'The app made a good @method request against @entity_type:@entity_id.',
      ),
    );
  }

}
