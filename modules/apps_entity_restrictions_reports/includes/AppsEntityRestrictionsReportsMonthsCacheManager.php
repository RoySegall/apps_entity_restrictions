<?php

/**
 * @file
 *
 * Contains AppsEntityRestrictionsReportsMonthsCacheManager
 */

class AppsEntityRestrictionsReportsMonthsCacheManager {

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
    $this->app = $app;
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
