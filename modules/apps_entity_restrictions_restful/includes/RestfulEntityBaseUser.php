<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBaseUser.
 */

/**
 * @see AppsEntityRestrictionsRestful::publicFieldsInfo().
 */
class AppsEntityRestrictionsRestfulBaseUser extends RestfulEntityBaseUser {

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
   * @return AppsEntityRestrictionsRestfulBaseUser
   */
  public function setApp($app) {
    $this->app = $app;
    AppsEntityRestrictionsRestful::$app = $app;
    return $this;
  }

  /**
   * Clearing the app static cache.
   *
   * @return AppsEntityRestrictionsRestfulBaseUser
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
  public function access() {
    $access = parent::access();

    if (!$this->app = AppsEntityRestrictionsRestful::loadByHeaders()) {
      throw new \RestfulBadRequestException('Application with the passed credential was not found. Please try again.');
    }

    return $access && $this->app->entityAccess(strtolower($this->getMethod()), $this->getEntityType());
  }

}
