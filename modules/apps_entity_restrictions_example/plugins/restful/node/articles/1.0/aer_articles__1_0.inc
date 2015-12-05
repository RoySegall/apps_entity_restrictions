<?php

$plugin = array(
  'label' => t('Apps entity restrictions example: article'),
  'resource' => 'aer_articles',
  'name' => 'aer_articles__1_0',
  'entity_type' => 'node',
  'bundle' => 'article',
  'description' => t('Apps entity restrictions integration example with article bundle.'),
  'class' => 'AppsEntityRestrictionsExampleArticle__1_0',
  'authentication_types' => TRUE,
  'authentication_optional' => TRUE,
);
