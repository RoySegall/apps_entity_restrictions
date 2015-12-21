<?php

/**
 * @file
 * Contains AppsEntityRestrictionsReportsCacheManagerBase
 */

abstract class AppsEntityRestrictionsReportsCacheManagerBase {

  /**
   * @var AppsEntityRestrictionsReportsCacheManager
   *
   * The cache manager instance.
   */
  protected $cacheManager;

  /**
   * Constructing the object.
   *
   * @param AppsEntityRestrictionsReportsCacheManager $cacheManager
   *   The cache manager.
   */
  function __construct(AppsEntityRestrictionsReportsCacheManager $cacheManager) {
    $this->cacheManager = $cacheManager;
  }

}
