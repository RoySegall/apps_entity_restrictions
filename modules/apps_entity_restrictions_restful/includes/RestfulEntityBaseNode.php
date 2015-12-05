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

    if (!$this->app = AppsEntityRestrictionsRestful::loadByHeaders($this->request)) {
      return FALSE;
    }

    return $access && $this->app->entityAccess(strtolower($this->getMethod()), $this->getEntityType());
  }

}
