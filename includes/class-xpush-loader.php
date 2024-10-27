<?php

class xPush_Loader_Class {

	protected $actions;

	protected $filters;

	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	public function xpush_add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->xpush_add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	public function xpush_add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->xpush_add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	private function xpush_add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	public function xpush_run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

}
