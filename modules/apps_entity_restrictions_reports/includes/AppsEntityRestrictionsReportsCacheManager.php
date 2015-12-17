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
   * Get the cache ID of the current app.
   */
  protected function getCacheId() {

  }

  /**
   * Get the app.
   *
   * @return AppsEntityRestriction
   */
  public function getApp() {
    return $this->app;
  }

  public function updateDateCount() {

  }

  public function addDateCount() {

  }

  public function decreaseDateCount() {

  }

  public function invalidateAppCache() {

  }

  public function setAppCache() {

  }

}
