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
   * @var AppsEntityRestrictionsReportsHitsCacheManager
   */
  protected $hitsManager;

  /**
   * Constructing the object.
   *
   * @param AppsEntityRestriction $app
   *   The app instance.
   */
  function __construct(AppsEntityRestriction $app) {
    $this->setApp($app);
    $this->hitsManager = new AppsEntityRestrictionsReportsHitsCacheManager($this);
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
   * @return AppsEntityRestrictionsReportsHitsCacheManager
   */
  public function getHitsManager() {
    return $this->hitsManager;
  }

  /**
   * Return a cache information match to a cache ID.
   *
   * @param $suffix
   *   A suffix added by tye different cache managers.
   *
   * @return mixed
   *   The cache value.
   */
  public function getCache($suffix) {
    $cid = AppsEntityRestrictionsReports::getCacheId($this->app) . ':' . $suffix;

    if (!$cache = cache_get($cid)) {
      return;
    }

    return $cache->data;
  }

  /**
   * Caching the data.
   *
   * @param $suffix
   *   A suffix added by tye different cache managers.
   * @param $data
   *   What we need to cache.
   */
  public function setCache($suffix, $data) {
    $cid = AppsEntityRestrictionsReports::getCacheId($this->app) . ':' . $suffix;
    return cache_set($cid, $data);
  }

}
