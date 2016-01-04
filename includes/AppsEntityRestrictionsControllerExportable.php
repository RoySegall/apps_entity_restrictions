<?php

/**
 * Class AppsEntityRestrictionsControllerExportable.
 */
class AppsEntityRestrictionsControllerExportable extends EntityAPIControllerExportable {

  /**
   * {@inheritdoc}
   */
  public function _export($entity, $prefix = '') {
    // The secret need to set per environment the app is imported to.
    unset($entity->app_secret);
    return parent::export($entity, $prefix);
  }

}