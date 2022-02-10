<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       lukasdumberger.de
 * @since      1.0.0
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ct_Anmeldungen
 * @subpackage Ct_Anmeldungen/admin
 * @author     Lukas Dumberger <lukas.dumberger@gmail.com>
 */
class Ct_Anmeldungen_Admin
{

    public static $TEMPLATE_DIR;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        self::$TEMPLATE_DIR = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates/';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ct-anmeldungen-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ct-anmeldungen-admin.js', array('jquery'), $this->version, false);

    }


    function options_page()
    {
        add_menu_page(
            'ChurchTools Anmeldungen',
            'ChurchTools Anmeldungen',
            'manage_options',
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings',
            array($this, 'options_page_html')
        );
    }

    public function options_page_html()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields(CT_Anmeldungen::$PLUGIN_SLUG . '_settings');
                settings_errors();
                do_settings_sections(CT_Anmeldungen::$PLUGIN_SLUG . '_settings');
                submit_button(__('Save Settings', 'textdomain'));
                ?>
            </form>
            <p>ShortCode:<pre>[<?php echo(Ct_Anmeldungen_Public::$SHORTCODE); ?>]</pre></p>
        </div>
        <?php
    }

    public function settings_init()
    {
        register_setting(CT_Anmeldungen::$PLUGIN_SLUG . '_settings', CT_Anmeldungen::$PLUGIN_SLUG . '_settings_url', 'sanitize_url');
        register_setting(CT_Anmeldungen::$PLUGIN_SLUG . '_settings', CT_Anmeldungen::$PLUGIN_SLUG . '_settings_parent_template', array($this, 'sanitize_parent_template'));
        register_setting(CT_Anmeldungen::$PLUGIN_SLUG . '_settings', CT_Anmeldungen::$PLUGIN_SLUG . '_settings_child_template', array($this, 'sanitize_child_template'));

        add_settings_section(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section',
            "API-Settings",
            array($this, 'settings_section_callback'),
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_url',
            'API_Url',
            array($this, 'settings_field_url_callback'),
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings',
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_parent_template',
            'Parent-Template',
            array($this, 'settings_field_parent_template_callback'),
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings',
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_child_template',
            'Child-Template',
            array($this, 'settings_field_child_template_callback'),
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings',
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );
    }

    public function clone_templates_to_disk()
    {
        $parentTemplate = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_parent_template');
        file_put_contents(self::$TEMPLATE_DIR.'parent-template.html.twig', $parentTemplate);

        $childTemplate = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_child_template');
        file_put_contents(self::$TEMPLATE_DIR.'child-template.html.twig', $childTemplate);
    }

    public function settings_section_callback()
    {
        echo '<p>Anmeldung konfigurieren.</p>';
    }

    public function settings_field_url_callback()
    {
        $url = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_url');
        echo '<input type="text" name="' . CT_Anmeldungen::$PLUGIN_SLUG . '_settings_url' . '" value="' . (isset($url) ? esc_attr($url) : '') . '">';
    }

    public function settings_field_parent_template_callback()
    {
        $template = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_parent_template');
        echo '<textarea name="' . CT_Anmeldungen::$PLUGIN_SLUG . '_settings_parent_template' . '">' . (isset($template) ? esc_attr($template) : '') . '</textarea>';
    }

    public function settings_field_child_template_callback()
    {
        $template = get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_child_template');
        echo '<textarea name="' . CT_Anmeldungen::$PLUGIN_SLUG . '_settings_child_template' . '">' . (isset($template) ? esc_attr($template) : '') . '</textarea>';
    }

    public function sanitize_parent_template($templateValue)
    {

        if (strpos($templateValue, "{{ children }}") == false) {
            add_settings_error(
                CT_Anmeldungen::$PLUGIN_SLUG . '_settings_parent_template',
                CT_Anmeldungen::$PLUGIN_SLUG . '_error_parent_template',
                'Parent-Template muss {{ children }} - Element enthalten.',
                'error'
            );
            return get_option(CT_Anmeldungen::$PLUGIN_SLUG . '_settings_parent_template');
        } else {
            return $templateValue;
        }
    }
}
