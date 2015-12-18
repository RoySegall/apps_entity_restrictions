<?php

/**
 * @file
 *
 * Contains AppsEntityRestrictionsReportsHitsCacheManager
 */

class AppsEntityRestrictionsReportsHitsCacheManager {

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

}
