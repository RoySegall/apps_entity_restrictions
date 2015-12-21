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
   * @var AppsEntityRestrictionsReportsCacheManagerHits
   */
  protected $hitsManager;

  /**
   * @var array
   *
   * The cache IDs. Will be used for loading multiple caches.
   */
  protected $cids = array();

  /**
   * Constructing the object.
   *
   * @param AppsEntityRestriction $app
   *   The app instance.
   */
  function __construct(AppsEntityRestriction $app) {
    $this->setApp($app);
    $this->hitsManager = new AppsEntityRestrictionsReportsCacheManagerHits($this);
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
   * @return AppsEntityRestrictionsReportsCacheManagerHits
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

    if (!$cache = cache_get($this->getCacheId($suffix))) {
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
    return cache_set($this->getCacheId($suffix), $data);
  }

  /**
   * Determine if the general app cache needs to be rebuild.
   *
   * This will come in handy when a new month was logged since the hits are
   * cached and a new logged month won't appear until the next cache clear.
   */
  public function needsRebuild() {
    $apps = variable_get('apps_entity_restrictions_reports_app_need_rebuild', array());
    $apps[] = $this->getApp()->identifier();
    variable_set('apps_entity_restrictions_reports_app_need_rebuild', $apps);
  }

  /**
   * Marking the app's cache as re-built.
   */
  public function invalidate() {
    $apps = variable_get('apps_entity_restrictions_reports_app_need_rebuild');
    $key = array_search($this->getApp()->identifier(), $apps);
    unset($apps[$key]);
    variable_set('apps_entity_restrictions_reports_app_need_rebuild', $apps);
  }

  /**
   * Check if the cache was invalidated.
   *
   * @return bool
   */
  public function invalidated() {
    $apps = variable_get('apps_entity_restrictions_reports_app_need_rebuild');
    return in_array($this->getApp()->identifier(), $apps);
  }

  /**
   * Constructing the cache ID.
   *
   * @param $suffix
   *   The suffix added by the cache managers.
   *
   * @return string
   *   The cache ID for the cache manager.
   */
  public function getCacheId($suffix) {
    return AppsEntityRestrictionsReports::getCacheId($this->app) . ':' . $suffix;
  }

  /**
   * Load multiple caches at once.
   *
   * @return array
   *   The cache objects.
   */
  public function loadMultiple() {
    return cache_get_multiple($this->cids);
  }

  /**
   * Resting the cache IDs property.
   *
   * @return AppsEntityRestrictionsReportsCacheManager
   */
  public function resetCacheIds() {
    $this->cids = array();

    return $this;
  }

  /**
   * Add a cache ID to the list of cache IDs.
   *
   * @param $id
   *   The cache ID.
   *
   * @return AppsEntityRestrictionsReportsCacheManager
   */
  public function addCacheId($id) {
    $this->cids[] = $id;

    return $this;
  }

}
