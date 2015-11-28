<?php


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
