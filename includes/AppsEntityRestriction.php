<?php

/**
 * @file
 *
 * Contains \AppsEntityRestriction
 */

class AppsEntityRestriction extends Entity {

  /**
   * @var integer
   *
   * The primary identifier for a applications.
   */
  protected $id;

  /**
   * @var string
   *
   * The title of this application.
   */
  protected $title;

  /**
   * @var string
   *
   * A description of the application.
   */
  protected $description;

  /**
   * @var string
   *
   * The unix time stamp the app was created.
   */
  protected $time;

  /**
   * @var integer
   *
   * The {users}.uid that owns this application.
   */
  protected $uid;

  /**
   * @var bool
   *
   * The status of the app.
   */
  protected $status;

  /**
   * @var array
   *
   * The entity types from which the app will fetch the data.
   */
  protected $need;

  /**
   * @var string
   *
   * The key of the app.
   */
  protected $app_key;

  /**
   * @var string
   *
   * The secret of the app.
   */
  protected $app_secret;

  /**
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param integer $id
   *   The identifier of the application.
   *
   * @return AppsEntityRestriction
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $title
   *   The application title.
   *
   * @return AppsEntityRestriction
   */
  public function setTitle($title) {
    $this->title = $title;

    return $this;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   *   description of the application.
   *
   * @return AppsEntityRestriction
   */
  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  /**
   * @return integer
   */
  public function getTime() {
    return $this->time;
  }

  /**
   * @param integer $time
   *   Timestamp of the creation.
   *
   * @return AppsEntityRestriction
   */
  public function setTime($time) {
    $this->time = $time;

    return $this;
  }

  /**
   * @return integer
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * @param integer $uid
   *   The owner's ID.
   *
   * @return AppsEntityRestriction
   */
  public function setUid($uid) {
    $this->uid = $uid;

    return $this;
  }

  /**
   * @return bool
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param bool $status
   *   Determine if the application active or not.
   *
   * @return AppsEntityRestriction
   */
  public function setStatus($status) {
    $this->status = $status;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getNeed() {
    return $this->need;
  }

  /**
   * @param array $need
   *   The restrictions settings.
   *
   * @return AppsEntityRestriction
   */
  public function setNeed($need) {
    $this->need = $need;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getAppKey() {
    return $this->app_key;
  }

  /**
   * @param string $app_key
   *   The application public key.
   *
   * @return AppsEntityRestriction
   */
  public function setAppKey($app_key) {
    $this->app_key = $app_key;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getAppSecret() {
    return $this->app_secret;
  }

  /**
   * @param string $app_secret
   *   The secret key.
   *
   * @return AppsEntityRestriction
   */
  public function setAppSecret($app_secret) {
    $this->app_secret = $app_secret;

    return $this;
  }

  /**
   * Adding a info to the need key.
   *
   * @param $entity
   *   The entity type.
   * @param $realm
   *   The realm - properties or methods.
   * @param $property
   *   The field name.
   *
   * @return AppsEntityRestriction
   */
  public function addNeed($entity, $realm, $property) {
    $this->need[$entity][$realm][] = $property;
    return $this;
  }

  /**
   * Removing a info from the need key.
   *
   * @param $entity
   *   The entity type.
   * @param $realm
   *   The realm - properties or methods.
   * @param $property
   *   The field name.
   *
   * @return AppsEntityRestriction
   */
  public function removeNeed($entity, $realm, $property) {
    $property_key = array_search($property, $this->need[$entity][$realm]);
    unset($this->need[$entity][$realm][$property_key]);
    return $this;
  }

  /**
   * Generating links of the app.
   *
   * @param $action
   *   The type of the actions: edit, delete or documentation.
   * @param $account
   *   The account user. This will take care of permissions.
   *
   * @return string
   *   The link for the action.
   */
  protected function generateLink($action, $account = NULL) {
    $actions = array(
      'edit' => array(
        'title' => t('Edit'),
        'href' => 'admin/apps/' . $this->id . '/edit',
      ),
      'delete' => array(
        'title' => t('Delete'),
        'href' => 'admin/apps/' . $this->id . '/delete',
      ),
      'devel' => array(
        'title' => t('Devel'),
        'href' => 'admin/apps/' . $this->id . '/devel',
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
  protected function generateKeyAndSecret() {
    $user = user_load($this->uid);

    $this->app_key = str_replace(array(' ', '', '-'), '_', strtolower($this->title));
    $this->app_secret = md5($user->name . $this->time . $this->app_key);
  }

  /**
   * Overrides Entity::save().
   *
   * Generate credentials when not provided.
   */
  public function save() {
    if (empty($this->app_key) || empty($this->app_secret)) {
      $this->generateKeyAndSecret();
    }

    return parent::save();
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

  /**
   * Check entity property access.
   *
   * @param $op
   *   The operation: create, read, update, delete.
   * @param $entity_type
   *   The entity type.
   * @param $property
   *   The property: nid, vid, field_date etc. etc.
   *
   * @return bool
   */
  public function entityPropertyAccess($op, $entity_type, $property) {
    if (!$this->entityAccess($op, $entity_type)) {
      return FALSE;
    }

    // Remove after changing the form to keep just the arrays of the methods.
    return in_array($property, $this->need[$entity_type]['properties']) ? TRUE : FALSE;
  }

}
