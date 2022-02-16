<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields(Ct_Anmeldungen_Admin::$SETTINGS);
        settings_errors();
        do_settings_sections(Ct_Anmeldungen_Admin::$SETTINGS);
        submit_button(__('Save Settings', 'textdomain'));
        ?>
    </form>
</div>