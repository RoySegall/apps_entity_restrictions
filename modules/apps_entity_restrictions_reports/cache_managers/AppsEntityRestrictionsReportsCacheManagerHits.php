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
    $this->cacheManager->setCache($this->getSuffix($date, $type), $value);
  }

  /**
   * Retrieve the cache value for a date.
   *
   * This will be used when increasing a hit or asking information for a date.
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
    if ($cache = $this->cacheManager->getCache($this->getSuffix($date, $type))) {
      return $cache;
    }
  }

  /**
   * Get the suffix for the cache manager.
   *
   * @param $date
   *   The date we handling.
   * @param $type
   *   The type of the cache: total, passed or failed.
   *
   * @return string
   *   The suffix.
   */
  public function getSuffix($date, $type) {
    $date = str_replace('/', '_', $date);
    return $date . ':hits:' . $type;
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
   *   The status of the request: total, failed or pass.
   */
  public function increaseDateHits($date, $status) {
    $total = $this->getDateHits($date, $status) ? $this->getDateHits($date, $status) : 0;
    $total++;
    $this->cacheDateHits($date, $total, $status);
  }

}
