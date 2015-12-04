<?php

/**
 * @file
 * Contains AppsEntityRestrictionsExampleArticle.
 */

class AppsEntityRestrictionsExampleArticle extends AppsEntityRestrictionsRestfulBaseNode {

  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    $fields['body'] = array(
      'property' => 'body',
    );

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
