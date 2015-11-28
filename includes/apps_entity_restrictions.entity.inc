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
   * Define if this a connect with application
   */
  public $connect_with;

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
}

/**
 * Class DrupalConnectAppsQuery
 *
 * This class designed in order to have a clean query API against the app assets
 * from the app object.
 *
 * The class is very to use. After loading an object you can access from the app
 * or by initialize the class and passing an app object. The query method in the
 * app object will take care of constructing this class.
 *
 * After setting up the class you need to set the asset. Using the asset method
 * you can set the the entity field query object. After the EFQ object will
 * initialized you can start using the commons method of EFQ like: property
 * condition, field condition and range. Other methods will be accessible via
 * the method invokeEntityFieldQuery. In the end you can fire up the execute
 * method and the EFQ you built will return results like a normal EFQ.
 *
 * You can also see the code below as an example:
 * @code
 *  $app = apps_entity_restrictions_load(1);
 *  $result = $app
 *    ->query()
 *    ->asset('node')
 *    ->propertyCondition('nid', 1)
 *    ->execute();
 * @encode
 */
class DrupalConnectAppsQuery {

  /**
   * @var DrupalConnectApps
   *
   * The app we working against.
   */
  protected $app;

  /**
   * @var
   * The assert we are working with: node, user, comment etc. etc.
   */
  protected $asset;

  /**
   * @var entityFieldQuery
   *
   * A variable which holds the entity field query object.
   */
  protected $query;

  /**
   * @param DrupalConnectApps $app
   *  The app fully loaded app.
   */
  public function __construct(DrupalConnectApps $app) {
    $this->app = $app;
  }

  /**
   * Accessing an app asset. i.e: user, node.
   *
   * @param $name
   *  The asset name: node, user etc. etc.
   *
   * @throws Exception
   *  Throw an exception when the app asset don't support get method.
   *
   * @return DrupalConnectAppsQuery
   */
  public function asset($name) {
    if (!$this->app->supportMethod('node', 'get')) {
      $params = array(
        '@method' => t('get'),
        '@asset' => t('node'),
      );
      throw new Exception(t("The app don't support the @method for @asset", $params));
    }

    $this->asset = $name;
    $this->query = new EntityFieldQuery();
    $this->query->entityCondition('entity_type', $this->asset);
    return $this;
  }

  /**
   * Invoking command for the entity field query.
   *
   * @param $method
   *  The method of the entity field query.
   * @param $arguments
   *  An array of arguments to pass to the method.
   *
   * @throws Exception
   * @return DrupalConnectAppsQuery
   */
  public function invokeEntityFieldQuery($method, $arguments = array()) {
    if (!$this->asset) {
      throw new Exception(t('The class asset was not defined.'));
    }

    if (!$this->query) {
      throw new Exception(t('The query object was initialized.'));
    }

    call_user_func_array(array($this->query, $method), $arguments);

    return $this;
  }

  /**
   * Adding a property to the entity field query.
   *
   * @param $property
   *  The property of the entity.
   * @param $value
   *  The value of the property.
   * @param string $operator
   *  The operator. Set default to =.
   *
   * @return DrupalConnectAppsQuery
   */
  public function propertyCondition($property, $value, $operator = '=') {
    $this->invokeEntityFieldQuery('propertyCondition', array($property, $value, $operator));

    return $this;
  }

  /**
   * Adding condition by field.
   *
   * @param $field
   *  Either a field name or a field array.
   * @param $column
   *  The column that should hold the value to be matched.
   * @param $value
   *  The value to test the column value against.
   * @param $operator
   *  The operator to be used to test the given value.
   * @param $delta_group
   *  An arbitrary identifier: conditions in the same group must have the same
   *  $delta_group.
   * @param $language_group
   *  An arbitrary identifier: conditions in the same group must have the same
   *  $language_group.
   *
   * @return DrupalConnectAppsQuery
   */
  public function fieldCondition($field, $column = NULL, $value = NULL, $operator = NULL, $delta_group = NULL, $language_group = NULL) {
    $this->invokeEntityFieldQuery('fieldCondition', array($field, $column, $value, $operator, $delta_group, $language_group));
    return $this;
  }

  /**
   * Restricts a query to a given range in the result set.
   *
   * @param $start
   *  The first entity from the result set to return. If NULL, removes and range
   *  directives that are set.
   * @param $length
   *  The number of entities to return from the result set.
   *
   * @return DrupalConnectAppsQuery
   */
  public function range($start, $length) {
    $this->invokeEntityFieldQuery('range', array($start, $length));
    return $this;
  }

  /**
   * Executing the entity field query.
   */
  public function execute() {
    $results = $this->query->execute();

    return empty($results[$this->asset]) ? '' : $results[$this->asset];
  }

  /**
   * Return the number of the results we found.
   *
   * When we need to count the results number the developer clone the query and
   * then execute the query with the count function. This method fire up the
   * process and assert this to the passed variable.
   *
   * @param $data
   *  A variable passed by reference in order to get the number of results.
   */
  public function count(&$data) {
    $query = clone $this->query;
    $data = $query
      ->count()
      ->execute();
  }
}
