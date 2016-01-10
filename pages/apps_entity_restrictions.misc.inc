<?php
/**
 * @file
 * Manage here all the pages which not relate to CRUD functionality.
 */

/**
 * Display the main page of the apps.
 */
function apps_entity_restrictions_main_app_page() {
  $query = new entityFieldQuery();
  $result = $query
    ->entityCondition('entity_type', 'apps_entity_restrictions')
    ->execute();

  /** @var AppsEntityRestriction[] $apps */
  $apps = array();
  if (!empty($result['apps_entity_restrictions'])) {
    $apps = apps_entity_restrictions_load_multiple(array_keys($result['apps_entity_restrictions']));
  }

  $header = array(
    array('data' => t('App name'), 'width' => '40%'),
    array('data' => t('Information'), 'width' => '40%'),
    array('data' => t('Options')),
  );

  $rows = array();

  if ($apps) {
    foreach ($apps as $app) {
      $params = array(
        '@key' => $app->app_key,
        '@secret' => $app->app_secret,
      );

      $items = array();
      $items[] = $app->generateLink('edit');

      if ($app->isAppExported() !== FALSE) {
        $items[] = $app->generateLink('delete');
      }

      if (module_exists('devel') && user_access('access devel information')) {
        $items[] = $app->generateLink('devel');
      }

      // Let other module add their own links.
      drupal_alter('apps_entity_restrictions_app_options', $items, $app);

      $rows[] = array(
        $app->title . '<br />' . $app->description,
        t('Key: @key<br />Secret: @secret', $params),
        theme('item_list', array(
          'items' => $items,
          'attributes' => array('class' => array('links', 'inline')),
        )),
      );
    }
  }
  else {
    $rows[] = array(array('data' => t('No apps were found.'), 'colspan' => 3));
  }

  $table = array(
    'header' => $header,
    'rows' => $rows,
  );
  $content = theme('table', $table);

  return $content;
}
