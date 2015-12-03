<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBase.
 */

/**
 * @see AppsEntityRestrictionsRestful::propertyAccessCallbacks().
 */
abstract class AppsEntityRestrictionsRestfulBase extends RestfulEntityBase {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    AppsEntityRestrictionsRestful::propertyAccessCallbacks($this);

    return $fields;
  }

}
