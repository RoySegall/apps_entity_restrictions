<?php

class DrupalConnectApps extends Entity {

  /**
   * @var
   *
   * The primary identifier for a applications.
   */
  public $id;

  /**
   * @var
   *
   * The title of this application.
   */
  public $title;

  /**
   * @var
   *
   * A description of the application.
   */
  public $description;

  /**
   * @var
   *
   * The unix time stamp the app was created.
   */
  public $time;

  /**
   * @var
   *
   * The {users}.uid that owns this application.
   */
  public $uid;

  /**
   * @var
   *
   * The status of the app.
   */
  public $status;

  /**
   * @var
   *
   * The entity types from which the app will fetch the data.
   */
  public $need;

  /**
   * @var
   *
   * The key of the app.
   */
  public $app_key;

  /**
   * @var
   *
   * The secret of the app.
   */
  public $app_secret;

  /**
   * @var
   *
   * The site address which the data will be pulled from.
   */
  public $app_site;

  /**
   * @var
   *
   * Holds metadata information about the app.
   */
  public $metadata;

  /**
   * @var DrupalConnectAppsQuery
   */
  protected $query;

  /**
   * Setting the query object when accessing.
   */
  function query() {
    $this->query = new DrupalConnectAppsQuery($this);

    return $this->query;
  }

  /**
   * Overrides save().
   */
  public function save() {
    // Before saving verify the user did not changed the key and secret keys.
    parent::save();
  }

  /**
   * Generating links of the app.
   *
   * @param $action
   *   The type of the actions: edit, delete or documentation.
   * @param $account
   *   The account user. This will take care of permissions.
   *
   * @return String
   *   The link for the action.
   */
  public function generateLink($action, $account = NULL) {
    $actions = array(
      'edit' => array(
        'title' => t('Edit'),
        'href' => 'admin/apps/' . $this->id . '/edit',
      ),
      'delete' => array(
        'title' => t('Delete'),
        'href' => 'admin/apps/' . $this->id . '/delete',
      ),
      'approve' => array(
        'title' => t('Approve app'),
        'href' => 'admin/apps/' . $this->id . '/approve',
      ),
    );

    drupal_alter('apps_entity_restrictions_generate_links_action', $actions, $this);
    $item = menu_get_item($actions[$action]['href']);

    if (!$item['access']) {
      return;
    }

    return l($actions[$action]['title'], $actions[$action]['href']);
  }

  /**
   * Generate key and secret keys for a new application.
   */
  public function generateKeyAndSecret() {
    $user = user_load($this->uid);

    $this->app_key = str_replace(array(' ', '', '-'), '_', strtolower($this->title));
    $this->app_secret = md5($user->name . $this->time);
  }

  /**
   * Get a metadata value.
   *
   * @param $name
   *   The name of the metadata.
   */
  public function getMetaData($name) {
    return $this->metadata[$name];
  }

  /**
   * Set metadata information.
   *
   * @param $name
   *   The name of the metadata.
   * @param $value
   *   The value of the metadata.
   *
   * @return DrupalConnectApps
   */
  public function setMetaData($name, $value) {
    $this->metadata[$name] = $value;

    return $this;
  }

  /**
   * Check if the app support a specific method.
   *
   * @param string $data
   *   The entity type: node, user, comments etc. etc. etc.
   * @param string $method
   *   The method type: get, post.
   *
   * @return bool
   *   True or false if the app support this type of method.
   */
  public function supportMethod($data, $method) {
    return !empty($this->need[$data]['methods'][$method]);
  }

  /**
   * Check if the app support a specific asset.
   *
   * @param $asset
   *   The asset type: node, user, comment etc. etc.
   *
   * @return Bool
   *   Return true/false weather the app support the requested asset.
   */
  public function supportAsset($asset) {
    // todo: Complete the method.
  }

  /**
   * Check the entity access.
   *
   * @param $op
   *   The operation: create, read, update, delete.
   * @param $entity_type
   *   The entity type.
   *
   * @return bool
   * @throws AppsEntityRestrictionsException
   */
  public function entityAccess($op, $entity_type) {
    $entity_info = entity_get_info($entity_type);

    if (empty($this->need[$entity_type])) {
      throw new AppsEntityRestrictionsException(format_string('The app does not handle @name', array('@name' => $entity_info['label'])));
    }

    return in_array($op, $this->need[$entity_type]['methods']) ? TRUE : FALSE;
  }
}
