<?php

/**
 * @file
 * Contains AppsEntityRestrictionsExampleArticle.
 */

class AppsEntityRestrictionsExampleArticle extends AppsEntityRestrictionsRestfulBaseNode {

  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
