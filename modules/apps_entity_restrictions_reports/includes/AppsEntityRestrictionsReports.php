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
          return array();
        }

        cache_set(self::BASIC_CACHE_KEY . $app->identifier(), $result, 'cache');
        $evcs = $result;
      }
    }

    return $evcs;
  }

  /**
   * Calculate the hits for each day per one month.
   *
   * @param $month
   *   The month for the hits. i.e: 09/15, 12/10
   * @param $days
   *   The days for that month.
   * @param $app_id
   *   The app ID.
   *
   * @return array
   *   The total, good and bad hits info.
   */
  public static function calculateHits($month, array $days, $app_id) {

    $hits = array();

    foreach ($days as $day) {
      $date = $day . '/' . $month;
      $hits[0][] = AppsEntityRestrictionsReports::countHits($date, 'passed', $app_id);
      $hits[1][] = AppsEntityRestrictionsReports::countHits($date, 'failed', $app_id);
      $hits[2][] = AppsEntityRestrictionsReports::countHits($date, 'total', $app_id);
    }

    return $hits;
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

  /**
   * Count the number of hits per day.
   *
   * @param $date
   *   The date of the hits.
   * @param $type
   *   The type of the counting: passed, failed or total.
   * @param $app_id
   *   The app id.
   *
   * @return integer
   *   The number o hits.
   */
  private static function countHits($date, $type, $app_id) {
    $query = new EntityFieldQuery();

    $query
      ->entityCondition('entity_type', 'entity_view_count')
      ->propertyCondition('entity_type', 'apps_entity_restrictions')
      ->propertyCondition('type', 'apps_usage')
      ->propertyCondition('entity_id', $app_id)
      ->fieldCondition('field_request_date', 'value', $date);

    if ($type != 'total') {
      $query->fieldCondition('field_request_status', 'value', $type);
    }

    return $query->count()->execute();
  }

}
