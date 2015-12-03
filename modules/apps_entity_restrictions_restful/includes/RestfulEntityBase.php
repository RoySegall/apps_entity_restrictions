<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBase.
 */

/**
 * @see AppsEntityRestrictionsRestful::publicFieldsInfo().
 */
abstract class AppsEntityRestrictionsRestfulBase extends RestfulEntityBase {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
