<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBaseNode.
 */

/**
 * @see AppsEntityRestrictionsRestful::propertyAccessCallbacks().
 */
class AppsEntityRestrictionsRestfulBaseNode extends RestfulEntityBaseNode {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    AppsEntityRestrictionsRestful::propertyAccessCallbacks($this);

    return $fields;
  }

}
