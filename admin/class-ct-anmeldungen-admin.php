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

    public static $PARENT_TEMPLATE_NAME = "parent-template.html.twig";
    public static $CHILD_TEMPLATE_NAME = "child-template.html.twig";

    public static $OPTION_URL = "ct_anmeldungen_settings_url";
    public static $OPTION_GROUP_HASH = "ct_anmeldungen_settings_group_hash";
    public static $OPTION_PARENT_TEMPLATE = "ct_anmeldungen_settings_parent_template";
    public static $OPTION_CHILD_TEMPLATE = "ct_anmeldungen_settings_child_template";

    private static $SETTINGS = "ct_anmeldungen_settings";

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

    public static function clone_templates_to_disk()
    {
        Ct_Anmeldungen::$LOG->debug("Clone Parent- & Child-Templates to Disk. Directory:", [self::$TEMPLATE_DIR]);

        $parentTemplate = get_option(self::$OPTION_PARENT_TEMPLATE);
        file_put_contents(self::$TEMPLATE_DIR.self::$PARENT_TEMPLATE_NAME, $parentTemplate);

        $childTemplate = get_option(self::$OPTION_CHILD_TEMPLATE);
        file_put_contents(self::$TEMPLATE_DIR.self::$CHILD_TEMPLATE_NAME, $childTemplate);
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
            self::$SETTINGS,
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
                settings_fields(self::$SETTINGS);
                settings_errors();
                do_settings_sections(self::$SETTINGS);
                submit_button(__('Save Settings', 'textdomain'));
                ?>
            </form>
        </div>
        <?php
    }

    public function settings_init()
    {
        register_setting(self::$SETTINGS, self::$OPTION_URL, 'sanitize_url');
        register_setting(self::$SETTINGS, self::$OPTION_GROUP_HASH);
        register_setting(self::$SETTINGS, self::$OPTION_PARENT_TEMPLATE, array($this, 'sanitize_parent_template'));
        register_setting(self::$SETTINGS, self::$OPTION_CHILD_TEMPLATE, array($this, 'sanitize_child_template'));

        add_settings_section(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section',
            "API-Settings",
            array($this, 'settings_section_callback'),
            self::$SETTINGS
        );

        add_settings_field(
            Ct_Anmeldungen::$PLUGIN_SLUG . '_settings_field_shortcode',
            'ShortCode',
            array($this, 'settings_field_shortcode'),
            self::$SETTINGS,
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_url',
            'API_Url',
            array($this, 'settings_field_url_callback'),
            self::$SETTINGS,
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_group_hash',
            'Group Hash',
            array($this, 'settings_field_group_hash_callback'),
            self::$SETTINGS,
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_parent_template',
            'Parent-Template',
            array($this, 'settings_field_parent_template_callback'),
            self::$SETTINGS,
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_field(
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_field_child_template',
            'Child-Template',
            array($this, 'settings_field_child_template_callback'),
            self::$SETTINGS,
            CT_Anmeldungen::$PLUGIN_SLUG . '_settings_section'
        );

        add_settings_section(
            CT_Anmeldungen::$PLUGIN_SLUG . '_log_section',
            "Log-Datei",
            array($this, 'log_section_callback'),
            self::$SETTINGS
        );

        add_settings_field(
            Ct_Anmeldungen::$PLUGIN_SLUG . '_settings_field_log',
            'Warning- / Error-Log',
            array($this, 'log_field_log'),
            self::$SETTINGS,
            CT_Anmeldungen::$PLUGIN_SLUG . '_log_section'
        );
    }

    public function settings_section_callback()
    {
        echo '<p>Anmeldung konfigurieren.</p>';
    }

    public function settings_field_shortcode()
    {
        echo '<b><pre>['.Ct_Anmeldungen_Public::$SHORTCODE.']</pre></b>';
    }

    public function settings_field_url_callback()
    {
        $url = get_option(self::$OPTION_URL);
        echo '<input type="text" name="' . self::$OPTION_URL . '" value="' . (isset($url) ? esc_attr($url) : '') . '">';
    }

    public function settings_field_group_hash_callback()
    {
        $groupHash = get_option(self::$OPTION_GROUP_HASH);
        echo '<input type="text" name="' . self::$OPTION_GROUP_HASH . '" value="' . (isset($groupHash) ? esc_attr($groupHash) : '') . '">';
    }


    public function settings_field_parent_template_callback()
    {
        $template = get_option(self::$OPTION_PARENT_TEMPLATE);
        wp_editor($template, CT_Anmeldungen::$PLUGIN_SLUG.'_parent_template_editor', array(
           'textarea_name' => self::$OPTION_PARENT_TEMPLATE,
            'media_buttons' => false,
        ));
    }

    public function settings_field_child_template_callback()
    {
        $template = get_option(self::$OPTION_CHILD_TEMPLATE);
        wp_editor($template, CT_Anmeldungen::$PLUGIN_SLUG.'_child_template_editor', array(
            'textarea_name' => self::$OPTION_CHILD_TEMPLATE,
            'media_buttons' => false,
        ));
    }

    public function sanitize_parent_template($templateValue)
    {

        if (strpos($templateValue, "{{ children|raw }}") == false) {
            add_settings_error(
                self::$OPTION_PARENT_TEMPLATE,
                CT_Anmeldungen::$PLUGIN_SLUG . '_error_parent_template',
                'Parent-Template muss {{ children|raw }} - Element enthalten.',
                'error'
            );
            return get_option(self::$OPTION_PARENT_TEMPLATE);
        } else {
            return $templateValue;
        }
    }

    public function log_section_callback()
    {
        echo '<p>Das Log enthält wichtige Aufzeichnungen über mögliche Fehler während der Ausführung des Plugins.</p>';
    }

    public function log_field_log()
    {
        echo '<pre style="overflow-x: scroll; padding: 1rem; max-width: 60rem;">'.Ct_Anmeldungen::getTailOfWarningLog(5).'</pre>';
    }
}
