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