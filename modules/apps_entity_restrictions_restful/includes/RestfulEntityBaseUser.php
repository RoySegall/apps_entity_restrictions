<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBaseUser.
 */

/**
 * @see AppsEntityRestrictionsRestful::propertyAccessCallbacks().
 */
class AppsEntityRestrictionsRestfulBaseUser extends RestfulEntityBaseUser {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    AppsEntityRestrictionsRestful::propertyAccessCallbacks($this);

    return $fields;
  }

}
