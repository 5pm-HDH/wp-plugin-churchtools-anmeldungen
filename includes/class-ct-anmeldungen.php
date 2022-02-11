<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       lukasdumberger.de
 * @since      1.0.0
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/includes
 */

use Monolog\Logger;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/includes
 * @author     Lukas Dumberger <lukas.dumberger@gmail.com>
 */
class Ct_Anmeldungen {

    public static $LOG;
    private static $warningLogFile = "";

    public static $PLUGIN_SLUG = "ct_anmeldungen";
    public static $PLUGIN_NAME = "ct-anmeldungen";


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ct_Anmeldungen_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

    /**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CT_ANMELDUNGEN_VERSION' ) ) {
			$this->version = CT_ANMELDUNGEN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ct-anmeldungen';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		self::$LOG = new Logger('PLUGIN_LOG');
        self::$LOG->pushHandler(new \Monolog\Handler\StreamHandler(
            plugin_dir_path( dirname( __FILE__ ) ) . 'logs/debug.log',
            Logger::DEBUG
        ));

        self::$warningLogFile = plugin_dir_path( dirname( __FILE__ ) ) . 'logs/warning.log';
        self::$LOG->pushHandler(new \Monolog\Handler\StreamHandler(
            self::$warningLogFile,
            Logger::WARNING
        ));
    }

    public static function getTailOfWarningLog(int $numberOfLines): string
    {
        if(!file_exists(self::$warningLogFile)){
            return "";
        }

        $lines = array();
        $fp = fopen(self::$warningLogFile, "r");
        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            array_push($lines, $line);
            if (count($lines) > ($numberOfLines+1))
                array_shift($lines);
        }
        fclose($fp);
        return implode("", $lines);
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ct_Anmeldungen_Loader. Orchestrates the hooks of the plugin.
	 * - Ct_Anmeldungen_i18n. Defines internationalization functionality.
	 * - Ct_Anmeldungen_Admin. Defines all hooks for the admin area.
	 * - Ct_Anmeldungen_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ct-anmeldungen-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ct-anmeldungen-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ct-anmeldungen-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ct-anmeldungen-public.php';

        /**
         * Load Composer Dependencies
         */
        require_once plugin_dir_path( dirname( __FILE__ )) .'vendor/autoload.php';

		$this->loader = new Ct_Anmeldungen_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ct_Anmeldungen_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ct_Anmeldungen_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ct_Anmeldungen_Admin( self::$PLUGIN_NAME, $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        $this->loader->add_action('admin_init', $plugin_admin, 'settings_init');
        $this->loader->add_action('admin_menu', $plugin_admin, 'options_page');

        $this->loader->add_action('update_option_'.Ct_Anmeldungen_Admin::$OPTION_CHILD_TEMPLATE, Ct_Anmeldungen_Admin::class, 'clone_templates_to_disk');
        $this->loader->add_action('update_option_'.Ct_Anmeldungen_Admin::$OPTION_PARENT_TEMPLATE, Ct_Anmeldungen_Admin::class, 'clone_templates_to_disk');
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ct_Anmeldungen_Public( self::$PLUGIN_NAME, $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        $this->loader->add_action( 'init', $plugin_public, 'init_shortcode' );
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ct_Anmeldungen_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
