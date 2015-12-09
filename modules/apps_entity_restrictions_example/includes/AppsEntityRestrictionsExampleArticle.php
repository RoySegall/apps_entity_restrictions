<?php

/**
 * @file
 * Contains AppsEntityRestrictionsExampleArticle.
 */

class AppsEntityRestrictionsExampleArticle extends RestfulEntityBaseNode {

  use AppsEntityRestrictionsRestfulTrait;

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    $fields['body'] = array(
      'property' => 'body',
    );

    // Should be exposed by default but in this example it won't.
    $fields['tags'] = array(
      'property' => 'field_tags',
    );

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
