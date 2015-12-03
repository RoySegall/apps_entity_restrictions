<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestfulBaseTaxonomyTerm.
 */

/**
 * @see AppsEntityRestrictionsRestful::propertyAccessCallbacks().
 */
class AppsEntityRestrictionsRestfulBaseTaxonomyTerm extends RestfulEntityBaseTaxonomyTerm {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    AppsEntityRestrictionsRestful::propertyAccessCallbacks($this);

    return $fields;
  }

}
