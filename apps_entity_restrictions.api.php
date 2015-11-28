<?php

/**
 * @file
 * Describing the hooks available via Drupal apps connect module.
 */

/**
 * Implements hook_apps_entity_restrictions_generate_links_action_alter().
 *
 * Each Drupal connect apps entity has a method for generate links. The method
 * is helpful when we want to create a link relate to action we need apply on
 * the entity: editing, deleting or watching the documentation of the app.
 *
 * When other module want to add a new action and want to use the method for
 * generating the link for the action they should use these hook.
 *
 * Using the generateLink methods will be similar like this:
 *  $app = apps_entity_restrictions_load(1);
 *  $link = $app->generateLink('new_methods');
 *
 * @param $actions
 *   List of actions that which the method can use.
 * @param DrupalConnectApps $app
 *   The app object.
 */
function hook_apps_entity_restrictions_generate_links_action_alter(&$actions, DrupalConnectApps $app) {
  $actions['new_action'] = array(
    'title' => t('New action'),
    'href' => 'apps/' . $app->id . '/new_action',
  );
}

/**
 * Implements hook_apps_entity_restrictions_app_options_alter().
 *
 * On the admin page each app has options that the user can apply on app. This
 * hook allow you to add more option for each application.
 *
 * @param $items
 *   The links to display for the user.
 * @param DrupalConnectApps $app
 *   The app object.
 */
function hook_apps_entity_restrictions_app_options_alter(&$items, DrupalConnectApps $app) {
  $items[] = $app->generateLink('new_action');
}

/**
 * Implements hook_apps_entity_restrictions_access().
 *
 * Allow other module to grant action for a specific action. This will consider
 * if other modules has different logic for access permission which are not edit
 * or delete or just want to bypass the apps_entity_restrictions_access function.
 *
 * The hook_apps_entity_restrictions_access() need to return TRUE or FALSE.
 * @see hook_node_access().
 *
 * @param $access
 *   The access name.
 * @param DrupalConnectApps $app
 *   The app object
 * @param $account
 *   The loaded user account.
 *
 * @return bool
 *   Need to return TRUE/FALSE.
 */
function hook_apps_entity_restrictions_access($access, DrupalConnectApps $app, $account) {
}

/**
 * Implements hook_apps_entity_restrictions_entity_ignore().
 *
 * Allow to module decide which apps can be selected when creating a new app.
 *
 * @return array
 *   Array of entities machine name to be ignored when creating a new app.
 */
function hook_apps_entity_restrictions_entity_ignore() {
  return array(
    'foo',
    'bar',
  );
}

/**
 * Alter the list of the ignored apps.
 *
 * @param $implements
 *   List of the entities machine name.
 */
function hook_apps_entity_restrictions_entity_ignore_alter(&$implements) {
  unset($implements['foo']);
}
