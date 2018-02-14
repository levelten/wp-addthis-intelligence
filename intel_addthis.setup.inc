<?php

/**
 * Included to assist in initial setup of plugin
 *
 * @since      1.0.0
 *
 * @package    Intel
 */

if (!is_callable('intel_setup')) {
	include_once intel_addthis()->dir . 'intel_com/intel.setup.inc';
}

class Intel_Addthis_Setup extends Intel_Setup {

	public $plugin_un = 'intel_addthis';

	/*
	 * Include any methods from Intel_Setup you want to override
	 */

}

function intel_addthis_setup() {
	return Intel_Addthis_Setup::instance();
}
intel_addthis_setup();
