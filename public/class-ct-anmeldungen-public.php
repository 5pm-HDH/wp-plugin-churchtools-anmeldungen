<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       lukasdumberger.de
 * @since      1.0.0
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/public
 */

use CTApi\CTConfig;
use CTApi\Models\PublicGroup;
use CTApi\Requests\PublicGroupRequest;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/public
 * @author     Lukas Dumberger <lukas.dumberger@gmail.com>
 */
class Ct_Anmeldungen_Public {

    public static $SHORTCODE = "ct-anmeldungen";

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ct_Anmeldungen_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ct_Anmeldungen_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ct-anmeldungen-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ct_Anmeldungen_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ct_Anmeldungen_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ct-anmeldungen-public.js', array( 'jquery' ), $this->version, false );

	}

	public function init_shortcode()
    {
        add_shortcode( Ct_Anmeldungen_Public::$SHORTCODE, array($this, 'parse_shortcode'));
    }

	public function parse_shortcode()
    {
        $twigLoader = new FilesystemLoader(Ct_Anmeldungen_Admin::$TEMPLATE_DIR);
        $twig = new Environment($twigLoader);

        // Check if Child- & Parent-Template exists.
        if(!$twig->getLoader()->exists(Ct_Anmeldungen_Admin::$CHILD_TEMPLATE_NAME)
         || !$twig->getLoader()->exists(Ct_Anmeldungen_Admin::$PARENT_TEMPLATE_NAME)){
            Ct_Anmeldungen_Admin::clone_templates_to_disk();

            // Reload Template-Directory
            $twigLoader = new FilesystemLoader(Ct_Anmeldungen_Admin::$TEMPLATE_DIR);
            $twig = new Environment($twigLoader);
        }

        try {
            $ctData = $this->provide_churchtools_data();

            $childHtml = "";
            foreach($ctData as $groupData){
                $childHtml .= $twig->render(Ct_Anmeldungen_Admin::$CHILD_TEMPLATE_NAME, $groupData);
            }

            return $twig->render(Ct_Anmeldungen_Admin::$PARENT_TEMPLATE_NAME, ['children' => $childHtml]);
        } catch (LoaderError| RuntimeError| SyntaxError  $e) {
            return "<h1>ERROR IN TWIG:</h1><p>".$e->getMessage()."</p>";
        }
    }

    private function provide_churchtools_data()
    {
        $ctUrl = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_url');
        $groupHash = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_group_hash');

        if(is_null($ctUrl) || $ctUrl == "" || is_null($groupHash) || $groupHash == ""){
            return [];
        }

        CTConfig::setApiUrl($ctUrl);
        $publicGroup = PublicGroupRequest::get($groupHash);
        return array_map(function($groupObject){
            return $this->parse_group_to_array($groupObject);
        }, $publicGroup->getGroups());
    }

    private function parse_group_to_array(PublicGroup $group)
    {
        return [
            'currentMemberCount' => $group->getCurrentMemberCount(),
            'signUpHeadline' => $group->getSignUpHeadline(),
            'id' => $group->getId(),
            'guid' => $group->getGuid(),
            'name' => $group->getName(),
            'meetingTime' => $group->getInformation()?->getMeetingTime(),
            'note' => $group->getInformation()?->getNote(),
            'imageUrl' => $group->getInformation()?->getImageUrl(),
        ];
    }

}
