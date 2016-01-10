<?php
/**
 * @file
 * apps_entity_restrictions_features.features.inc
 */

/**
 * Implements hook_hook_default_apps_entity_restrictions().
 */
function apps_entity_restrictions_features_hook_default_apps_entity_restrictions() {
  $items = array();
  $items['demo_app_deployment'] = entity_import('apps_entity_restrictions', '{
    "title" : "Demo app deployment",
    "description" : "Demonstrating how an app can be deployed using features.",
    "time" : "1452459188",
    "uid" : "1",
    "need" : {
      "comment" : { "methods" : [ "get" ], "properties" : [ "comment_body" ] },
      "node" : { "methods" : [ "get" ], "properties" : [ "nid", "body" ] }
    },
    "app_key" : "demo_app_deployment",
    "app_secret" : "a2b28c482be50e04fbe695493dc5a43b",
    "rdf_mapping" : []
  }');
  return $items;
}
