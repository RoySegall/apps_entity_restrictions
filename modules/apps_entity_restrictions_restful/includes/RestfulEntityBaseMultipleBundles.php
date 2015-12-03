<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulMultipleBundles.
 */

/**
 * @see AppsEntityRestrictionsRestful::publicFieldsInfo().
 */
class AppsEntityRestrictionsRestfulMultipleBundles extends RestfulEntityBaseMultipleBundles {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
