<?php

/**
 * @file
 *
 * Contains AppsEntityRestrictionsReportsHitsCacheManager
 */

class AppsEntityRestrictionsReportsCacheManagerHits extends AppsEntityRestrictionsReportsCacheManagerBase {

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
    $date = str_replace('/', '_', $date);
    $this->cacheManager->setCache($date . ':hits:' . $type, $value);
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
   *
   * @return mixed
   *   The cached date hits.
   */
  public function getDateHits($date, $type) {
    $date = str_replace('/', '_', $date);
    if ($cache = $this->cacheManager->getCache($date . ':hits:' . $type)) {
      return $cache;
    }
  }

}
