<?php

/**
 * @file
 * Contains AppsEntityRestrictionsExampleArticle.
 */

class AppsEntityRestrictionsExampleArticle extends AppsEntityRestrictionsRestfulBaseNode {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    $fields['body'] = array(
      'property' => 'body',
    );

    // Should be exposed by default.
    $fields['tags'] = array(
      'property' => 'field_tags',
    );

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
