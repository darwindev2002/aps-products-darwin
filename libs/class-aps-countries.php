<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
 * @class APS_Countries
*/

class APS_Countries {
	
	private
	$states_list,
	$countries_list;
	
	public function __construct() {
		// include states and countries arrays
		include(APS_DIR .'/inc/data/states.php');
		include(APS_DIR .'/inc/data/countries.php');
		
		// use filters to expand the arrays
		$this->states_list = apply_filters('aps_states_list', $states);
		$this->countries_list = apply_filters('aps_countries_list', $countries);
	}
	
	// get countries
	public function get_countries() {
		// return an array of world countries
		asort($this->countries_list);
		return $this->countries_list;
	}
	
	// get states
	public function get_states() {
		// return an array of states
		asort($this->states_list);
		return $this->states_list;
	}
	
	// get states of country
	public function get_country_states($country_code) {
		if ($country_code) {
			$states = $this->states_list;
			if (isset($states[$country_code])) {
				// return an array of states
				asort($states[$country_code]);
				return $states[$country_code];
			}
		}
		return false;
	}
	
	// get country by state name
	public function get_state_country($state_name) {
		if ($state_name) {
			$states = $this->states_list;
			foreach ($states as $country => $country_state) {
				foreach ($country_state as $state) {
					if ($state_name == $state) {
						$countries = $this->countries_list;
						$country_array = array($country => $countries[$country]);
						return $country_array;
					}
				}
			}
		}
		return false;
	}
}