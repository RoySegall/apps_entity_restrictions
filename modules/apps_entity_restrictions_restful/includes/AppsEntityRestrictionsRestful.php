<?php

/**
 * @file
 * Contains AppsEntityRestrictionsRestful
 */

class AppsEntityRestrictionsRestful {

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
   * @return AppsEntityRestriction
   *
   * @throws RestfulBadRequestException
   */
  static public function loadByHeaders() {
    if (!self::applicationCredentialExists()) {
      throw new RestfulBadRequestException("You need to provide the application's credentials.");
    }

    $keys = self::getHeadersKeys();
    $request = restful_parse_request();

    return apps_entity_restrictions_load_by_keys($request['__application'][$keys['public']], $request['__application'][$keys['secret']]);
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
    );

    $info = $property_wrapper->info();

    if (empty($info['name'])) {
      return RestfulInterface::ACCESS_IGNORE;
    }

    return $app->entityPropertyAccess($op_replacement[$op], $wrapper->type(), $info['name']) ? RestfulInterface::ACCESS_ALLOW : RestfulInterface::ACCESS_DENY;
  }

}
