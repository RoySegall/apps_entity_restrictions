<?php

/**
 * @file
 * Contain AppsEntityRestrictionsReports
 */

class AppsEntityRestrictionsReports {

  const BASIC_CACHE_KEY = 'aer_app_views_';

  /**
   * Quick alias for the cache manager.
   *
   * @param AppsEntityRestriction $app
   *   The application instance.
   *
   * @return AppsEntityRestrictionsReportsCacheManager
   */
  public static function cacheManager(AppsEntityRestriction $app) {
    return new AppsEntityRestrictionsReportsCacheManager($app);
  }

  /**
   * Get cache ID for application.
   *
   * @param AppsEntityRestriction $app
   *   The application instance.
   *
   * @return string
   *   The cache ID.
   */
  public static function getCacheId(AppsEntityRestriction $app) {
    return AppsEntityRestrictionsReports::BASIC_CACHE_KEY . $app->identifier();
  }

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

    // Cache the days calendar representation.
    foreach ($evcs as $evc) {
      $day = date('d', $evc->created);
      $month = date('m/Y', $evc->created);

      if (empty($months[$month])) {
        $months[$month] = array();
      }

      if (in_array($day, $months[$month])) {
        continue;
      }

      $months[$month][] = $day;
    }

    return $months;
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

      if ($cache = cache_get(self::getCacheId($app))) {
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

        cache_set(self::getCacheId($app), $result, 'cache');
        $evcs = $result;
      }
    }

    return $evcs;
  }

  /**
   * Calculate the hits for each day per one month.
   *
   * @param $month
   *   The month for the hits. i.e: 09/2015, 12/2010
   * @param $days
   *   The days for that month.
   * @param AppsEntityRestriction $app
   *   The app instance.
   *
   * @return array
   *   The total, good and bad hits info.
   */
  public static function calculateHits($month, array $days, AppsEntityRestriction $app) {

    $hits = array();

    foreach ($days as $day) {
      $date = $day . '/' . $month;
      $hits[0][] = AppsEntityRestrictionsReports::countHits($date, 'passed', $app);
      $hits[1][] = AppsEntityRestrictionsReports::countHits($date, 'failed', $app);
      $hits[2][] = AppsEntityRestrictionsReports::countHits($date, 'total', $app);
    }

    return $hits;
  }

  /**
   * Count the number of hits per day.
   *
   * @param $date
   *   The date of the hits.
   * @param $type
   *   The type of the counting: passed, failed or total.
   * @param AppsEntityRestriction $app
   *   The app instance.
   *
   * @return integer
   *   The number o hits.
   */
  private static function countHits($date, $type, AppsEntityRestriction $app) {
    /** @var AppsEntityRestrictionsReportsHitsCacheManager $cache_manager */
    $cache_manager = AppsEntityRestrictionsReports::cacheManager($app)->getHitsManager();

    if ($count = $cache_manager->getDateHits($date, $type)) {
      return $count;
    }

    // Handle cache per day for each application.
    $query = new EntityFieldQuery();

    $query
      ->entityCondition('entity_type', 'entity_view_count')
      ->propertyCondition('entity_type', 'apps_entity_restrictions')
      ->propertyCondition('type', 'apps_usage')
      ->propertyCondition('entity_id', $app->identifier())
      ->fieldCondition('field_request_date', 'value', $date);

    if ($type != 'total') {
      $query->fieldCondition('field_request_status', 'value', $type);
    }

    $count = $query->count()->execute();
    $cache_manager->cacheDateHits($date, $count, $type);
    return $count;
  }

  /**
   * Sorting callback; Sort months in descending order.
   */
  public static function orderMonths($a, $b) {
    $strtotime = function($month_year) {
      list($month, $year) = explode('/', $month_year);

      return strtotime($month . '/1/' . $year);
    };

    return $strtotime($a) > $strtotime($b) ? -1 : 1;
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
   * Creating an entity view count entry.
   *
   * @param AppsEntityRestriction $app
   *   The app instance.
   * @param $status
   *   The status information: passed or failed
   * @param $info
   *   The log text.
   * @param $created
   *   The timestamp of the view. Optional.
   */
  public static function createEntityViewCount(AppsEntityRestriction $app, $status, $info, $created = NULL) {
    if (!$created) {
      $created = time();
    }

    $date = date('d/m/Y', $created);
    $evc = entity_view_count_create(array());
    $evc->entity_id = $app->identifier();
    $evc->entity_type = 'apps_entity_restrictions';
    $evc->type = 'apps_usage';
    $evc->created = $created;
    $wrapper = entity_metadata_wrapper('entity_view_count', $evc);
    $wrapper->field_request_status->set($status);
    $wrapper->field_info->set($info);
    $wrapper->field_request_date->set($date);
    $wrapper->save();

    // todo Update the cache.
    $cache_manager = AppsEntityRestrictionsReports::cacheManager($app)->getHitsManager();

    // Update the total.
    foreach (array('total', $status) as $type) {
      $total = $cache_manager->getDateHits($date, $type) ? $cache_manager->getDateHits($date, $type) : 0;
      $total++;
      $cache_manager->cacheDateHits($date, $total, $type);
    }
  }

}
