<?php
/**
 * @file
 * Oversee progress flows and guide user from begin to finish
 */
class FlowModel {

	/**
	 * Define rules that trigger a flow
	 *
	 * @param string $referrer
	 */
	public trigger ($referrer) {
		$state = preg_replace('/^http[s]?:\/\/[a-z]*\.?[coursecoffee]\.[a-z]{3}\//i', '', $referrer);

		switch ($state) {
			case 'welcome':
				return 'sign-up';
			case 'class':
			case 'calendar':
			case 'home':
			default:
				return $state;
		}
	}
}
