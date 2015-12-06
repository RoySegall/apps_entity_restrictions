<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBaseNode.
 */

/**
 * @see AppsEntityRestrictionsRestful::publicFieldsInfo().
 */
class AppsEntityRestrictionsRestfulBaseNode extends RestfulEntityBaseNode {

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
   * @return AppsEntityRestrictionsRestfulBaseNode
   */
  public function setApp($app) {
    $this->app = $app;
    AppsEntityRestrictionsRestful::$app = $app;
    return $this;
  }

  /**
   * Clearing the app static cache.
   *
   * @return AppsEntityRestrictionsRestfulBaseNode
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
    $access =  parent::checkEntityAccess($op, $entity_type, $entity);

    if (!$this->app = AppsEntityRestrictionsRestful::loadByHeaders($this->request)) {
      return FALSE;
    }

    $method = $this->getMethod();
    if (in_array($this->getMethod(), array(RestfulInterface::PATCH, RestfulInterface::PUT))) {
      $method = 'update';
    }

    return $access && $this->app->entityAccess(strtolower($method), $this->getEntityType());
  }

}
