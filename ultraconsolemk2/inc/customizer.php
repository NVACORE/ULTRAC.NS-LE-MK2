<?php
/**
 * Theme Customizer for N°VACORE Terminal
 * 
 * Adds customizer options for theme colors, video backgrounds,
 * and other theme-specific settings.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add customizer settings
 */
function nvacore_customize_register($wp_customize) {
    
    // Terminal Theme Section
    $wp_customize->add_section('nvacore_terminal_theme', array(
        'title' => 'Terminal Theme Settings',
        'priority' => 30,
        'description' => 'Customize your N°VACORE terminal experience',
    ));
    
    // Boot Video Settings
    $wp_customize->add_section('nvacore_video_backgrounds', array(
        'title' => 'Video Backgrounds',
        'priority' => 31,
        'description' => 'Configure video backgrounds for different themes and devices',
    ));
    
    // Jukebox Settings
    $wp_customize->add_section('nvacore_jukebox', array(
        'title' => 'Jukebox Settings',
        'priority' => 32,
        'description' => 'Configure the music player settings',
    ));
    
    // Contact Information
    $wp_customize->add_section('nvacore_contact_info', array(
        'title' => 'Contact Information',
        'priority' => 33,
        'description' => 'Update your contact details',
    ));
    
    // Terminal Colors
    $wp_customize->add_setting('nvacore_accent_color', array(
        'default' => '#c4a0ff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nvacore_accent_color', array(
        'label' => 'Accent Color',
        'section' => 'nvacore_terminal_theme',
        'settings' => 'nvacore_accent_color',
        'description' => 'Main accent color for the terminal interface',
    )));
    
    $wp_customize->add_setting('nvacore_terminal_bg_color', array(
        'default' => '#1a1a1a',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nvacore_terminal_bg_color', array(
        'label' => 'Terminal Background Color',
        'section' => 'nvacore_terminal_theme',
        'settings' => 'nvacore_terminal_bg_color',
    )));
    
    $wp_customize->add_setting('nvacore_text_color', array(
        'default' => '#e0e0e0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nvacore_text_color', array(
        'label' => 'Text Color',
        'section' => 'nvacore_terminal_theme',
        'settings' => 'nvacore_text_color',
    )));
    
    // Enable/Disable Features
    $wp_customize->add_setting('nvacore_enable_video_bg', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('nvacore_enable_video_bg', array(
        'label' => 'Enable Video Backgrounds',
        'section' => 'nvacore_video_backgrounds',
        'type' => 'checkbox',
        'description' => 'Enable animated video backgrounds (disable for better performance)',
    ));
    
    $wp_customize->add_setting('nvacore_enable_jukebox', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('nvacore_enable_jukebox', array(
        'label' => 'Enable Jukebox',
        'section' => 'nvacore_jukebox',
        'type' => 'checkbox',
        'description' => 'Enable the music player/jukebox feature',
    ));
    
    $wp_customize->add_setting('nvacore_jukebox_autoplay', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('nvacore_jukebox_autoplay', array(
        'label' => 'Jukebox Autoplay',
        'section' => 'nvacore_jukebox',
        'type' => 'checkbox',
        'description' => 'Start playing music automatically (may be blocked by browsers)',
    ));
    
    $wp_customize->add_setting('nvacore_default_volume', array(
        'default' => 50,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('nvacore_default_volume', array(
        'label' => 'Default Volume',
        'section' => 'nvacore_jukebox',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 0,
            'max' => 100,
            'step' => 5,
        ),
        'description' => 'Default volume level (0-100)',
    ));
    
    // Video URLs for different themes/devices
    $devices = array('mobile', 'desktop');
    $themes = array('dark', 'light');
    $video_types = array('boot', 'loop');
    
    foreach ($devices as $device) {
        foreach ($themes as $theme) {
            foreach ($video_types as $type) {
                $setting_id = "nvacore_video_{$device}_{$theme}_{$type}";
                
                $wp_customize->add_setting($setting_id, array(
                    'default' => '',
                    'sanitize_callback' => 'esc_url_raw',
                ));
                
                $wp_customize->add_control($setting_id, array(
                    'label' => ucfirst($device) . ' ' . ucfirst($theme) . ' ' . ucfirst($type) . ' Video',
                    'section' => 'nvacore_video_backgrounds',
                    'type' => 'url',
                    'description' => "URL for {$device} {$theme} theme {$type} video",
                ));
            }
        }
    }
    
    // Contact Information
    $wp_customize->add_setting('nvacore_instagram_main', array(
        'default' => 'https://www.instagram.com/n.vacore/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('nvacore_instagram_main', array(
        'label' => 'Main Instagram URL',
        'section' => 'nvacore_contact_info',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('nvacore_instagram_alt', array(
        'default' => 'https://www.instagram.com/ultrac_re/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('nvacore_instagram_alt', array(
        'label' => 'ULTRAC°RE Instagram URL',
        'section' => 'nvacore_contact_info',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('nvacore_discord_username', array(
        'default' => 'n.vacore',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('nvacore_discord_username', array(
        'label' => 'Discord Username',
        'section' => 'nvacore_contact_info',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('nvacore_discord_server', array(
        'default' => 'https://discord.gg/UuhZFmwdWC',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('nvacore_discord_server', array(
        'label' => 'Discord Server Invite',
        'section' => 'nvacore_contact_info',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('nvacore_email', array(
        'default' => 'cadenbunce@gmail.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('nvacore_email', array(
        'label' => 'Contact Email',
        'section' => 'nvacore_contact_info',
        'type' => 'email',
    ));
    
    // Commission Status
    $wp_customize->add_setting('nvacore_commissions_status', array(
        'default' => 'open',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('nvacore_commissions_status', array(
        'label' => 'Commission Status',
        'section' => 'nvacore_contact_info',
        'type' => 'select',
        'choices' => array(
            'open' => 'Open',
            'closed' => 'Closed',
            'waitlist' => 'Waitlist Only',
        ),
    ));
    
    // Boot Messages Customization
    $wp_customize->add_setting('nvacore_custom_boot_messages', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('nvacore_custom_boot_messages', array(
        'label' => 'Custom Boot Messages',
        'section' => 'nvacore_terminal_theme',
        'type' => 'textarea',
        'description' => 'One message per line. Leave empty to use defaults.',
    ));
    
    // Advanced Settings
    $wp_customize->add_section('nvacore_advanced', array(
        'title' => 'Advanced Settings',
        'priority' => 34,
        'description' => 'Advanced customization options',
    ));
    
    $wp_customize->add_setting('nvacore_disable_cursor_effects', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('nvacore_disable_cursor_effects', array(
        'label' => 'Disable Custom Cursor Effects',
        'section' => 'nvacore_advanced',
        'type' => 'checkbox',
        'description' => 'Disable custom cursor animations for better performance',
    ));
    
    $wp_customize->add_setting('nvacore_reduce_animations', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('nvacore_reduce_animations', array(
        'label' => 'Reduce Animations',
        'section' => 'nvacore_advanced',
        'type' => 'checkbox',
        'description' => 'Reduce animations for better performance and accessibility',
    ));
    
    $wp_customize->add_setting('nvacore_enable_debug', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('nvacore_enable_debug', array(
        'label' => 'Enable Debug Mode',
        'section' => 'nvacore_advanced',
        'type' => 'checkbox',
        'description' => 'Enable console logging for troubleshooting',
    ));
}
add_action('customize_register', 'nvacore_customize_register');

/**
 * Bind JS handlers to instantly live-preview changes
 */
function nvacore_customize_preview_js() {
    wp_enqueue_script(
        'nvacore-customize-preview',
        get_template_directory_uri() . '/assets/js/customize-preview.js',
        array('customize-preview'),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('customize_preview_init', 'nvacore_customize_preview_js');

/**
 * Output customizer CSS
 */
function nvacore_customizer_css() {
    $accent_color = get_theme_mod('nvacore_accent_color', '#c4a0ff');
    $terminal_bg = get_theme_mod('nvacore_terminal_bg_color', '#1a1a1a');
    $text_color = get_theme_mod('nvacore_text_color', '#e0e0e0');
    
    echo '<style type="text/css">';
    echo ':root {';
    echo '--accent-color: ' . esc_attr($accent_color) . ';';
    echo '--terminal-bg: ' . esc_attr($terminal_bg) . ';';
    echo '--text-color: ' . esc_attr($text_color) . ';';
    echo '}';
    
    // Disable animations if set
    if (get_theme_mod('nvacore_reduce_animations', false)) {
        echo '* { transition: none !important; animation: none !important; }';
    }
    
    // Hide cursor effects if disabled
    if (get_theme_mod('nvacore_disable_cursor_effects', false)) {
        echo '.custom-cursor, .click-animation, .cursor-ray { display: none !important; }';
    }
    
    echo '</style>';
}
add_action('wp_head', 'nvacore_customizer_css');

/**
 * Add customizer data to JavaScript
 */
function nvacore_customizer_js_data() {
    if (is_admin()) return;
    
    $video_config = array();
    $devices = array('mobile', 'desktop');
    $themes = array('dark', 'light');
    $video_types = array('boot', 'loop');
    
    foreach ($devices as $device) {
        foreach ($themes as $theme) {
            foreach ($video_types as $type) {
                $setting_id = "nvacore_video_{$device}_{$theme}_{$type}";
                $video_config[$device][$theme][$type] = get_theme_mod($setting_id, '');
            }
        }
    }
    
    $customizer_data = array(
        'video_backgrounds' => $video_config,
        'enable_video_bg' => get_theme_mod('nvacore_enable_video_bg', true),
        'enable_jukebox' => get_theme_mod('nvacore_enable_jukebox', true),
        'jukebox_autoplay' => get_theme_mod('nvacore_jukebox_autoplay', false),
        'default_volume' => get_theme_mod('nvacore_default_volume', 50),
        'enable_debug' => get_theme_mod('nvacore_enable_debug', false),
        'custom_boot_messages' => array_filter(explode("\n", get_theme_mod('nvacore_custom_boot_messages', ''))),
        'contact_info' => array(
            'instagram_main' => get_theme_mod('nvacore_instagram_main', 'https://www.instagram.com/n.vacore/'),
            'instagram_alt' => get_theme_mod('nvacore_instagram_alt', 'https://www.instagram.com/ultrac_re/'),
            'discord_username' => get_theme_mod('nvacore_discord_username', 'n.vacore'),
            'discord_server' => get_theme_mod('nvacore_discord_server', 'https://discord.gg/UuhZFmwdWC'),
            'email' => get_theme_mod('nvacore_email', 'cadenbunce@gmail.com'),
            'commissions_status' => get_theme_mod('nvacore_commissions_status', 'open'),
        ),
    );
    
    echo '<script type="text/javascript">';
    echo 'window.nvacore_customizer = ' . json_encode($customizer_data) . ';';
    echo '</script>';
}
add_action('wp_head', 'nvacore_customizer_js_data');

/**
 * Helper function to get commission status display
 */
function nvacore_get_commission_status_display() {
    $status = get_theme_mod('nvacore_commissions_status', 'open');
    
    $statuses = array(
        'open' => array(
            'text' => 'OPEN',
            'color' => '#88ff88',
        ),
        'closed' => array(
            'text' => 'CLOSED',
            'color' => '#ff8888',
        ),
        'waitlist' => array(
            'text' => 'WAITLIST ONLY',
            'color' => '#ffaa44',
        ),
    );
    
    return $statuses[$status] ?? $statuses['open'];
}