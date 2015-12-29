<?php

/**
 * @file
 * Contain AppsEntityRestrictionsRestfulTrait
 */
trait AppsEntityRestrictionsRestfulTrait {

  /**
   * @var AppsEntityRestriction
   *
   * The application instance.
   */
  protected $app;

  /**
   * @return AppsEntityRestriction
   */
  public function getApp() {
    return $this->app;
  }

  /**
   * Setting the app object for the current controller and the static methods as
   * well.
   *
   * @param AppsEntityRestriction $app
   *   The application instance.
   *
   * @return AppsEntityRestrictionsRestfulTrait
   */
  public function setApp($app) {
    $this->app = $app;
    AppsEntityRestrictionsRestful::$app = $app;
    return $this;
  }

  /**
   * Clearing the app static cache.
   *
   * @return AppsEntityRestrictionsRestfulTrait
   */
  public function cleanApp() {
    $this->app = NULL;
    AppsEntityRestrictionsRestful::$app = NULL;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

  /**
   * {@inheritdoc}
   */
  public function checkEntityAccess($op, $entity_type, $entity) {
    $info = $this->getEntityInfo($entity_type);

    if (empty($info['access callback'])) {
      // The current entity type does not have any access callback there for we
      // only need to the app access callback.
      return AppsEntityRestrictionsRestful::checkEntityAccess($this);
    }

    return parent::checkEntityAccess($op, $entity_type, $entity);
  }

}
