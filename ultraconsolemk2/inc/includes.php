<?php
/**
 * Includes and Initialization for N°VACORE Terminal Theme
 * 
 * This file includes all theme modules and handles initialization.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get theme directory path
 */
function nvacore_get_inc_dir() {
    return get_template_directory() . '/inc/';
}

/**
 * Include theme modules
 */
function nvacore_include_modules() {
    $modules = array(
        'admin-functions.php',
        'admin-interface.php',
        'ajax-handlers.php',
        'customizer.php',
        'security.php',
    );
    
    foreach ($modules as $module) {
        $file_path = nvacore_get_inc_dir() . $module;
        
        if (file_exists($file_path)) {
            require_once $file_path;
        } else {
            error_log("N°VACORE Theme: Missing module file - {$module}");
        }
    }
}

/**
 * Initialize theme modules
 */
function nvacore_init_modules() {
    // Hook into WordPress initialization
    add_action('init', 'nvacore_register_post_types', 0);
    add_action('after_setup_theme', 'nvacore_theme_setup');
    add_action('wp_enqueue_scripts', 'nvacore_enqueue_assets');
    
    // Admin hooks
    if (is_admin()) {
        add_action('add_meta_boxes', 'nvacore_add_meta_boxes');
        add_action('save_post', 'nvacore_save_meta_boxes');
    }
}

/**
 * Theme utilities and helper functions
 */

/**
 * Get dynamic content for JavaScript
 */
function nvacore_get_dynamic_content() {
    return array(
        'artwork_collection' => nvacore_get_artwork_collection(),
        'music_playlist' => nvacore_get_music_playlist(),
        'log_entries' => nvacore_get_log_entries(),
        'customizer_settings' => nvacore_get_customizer_settings(),
    );
}

/**
 * Get customizer settings for JavaScript
 */
function nvacore_get_customizer_settings() {
    return array(
        'video_backgrounds_enabled' => get_theme_mod('nvacore_enable_video_bg', true),
        'jukebox_enabled' => get_theme_mod('nvacore_enable_jukebox', true),
        'jukebox_autoplay' => get_theme_mod('nvacore_jukebox_autoplay', false),
        'default_volume' => get_theme_mod('nvacore_default_volume', 50),
        'debug_mode' => get_theme_mod('nvacore_enable_debug', false),
        'reduced_animations' => get_theme_mod('nvacore_reduce_animations', false),
        'custom_cursor_disabled' => get_theme_mod('nvacore_disable_cursor_effects', false),
    );
}

/**
 * Enhanced error handling
 */
function nvacore_error_handler($errno, $errstr, $errfile, $errline) {
    if (get_theme_mod('nvacore_enable_debug', false)) {
        $error_message = "Error [$errno]: $errstr in $errfile on line $errline";
        error_log("N°VACORE Theme: " . $error_message);
    }
    
    // Don't execute PHP internal error handler
    return true;
}

/**
 * Custom exception handler
 */
function nvacore_exception_handler($exception) {
    if (get_theme_mod('nvacore_enable_debug', false)) {
        error_log("N°VACORE Theme Exception: " . $exception->getMessage());
    }
}

/**
 * Set custom error handlers
 */
function nvacore_set_error_handlers() {
    if (get_theme_mod('nvacore_enable_debug', false)) {
        set_error_handler('nvacore_error_handler');
        set_exception_handler('nvacore_exception_handler');
    }
}

/**
 * Theme compatibility checks
 */
function nvacore_compatibility_check() {
    $requirements = array(
        'php_version' => '7.4',
        'wp_version' => '5.0',
        'required_functions' => array(
            'wp_enqueue_script',
            'wp_localize_script',
            'register_post_type',
            'add_theme_support',
        ),
        'required_extensions' => array(
            'json',
            'curl',
        ),
    );
    
    $errors = array();
    
    // Check PHP version
    if (version_compare(PHP_VERSION, $requirements['php_version'], '<')) {
        $errors[] = sprintf(
            'PHP version %s or higher is required. You are running %s.',
            $requirements['php_version'],
            PHP_VERSION
        );
    }
    
    // Check WordPress version
    global $wp_version;
    if (version_compare($wp_version, $requirements['wp_version'], '<')) {
        $errors[] = sprintf(
            'WordPress version %s or higher is required. You are running %s.',
            $requirements['wp_version'],
            $wp_version
        );
    }
    
    // Check required functions
    foreach ($requirements['required_functions'] as $function) {
        if (!function_exists($function)) {
            $errors[] = "Required function '{$function}' is not available.";
        }
    }
    
    // Check PHP extensions
    foreach ($requirements['required_extensions'] as $extension) {
        if (!extension_loaded($extension)) {
            $errors[] = "Required PHP extension '{$extension}' is not loaded.";
        }
    }
    
    if (!empty($errors)) {
        add_action('admin_notices', function() use ($errors) {
            echo '<div class="notice notice-error"><p><strong>N°VACORE Terminal Theme:</strong></p><ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        });
        
        return false;
    }
    
    return true;
}

/**
 * Theme activation hook
 */
function nvacore_theme_activation() {
    // Check compatibility
    if (!nvacore_compatibility_check()) {
        return;
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Create default pages if they don't exist
    nvacore_create_default_pages();
    
    // Set default theme options
    nvacore_set_default_options();
    
    // Log activation
    error_log('N°VACORE Terminal Theme activated successfully');
}

/**
 * Set default theme options
 */
function nvacore_set_default_options() {
    $defaults = array(
        'nvacore_accent_color' => '#c4a0ff',
        'nvacore_terminal_bg_color' => '#1a1a1a',
        'nvacore_text_color' => '#e0e0e0',
        'nvacore_enable_video_bg' => true,
        'nvacore_enable_jukebox' => true,
        'nvacore_jukebox_autoplay' => false,
        'nvacore_default_volume' => 50,
        'nvacore_commissions_status' => 'open',
        'nvacore_instagram_main' => 'https://www.instagram.com/n.vacore/',
        'nvacore_instagram_alt' => 'https://www.instagram.com/ultrac_re/',
        'nvacore_discord_username' => 'n.vacore',
        'nvacore_discord_server' => 'https://discord.gg/UuhZFmwdWC',
        'nvacore_email' => 'cadenbunce@gmail.com',
    );
    
    foreach ($defaults as $option => $value) {
        if (get_theme_mod($option) === null) {
            set_theme_mod($option, $value);
        }
    }
}

/**
 * Theme deactivation hook
 */
function nvacore_theme_deactivation() {
    // Clean up if needed
    flush_rewrite_rules();
    
    // Log deactivation
    error_log('N°VACORE Terminal Theme deactivated');
}

/**
 * Debug information
 */
function nvacore_get_debug_info() {
    if (!get_theme_mod('nvacore_enable_debug', false)) {
        return array();
    }
    
    return array(
        'theme_version' => wp_get_theme()->get('Version'),
        'wp_version' => get_bloginfo('version'),
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'is_mobile' => wp_is_mobile(),
        'is_admin' => is_admin(),
        'current_user_id' => get_current_user_id(),
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true),
        'loaded_modules' => array(
            'admin_functions' => function_exists('nvacore_admin_reply_guestbook'),
            'ajax_handlers' => function_exists('nvacore_submit_guestbook_entry'),
            'customizer' => function_exists('nvacore_customize_register'),
            'security' => function_exists('nvacore_security_headers'),
        ),
    );
}

/**
 * Add debug information to footer
 */
function nvacore_add_debug_info() {
    if (get_theme_mod('nvacore_enable_debug', false) && current_user_can('manage_options')) {
        $debug_info = nvacore_get_debug_info();
        echo '<script>console.log("N°VACORE Debug Info:", ' . json_encode($debug_info) . ');</script>';
    }
}
add_action('wp_footer', 'nvacore_add_debug_info');

/**
 * AJAX action to get debug information
 */
function nvacore_get_debug_info_ajax() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }
    
    wp_send_json_success(nvacore_get_debug_info());
}
add_action('wp_ajax_nvacore_debug_info', 'nvacore_get_debug_info_ajax');

/**
 * Theme update notifications
 */
function nvacore_check_for_updates() {
    // This would integrate with a custom update server
    // For now, just provide a way to check the current version
    return array(
        'current_version' => wp_get_theme()->get('Version'),
        'latest_version' => wp_get_theme()->get('Version'), // Would fetch from server
        'update_available' => false,
    );
}

/**
 * Performance monitoring
 */
function nvacore_performance_monitor() {
    if (get_theme_mod('nvacore_enable_debug', false)) {
        $start_time = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        
        add_action('wp_footer', function() use ($start_time) {
            $end_time = microtime(true);
            $execution_time = round(($end_time - $start_time) * 1000, 2);
            
            echo '<script>console.log("Page load time: ' . $execution_time . 'ms");</script>';
        });
    }
}
add_action('init', 'nvacore_performance_monitor');

/**
 * Cache management
 */
function nvacore_clear_theme_cache() {
    // Clear any theme-specific caches
    $cache_keys = array(
        'nvacore_artwork_collection',
        'nvacore_music_playlist',
        'nvacore_log_entries',
    );
    
    foreach ($cache_keys as $key) {
        delete_transient($key);
    }
    
    // Clear WordPress object cache if available
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}

/**
 * AJAX handler for clearing cache
 */
function nvacore_clear_cache_ajax() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }
    
    nvacore_clear_theme_cache();
    wp_send_json_success('Cache cleared successfully');
}
add_action('wp_ajax_nvacore_clear_cache', 'nvacore_clear_cache_ajax');

/**
 * Initialize everything
 */
function nvacore_initialize() {
    // Check compatibility first
    if (!nvacore_compatibility_check()) {
        return;
    }
    
    // Set error handlers
    nvacore_set_error_handlers();
    
    // Include all modules
    nvacore_include_modules();
    
    // Initialize modules
    nvacore_init_modules();
    
    // Theme is ready
    do_action('nvacore_theme_ready');
}

// Run initialization
nvacore_initialize();

/**
 * Hook for theme activation/deactivation
 */
add_action('after_switch_theme', 'nvacore_theme_activation');
add_action('switch_theme', 'nvacore_theme_deactivation');