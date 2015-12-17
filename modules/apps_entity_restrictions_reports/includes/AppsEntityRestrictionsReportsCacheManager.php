<?php

/**
 * @file
 * Contains \AppsEntityRestrictionsReportsCacheManager.
 */

/**
 * Manging cache for application records. Act upon adding a view count for a
 * date and when deleting the entity view count for the date.
 */
class AppsEntityRestrictionsReportsCacheManager {

  /**
   * @var AppsEntityRestriction
   *
   * The application object.
   */
  protected $app;

  /**
   * Constructing the object.
   *
   * @param AppsEntityRestriction $app
   *   The app instance.
   */
  function __construct(AppsEntityRestriction $app) {
    $this->setApp($app);
  }

  /**
   * Setting the app.
   *
   * @param AppsEntityRestriction $app
   *   The app object.
   *
   * @return AppsEntityRestrictionsReportsCacheManager
   */
  public function setApp(AppsEntityRestriction $app) {
    $this->app = $app;

    return $this;
  }

  /**
   * Get the app.
   *
   * @return AppsEntityRestriction
   */
  public function getApp() {
    return $this->app;
  }

  /**
   * check if the cache type exists.
   *
   * @param $type
   *   The type of cache i.e. hits per date.
   *
   * @return bool
   */
  public function cacheExists($type) {
  }

  /**
   * Cache the hits per each day. This will be invoke after calculating the
   * final value and won't be used for summing hits type.
   *
   * @param $date
   *   The date we handling.
   * @param $value
   *   The value to cache for the date.
   * @param $type
   *   The type of the hit: total, passed or failed.
   */
  public function cacheDateHits($date, $value, $type) {
  }

  /**
   * Increasing the value of the hit for a date.
   *
   * Unlike caching the total hits info for a date, this will be invoke once a
   * entity view count has created.
   *
   * @param $date
   *   The date we handling.
   * @param $status
   *   The status of the request: failed or pass. This will also increase the
   *   total hits for the date.
   */
  public function increaseDateHits($date, $status) {
  }

  /**
   * Retrieve the cache value for a date.
   *
   * This will be used when increasing a hit.
   *
   * @param $date
   *   The date we handling.
   * @param $type
   *   The type of the cache: total, passed or failed.
   */
  public function getDateHits($date, $type) {
  }

  /**
   * Getting all the cached days of a month.
   *
   * @param $month
   *   The month we are handling: 09, 10, 12
   *
   * @return array
   *   Array of days: [1,2,3,...,29,30,31]
   */
  public function getMonthDays($month) {
  }

  /**
   * Caching the day of a month.
   *
   * This will be invoke when creating a new entity view count.
   *
   * @param $month
   *   The month we are handling: 09, 10, 12
   * @param $day
   *   The day in the month: 20, 22, 01
   */
  public function cacheMonthDay($month, $day) {
  }

  /**
   * Caching the days of the month.
   *
   * Unlike caching a day for a month, this method will be invoked when
   * calculating a chink of days per month.
   *
   * @param $month
   *   The month we are handling: 09, 10, 12
   * @param array $days
   *   Array of days: [1,2,3,...,29,30,31]
   */
  public function cacheMonthDays($month, array $days) {
  }

}
