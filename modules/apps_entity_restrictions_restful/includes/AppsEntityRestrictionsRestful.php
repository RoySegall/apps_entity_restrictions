<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestful
 */

class AppsEntityRestrictionsRestful {

  /**
   * @var AppsEntityRestrictionsRestful
   *
   * Hold cached object of the current application.
   */
  static $app;

  /**
   * Check if we in a restful context.
   *
   * @return bool
   */
  static public function restfulContext() {
    $base_path = variable_get('restful_hook_menu_base_path', 'api');

    return strpos($_GET['q'], $base_path . '/') === 0 && $_GET['q'] != $base_path;
  }

  /**
   * Return a simple array with the names representing the headers for the
   * application credentials.
   *
   * @return array
   * Array structured by:
   *  - public
   *  - secret
   */
  static public function getHeadersKeys() {
    return array(
      'public' => variable_get('apps_entity_restrictions_restful_public_public_key'),
      'secret' => variable_get('apps_entity_restrictions_restful_public_secret_key'),
    );
  }

  /**
   * Return true of if the application did not return the correct headers.
   *
   * @return bool
   */
  static public function applicationCredentialExists() {
    $keys = self::getHeadersKeys();

    $request = restful_parse_request();

    $public_passed = !empty($request['__application'][$keys['public']]);
    $secret_passed = !empty($request['__application'][$keys['secret']]);

    return $public_passed && $secret_passed;
  }

  /**
   * Loading the app by the headers.
   *
   * @param $request
   *   Passing Restful request object. When not empty the credentials will be
   *   calculated from the variable and not from the headers.
   *
   * @return AppsEntityRestriction
   *
   * @throws RestfulBadRequestException
   */
  static public function loadByHeaders($request = NULL) {
    if (self::$app) {
      return self::$app;
    }

    $keys = self::getHeadersKeys();

    if (!$request) {
      if (!self::applicationCredentialExists()) {
        throw new RestfulBadRequestException("You need to provide the application's credentials.");
      }

      $request = restful_parse_request();
    }

    $public = $request['__application'][$keys['public']];
    $secret = $request['__application'][$keys['secret']];

    self::$app = apps_entity_restrictions_load_by_keys($public, $secret);
    return self::$app;
  }

  /**
   * Alter the public fields definition by adding access callback.
   *
   * Restful have a base class for each entity. Apps entity restriction will
   * extend each controller and will override the public fields info. The
   * overridden is needed in order to add to each public field an access
   * callback.
   *
   * In that access callback, the app restrictions for that entity will be
   * checked and a boolean value will determine if the app have any access to
   * that property.
   *
   * @param RestfulBase $plugin
   *   The plugin instance.
   * @param $fields
   *   The public fields definition.
   *
   * @return array
   *   The list of public fields with the access callback array.
   */
  static public function publicFieldsInfo(RestfulBase $plugin, $fields) {

    foreach ($fields as $property => &$info) {
      if ($property == 'self') {
        // No need to check the safe public field. It's just a URL.
        continue;
      }

      $info['access_callbacks'][] = array('AppsEntityRestrictionsRestful', 'accessCallbacks');
    }

    return $fields;
  }

  /**
   * Public function access callbacks.
   *
   * @return bool
   */
  static public function accessCallbacks($op, $public_field_name, EntityMetadataWrapper $property_wrapper, EntityMetadataWrapper $wrapper) {

    if (!$app = self::loadByHeaders()) {
      // No application info available. Return early.
      return RestfulInterface::ACCESS_IGNORE;
    }

    $op_replacement = array(
      'view' => 'get',
      'edit' => 'post',
    );

    $info = $property_wrapper->info();

    if (empty($info['name'])) {
      return RestfulInterface::ACCESS_IGNORE;
    }

    if (!$app->entityPropertyAccess($op_replacement[$op], $wrapper->type(), $info['name'])) {
      // Track a bad
      $app->dispatch(array(
        'reason' => 'property_operation_not_allowed',
        'method' => $op,
        'entity_type' => $wrapper->type(),
      ));

      return RestfulInterface::ACCESS_DENY;
    }

    // A good request against a property should happen more often. There for
    // this won't be recorded since the records number got be very very high.
    return RestfulInterface::ACCESS_ALLOW;
  }

  /**
   * Helper static methods for all the base controllers.
   *
   * @param RestfulBase $controller
   *   The controllers instance.
   *
   * @return bool
   * @throws AppsEntityRestrictionsException
   * @throws RestfulBadRequestException
   */
  static public function checkEntityAccess(RestfulBase $controller) {
    if (!$controller->getApp()) {

      if (!$app = AppsEntityRestrictionsRestful::loadByHeaders($controller->getRequest())) {
        return FALSE;
      }

      $controller->setApp($app);
    }

    /** @var AppsEntityRestriction $app */
    $app = $controller->getApp();

    $method = $controller->getMethod();
    if (in_array($controller->getMethod(), array(RestfulInterface::PATCH, RestfulInterface::PUT))) {
      $method = 'update';
    }

    if (!$app->entityAccess(strtolower($method), $controller->getEntityType())) {
      // Return false and notify listeners for a bad request.
      $app->dispatch(array(
        'reason' => 'general_operation_not_allowed',
        'method' => $method,
        'entity_type' => $controller->getEntityType(),
      ));
      return FALSE;
    }

    // Notify listeners for a good request.
    $app->dispatch(array(
      'reason' => 'general_operation_allowed',
      'method' => $method,
      'entity_type' => $controller->getEntityType(),
    ));

    return TRUE;
  }

}
