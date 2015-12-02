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
  public static function loadByHeaders() {
    if (!self::applicationCredentialExists()) {
      throw new RestfulBadRequestException("You need to provide the application's credentials.");
    }

    $keys = self::getHeadersKeys();
    $request = restful_parse_request();

    return apps_entity_restrictions_load_by_keys($request['__application'][$keys['public']], $request['__application'][$keys['secret']]);
  }
}