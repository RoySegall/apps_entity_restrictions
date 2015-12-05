# Apps entity restrictions
[![Build Status](https://api.travis-ci.org/RoySegall/apps_entity_restrictions.svg?branch=7.x-1.x)](https://travis-ci.org/RoySegall/apps_entity_restrictions)
## General info
When your Drupal site need to act as a backend you would like to restrict third
side application for a specific given set of entities and a very specific set of
fields. This module, Apps entity restrictions, give you the option to create application
representation. Each record will have a public key and a secret key and have a
list of allowed method he can apply for each entity and to which properties and
fields.

This module won't implement any logic for you and this is your responsibility
to hook in `hook_field_access`, `hook_entity_access` and restrict your exposed
properties in restful public fields info method.

## API
Except for a nice UI under `admin/structure/apps` there is a nice API.

Create a new application:
```php
<?php
$app
  ->setTitle('Demo application')
  ->allow('node', 'methods', 'get')
  ->allow('node', 'properties', 'nid')
  ->allow('node', 'properties', 'body')
  ->save();
```

Loading an application by keys:
```php
<?php
$app = apps_entity_restrictions_load_by_keys($_GET['key'], $_GET['secret']);
```

Checking access:
```php
<?php
// Check general access.
try {
  $app->entityAccess('get', 'user');
} catch (\AppsEntityRestrictionsException $e) {
  drupal_set_message(t('This apps have no support to the user entity.', 'error'));
}
// Check access for specific property.
if (!$app->entityAccess('get', 'node')) {
  drupal_set_message(t('This apps have no access in GET request for a node.', 'error'));
}

// Check property access.
if (!$app->entityPropertyAccess('get', 'node', 'nid')) {
  drupal_set_message(t("This apps have no access in GET request for the node's nid.", 'error'));
}

// Check field access.
if (!$app->entityPropertyAccess('get', 'node', 'field_date')) {
  drupal_set_message(t("This apps have no access in GET request for the node's date field.", 'error'));
}

```

## Restful integration
Your [restful](http://drupal.org/project/restful) endpoints could also benefit
from the logic of Apps entity restrictions. You'd need to enable the module
`Apps entity restrictions Restful`. The module come with couple of base classes:
  * `AppsEntityRestrictionsRestfulBase` - extends `RestfulEntityBase`
  * `AppsEntityRestrictionsRestfulBaseNode` - extends `RestfulEntityBaseNode`
  * `AppsEntityRestrictionsRestfulBaseTaxonomyTerm` - extends `RestfulEntityBaseTaxonomyTerm`
  * `AppsEntityRestrictionsRestfulMultipleBundles` - extends `RestfulEntityBaseMultipleBundles`


The extension was needed for two reasons:
  * Add to the access method the entity access logic of Apps entity
    restrictions.
  * Add to each public field definition from `parent::publicFieldsInfo` a callback
    access.

Your end point would need define more public fields. In order to wrap them
easily with the access callback you could implement the public fields info
similar to this:
```php
<?php

class AppsEntityRestrictionsExampleArticle extends AppsEntityRestrictionsRestfulBaseNode {

  /**
   * {@inheritdoc}
   */
  public function publicFieldsInfo() {
    $fields = parent::publicFieldsInfo();

    $fields['body'] = array(
      'property' => 'body',
    );

    $fields['tags'] = array(
      'property' => 'field_tags',
    );

    return AppsEntityRestrictionsRestful::publicFieldsInfo($this, $fields);
  }

}
```
