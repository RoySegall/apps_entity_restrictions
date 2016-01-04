<?php
/**
 * @file
 * Manage here all the pages which relate to CRUD functionality.
 */

/**
 * Manage an app entity.
 */
function apps_entity_restrictions_app_form($form, &$form_state, AppsEntityRestriction $app = NULL) {
  if (!$app) {
    $app = apps_entity_restrictions_create();
  }

  $form_state['#entity'] = $app;

  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#description' => t('The name of the application.'),
    '#required' => TRUE,
    '#default_value' => $app->title,
  );

  $form['app_key'] = array(
    '#type' => 'machine_name',
    '#default_value' => $app->app_key,
    '#maxlength' => 32,
    '#disabled' => !$app->is_new,
    '#machine_name' => array(
      'exists' => 'apps_entity_restrictions_app_exists',
    ),
    '#description' => t('A unique machine-readable name for this content type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %node-add page, in which underscores will be converted into hyphens.', array(
      '%node-add' => t('Add new content'),
    )),
  );

  $form['description'] = array(
    '#type' => 'textfield',
    '#title' => t('Description'),
    '#description' => t('The description of the application'),
    '#default_value' => $app->description,
  );

  // Get the entities and sort them in alphabetical order.
  $entities = entity_get_info();

  ksort($entities);
  $ignored = apps_entity_restrictions_ignored_apps();

  foreach ($entities as $name => $info) {
    if (in_array($name, $ignored)) {
      // This entity should be displayed to the app creator.
      continue;
    }

    $need_info = empty($app->need[$name]) ? array() : $app->need[$name];

    $form['need'][$name] = array(
      '#type' => 'container',
      '#tree' => true,
    );

    $form['need'][$name][$name . '_need'] = array(
      '#type' => 'checkbox',
      '#title' => $info['label'],
      '#default_value' => !empty($app->need[$name]) && apps_entity_restrictions_is_selected($app->need[$name]),
      '#tree' => true,
    );

    $form['need'][$name]['exposed'] = array(
      '#type' => 'fieldset',
      '#states' => array(
        'visible' => array(
          ':input[name="' . $name . '[' . $name . '_need]"]' => array('checked' => TRUE),
        ),
      ),
    );

    $exposed = &$form['need'][$name]['exposed'];

    $exposed['methods'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Actions'),
      '#options' => array(
        'get' => t('Get'),
        'post' => t('Post'),
        'delete' => t('Delete'),
        'update' => t('Update'),
      ),
      '#attributes' => array('class' => array('container-inline')),
      '#default_value' => !empty($need_info['methods']) ? $need_info['methods'] : array(),
    );

    $exposed['properties'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Exposed info'),
      '#options' => apps_entity_restrictions_build_fields($name),
      '#default_value' => !empty($need_info['properties']) ? $need_info['properties'] : array(),
    );
  }

  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => 99,
  );

  $form['author'] = array(
    '#type' => 'fieldset',
    '#access' => user_access('administer nodes'),
    '#title' => t('Authoring information'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'additional_settings',
    '#weight' => 90,
  );

  $form['author']['owner'] = array(
    '#type' => 'textfield',
    '#title' => t('Authored by'),
    '#maxlength' => 60,
    '#autocomplete_path' => 'user/autocomplete',
    '#default_value' => user_load($app->uid)->name,
    '#weight' => -1,
    '#description' => t('Leave blank for %anonymous.', array('%anonymous' => variable_get('anonymous', t('Anonymous')))),
  );

  $form['oauth'] = array(
    '#type' => 'fieldset',
    '#access' => user_access('administer nodes'),
    '#title' => t('oAuth information'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'additional_settings',
    '#weight' => 100,
  );

  $form['oauth']['key'] = array(
    '#type' => 'item',
    '#title' => t('Key'),
    '#markup' => $app->app_key ? $app->app_key : t('Need be generated'),
  );

  $form['oauth']['secret'] = array(
    '#type' => 'item',
    '#title' => t('Secret'),
    '#markup' => $app->app_secret ? $app->app_secret : t('Need be generated'),
  );

  $form['actions'] = array(
    '#type' => 'actions',
    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    ),
    'cancel' => array(
      '#type' => 'markup',
      '#markup' => l(t('Cancel'), 'admin/apps'),
    ),
  );

  return $form;
}

/**
 * @param $public
 * @return bool
 */
function apps_entity_restrictions_app_exists($public) {
  return apps_entity_restrictions_load_by_keys($public, NULL) ? TRUE : FALSE;
}

/**
 * Validate handler.
 */
function apps_entity_restrictions_app_form_validate($form, $form_state) {
  // App name should be only in english.
  if (preg_match('/[^A-Za-z0-9 ]/', $form_state['values']['name'])) {
    form_set_error('title', t('The name of the app should be only in english.'));
  }

  $values = $form_state['values'];

  // The app must user at least one entity.
  $entities = array_keys(entity_get_info());
  $entity_selected = FALSE;
  foreach ($entities as $entity) {
    if (empty($values[$entity])) {
      // Entity was not selected.
      continue;
    }

    if ($entity_selected = apps_entity_restrictions_is_selected($values[$entity]['exposed'])) {
      break;
    }
  }

  if (!$entity_selected) {
    form_set_error('', t('A basic app must use at least one entity.'));
  }

  field_attach_form_validate('apps_entity_restrictions', $form_state['#entity'], $form, $form_state);
}

/**
 * Submit handler.
 */
function apps_entity_restrictions_app_form_submit($form, &$form_state) {
  /** @var AppsEntityRestriction $app */
  $app = $form_state['#entity'];
  $values = $form_state['values'];

  // Star building information about entity.
  $entities = array_keys(entity_get_info());
  $need = array();

  foreach ($entities as $entity) {
    if (in_array($entity, apps_entity_restrictions_ignored_apps())) {
      continue;
    }

    if (!$values[$entity][$entity . '_need']) {
      // Entity was not selected.
      continue;
    }

    $methods = $values[$entity]['exposed']['methods'];
    $properties = $values[$entity]['exposed']['properties'];

    if (!apps_entity_restrictions_is_selected($values[$entity]['exposed'])) {
      continue;
    }

    $need[$entity] = array(
      'methods' => array_filter(array_values($methods)),
      'properties' => array_filter(array_values($properties)),
    );
  }

  // Building the entity object.
  $app->title = $values['name'];
  $app->app_key = $values['app_key'];
  $app->description = $values['description'];
  $app->need = $need;
  $app->uid = user_load_by_name($values['owner'])->uid;

  // Generating secret and public for the entity.
  if (!empty($app->is_new)) {
    $op = t('created');
    $app->generateKeyAndSecret();
  }
  else {
    $op = t('updated');
  }

  // Update/save the entity.
  $app->save();

  // Showing messages.
  $params = array(
    '@name' => $app->title,
    '@op' => $op,
  );
  drupal_set_message(t('The app @name has @op successful.', $params));

  if (!empty($app->need['user'])) {
    drupal_set_message(t('Your app need the user information. This could be achieved only when the user approve the app.'), 'warning');
  }

  // Display message and redirect the user back to the main apps page.
  $form_state['redirect'] = 'admin/apps';
}

/**
 * Ask the user if he wan't to delete the app.
 */
function apps_entity_restrictions_app_delete($form, &$form_state, AppsEntityRestriction $app) {
  $form_state['#entity'] = $app;
  return confirm_form($form,
    t('Are you sure you want to delete %title?', array('%title' => $app->title)),
    'admin/apps',
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );
}

/**
 * Deleting the app.
 */
function apps_entity_restrictions_app_delete_submit($form, &$form_state) {
  // Delete.
  $form_state['#entity']->delete();

  // Show message.
  drupal_set_message(t('The app @name has deleted successfully', array('@name' => $form_state['#entity']->title)));

  // Redirect user.
  $form_state['redirect'] = 'admin/apps';
}
