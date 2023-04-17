<?php

/**
 * Fired during plugin activation
 *
 * @link       lukasdumberger.de
 * @since      1.0.0
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/includes
 * @author     Lukas Dumberger <lukas.dumberger@gmail.com>
 */
class Ct_Anmeldungen_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        Ct_Anmeldungen_Admin::clone_templates_to_disk();
	}

}
