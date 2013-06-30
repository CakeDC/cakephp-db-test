<?php
/*
 * DbTestCase.php
 */

App::uses('CakeTestCase', 'TestSuite');
App::uses('DbTestListener', 'Lib/TestSuite/Listener');
/**
 * Base core test case, handles model, controller, etc.
 * Handles building test case and running each method
 */
class DbTestCase extends CakeTestCase {

/** @var array */
	protected $defaultFixtures = array();

/**
 * Body of response
 * @var String
 */
	protected $contents;

/**
 * Constructs a test case with the given name.
 *
 * @param null|string $name
 * @param array       $data
 * @param string      $dataName
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		ClassRegistry::config(array('ds' => 'test', 'testing' => true));
		parent::__construct($name, $data, $dataName);
	}

/**
 * Runs the test case and collects the results in a TestResult object.
 * This method is run for each test method in this class
 *
 * @param PHPUnit_Framework_TestResult $result
 *
 * @return PHPUnit_Framework_TestResult
 */
	public function run(PHPUnit_Framework_TestResult $result = null) {
		$this->fixtureManager = null; // must remain null, otherwise cake's fixture manager attempts to engage
		return parent::run($result);
	}

	public function setUp() {
		ConnectionManager::getDataSource('test')->ignoreQueryCaching = true;
	}


/**
 * @deprecated phpUnit's setUp method should be used
 * @see setUp
 *
 * called when a test method starts
 */
	public function startTest() {
		ConnectionManager::getDataSource('test')->ignoreQueryCaching = true;
	}

/**
 * setup and mock a model
 *
 * @param array $models
 * @return MissingModelException
 */
	public function mockModels(array $models) {
		$config = ClassRegistry::config('Model');
		foreach ($models as $model => $methods) {
			if (is_string($methods)) {
				$model = $methods;
				$methods = true;
			}
			if ($methods === true) {
				$methods = array();
			}

			list($plugin, $name) = pluginSplit($model, true);
			App::uses($name, $plugin . 'Model');
			if (!class_exists($name)) {
				return new MissingModelException($name);
			}

			$config = array_merge((array)$config, array('name' => $model));
			$_model = $this->getMock($name, $methods, array($config));
			ClassRegistry::removeObject($name);
			ClassRegistry::addObject($name, $_model);
		}
	}

/**
 * setup and mock a component
 *
 * @param array $components
 * @param       $controller
 * @throws MissingComponentException
 */
	public function mockComponents(array $components, $controller) {
		foreach ($components as $component => $methods) {
			if (is_string($methods)) {
				$component = $methods;
				$methods = true;
			}
			if ($methods === true) {
				$methods = array();
			}
			list($plugin, $name) = pluginSplit($component, true);
			$componentClass = $name . 'Component';
			App::uses($componentClass, $plugin . 'Controller/Component');
			if (!class_exists($componentClass)) {
				throw new MissingComponentException(array(
					'class' => $componentClass
				));
			}
			$_component = $this->getMock($componentClass, $methods, array(), '', false);
			$controller->Components->set($name, $_component);
		}
	}

}
