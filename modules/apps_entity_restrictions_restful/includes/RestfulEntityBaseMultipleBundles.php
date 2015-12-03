<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulMultipleBundles.
 */

/**
 * @see AppsEntityRestrictionsRestful::propertyAccessCallbacks().
 */
class AppsEntityRestrictionsRestfulMultipleBundles extends RestfulEntityBaseMultipleBundles {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    AppsEntityRestrictionsRestful::propertyAccessCallbacks($this);

    return $fields;
  }

}
