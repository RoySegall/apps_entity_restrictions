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
   * @var AppsEntityRestrictionsReportsMonthsCacheManager
   */
  protected $monthsManager;

  /**
   * @var AppsEntityRestrictionsReportsMonthsCacheManager
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

    $this->monthsManager = new AppsEntityRestrictionsReportsMonthsCacheManager($app);
    $this->hitsManager = new AppsEntityRestrictionsReportsHitsCacheManager($app);
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
   * @return AppsEntityRestrictionsReportsMonthsCacheManager
   */
  public function getMonthsManager() {
    return $this->monthsManager;
  }

  /**
   * @return AppsEntityRestrictionsReportsMonthsCacheManager
   */
  public function getHitsManager() {
    return $this->hitsManager;
  }

}
