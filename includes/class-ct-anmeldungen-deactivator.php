<?php

/**
 * Fired during plugin deactivation
 *
 * @link       lukasdumberger.de
 * @since      1.0.0
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/includes
 * @author     Lukas Dumberger <lukas.dumberger@gmail.com>
 */
class Ct_Anmeldungen_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        self::deleteOptions([
            Ct_Anmeldungen_Admin::$OPTION_URL,
            Ct_Anmeldungen_Admin::$OPTION_GROUP_HASH,
            Ct_Anmeldungen_Admin::$OPTION_CHILD_TEMPLATE,
            Ct_Anmeldungen_Admin::$OPTION_PARENT_TEMPLATE
        ]);
	}

	private static function deleteOptions(array $options){
        foreach($options as $option){
            $optionHasBeenDeleted = delete_option($option);
            Ct_Anmeldungen::$LOG->info("Option ". $option . " has" . ($optionHasBeenDeleted ? ' ' : ' not ') . "been deleted.");
        }
    }

}
