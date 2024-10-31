<?php

/**
 * Register all actions for the plugin
 */
class Pushpro_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @var      array $actions The actions registered with WordPress to fire
	 *     when the plugin loads.
	 */
	protected $actions;

	/**
	 * Initialize the collections used to maintain the actions.
	 */
	public function __construct() {
		$this->actions = [];
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param $hook
	 * @param $component
	 * @param $callback
	 * @param  int  $priority
	 * @param  int  $accepted_args
	 */
	public function add_action(
		$hook,
		$component,
		$callback,
		$priority = 10,
		$accepted_args = 1
	) {
		$this->actions = $this->add( $this->actions, $hook, $component,
			$callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a
	 * single collection.
	 *
	 * @param $hooks
	 * @param $hook
	 * @param $component
	 * @param $callback
	 * @param $priority
	 * @param $accepted_args
	 *
	 * @return array
	 */
	private function add(
		$hooks,
		$hook,
		$component,
		$callback,
		$priority,
		$accepted_args
	) {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 */
	public function run() {
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'],
				[ $hook['component'], $hook['callback'] ], $hook['priority'],
				$hook['accepted_args'] );
		}
	}
}