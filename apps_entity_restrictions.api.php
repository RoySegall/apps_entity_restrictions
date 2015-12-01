<?php

/**
 * @file
 * Describing the hooks available via Apps entity restrictions module.
 */

/**
 * Implements hook_apps_entity_restrictions_generate_links_action_alter().
 *
 * Each app entity restriction entity has a method for generate links. The
 * method is helpful when we want to create a link relate to action we need
 * apply on the entity: editing, deleting or watching the documentation of the
 * app.
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
 * @param AppsEntityRestriction $app
 *   The app object.
 */
function hook_apps_entity_restrictions_generate_links_action_alter(&$actions, AppsEntityRestriction $app) {
  $actions['new_action'] = array(
    'title' => t('New action'),
    'href' => 'apps/' . $app->id . '/new_action',
  );
}

/**
 * Implements hook_apps_entity_restrictions_app_options_alter().
 *
 * On the admin page, each app has an options which the user can apply on app.
 * This hook allow you to add more option for each application.
 *
 * @param $items
 *   The links to display for the user.
 * @param AppsEntityRestriction $app
 *   The app object.
 */
function hook_apps_entity_restrictions_app_options_alter(&$items, AppsEntityRestriction $app) {
  $items[] = $app->generateLink('new_action');
}

/**
 * Implements hook_apps_entity_restrictions_access().
 *
 * Allow other module to grant action for a specific action. This will consider
 * if other modules has different logic for access permission which are not edit
 * or delete or just want to bypass the apps_entity_restrictions_access
 * function.
 *
 * The hook_apps_entity_restrictions_access() need to return TRUE or FALSE.
 * @see hook_node_access().
 *
 * @param $access
 *   The access name.
 * @param AppsEntityRestriction $app
 *   The app object
 * @param $account
 *   The loaded user account.
 *
 * @return bool
 *   Need to return TRUE/FALSE.
 */
function hook_apps_entity_restrictions_access($access, AppsEntityRestriction $app, $account) {
}

/**
 * Implements hook_apps_entity_restrictions_entity_ignore().
 *
 * Allow modules to hide their entities from being display when creating an app.
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
