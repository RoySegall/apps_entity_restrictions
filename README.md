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
$app = apps_entity_restrictions_create();
$app->title = 'Demo application';
$app->need = array(
  'node' => array(
    'methods' => array('get'),
    'property' => array('nid', 'body'),
  ),
);
$app->save();
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
  $app->entityAccess('POST', 'user');
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
