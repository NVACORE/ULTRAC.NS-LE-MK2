<?php
/**
 * Security Functions for N°VACORE Terminal Theme
 * 
 * Handles security measures, optimization, and performance enhancements.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Security Enhancements
 */

/**
 * Remove WordPress version from head and feeds
 */
function nvacore_remove_wp_version() {
    return '';
}
add_filter('the_generator', 'nvacore_remove_wp_version');

/**
 * Remove version numbers from scripts and styles
 */
function nvacore_remove_script_version($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'nvacore_remove_script_version', 15, 1);
add_filter('style_loader_src', 'nvacore_remove_script_version', 15, 1);

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove RSD link from head
 */
remove_action('wp_head', 'rsd_link');

/**
 * Remove Windows Live Writer manifest link
 */
remove_action('wp_head', 'wlwmanifest_link');

/**
 * Remove shortlink from head
 */
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Remove REST API links from head
 */
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');

/**
 * Disable file editing in admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Enhanced security headers
 */
function nvacore_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'nvacore_security_headers');

/**
 * Rate Limiting Functions
 */

/**
 * Implement basic rate limiting for AJAX requests
 */
function nvacore_rate_limit_check($action, $limit = 5, $window = 300) {
    if (current_user_can('manage_options')) {
        return true; // Skip rate limiting for admins
    }
    
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $cache_key = "nvacore_rate_limit_{$action}_{$user_ip}";
    
    $attempts = get_transient($cache_key);
    
    if ($attempts === false) {
        set_transient($cache_key, 1, $window);
        return true;
    } elseif ($attempts < $limit) {
        set_transient($cache_key, $attempts + 1, $window);
        return true;
    }
    
    return false;
}

/**
 * Apply rate limiting to sensitive AJAX actions
 */
function nvacore_apply_rate_limiting() {
    $action = $_POST['action'] ?? '';
    
    $rate_limited_actions = array(
        'submit_guestbook_entry' => array('limit' => 3, 'window' => 3600), // 3 per hour
        'fetch_music_metadata' => array('limit' => 10, 'window' => 300),   // 10 per 5 minutes
        'submit_contact_form' => array('limit' => 2, 'window' => 1800),    // 2 per 30 minutes
    );
    
    if (isset($rate_limited_actions[$action])) {
        $config = $rate_limited_actions[$action];
        if (!nvacore_rate_limit_check($action, $config['limit'], $config['window'])) {
            wp_send_json_error('Rate limit exceeded. Please try again later.');
        }
    }
}
add_action('wp_ajax_nopriv_submit_guestbook_entry', 'nvacore_apply_rate_limiting', 1);
add_action('wp_ajax_nopriv_fetch_music_metadata', 'nvacore_apply_rate_limiting', 1);
add_action('wp_ajax_nopriv_submit_contact_form', 'nvacore_apply_rate_limiting', 1);

/**
 * Performance Optimizations
 */

/**
 * Optimize WordPress for better performance
 */
function nvacore_performance_optimizations() {
    // Remove query strings from static resources
    if (!is_admin()) {
        add_filter('script_loader_src', 'nvacore_remove_query_strings', 15, 1);
        add_filter('style_loader_src', 'nvacore_remove_query_strings', 15, 1);
    }
    
    // Defer non-critical JavaScript
    add_filter('script_loader_tag', 'nvacore_defer_scripts', 10, 3);
    
    // Preload critical resources
    add_action('wp_head', 'nvacore_preload_resources', 1);
}
add_action('init', 'nvacore_performance_optimizations');

/**
 * Remove query strings from static resources
 */
function nvacore_remove_query_strings($src) {
    $parts = explode('?', $src);
    return $parts[0];
}

/**
 * Defer non-critical JavaScript files
 */
function nvacore_defer_scripts($tag, $handle, $src) {
    // List of scripts to defer
    $defer_scripts = array(
        'nvacore-interests',
        'nvacore-artwork',
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace('<script ', '<script defer ', $tag);
    }
    
    return $tag;
}

/**
 * Preload critical resources
 */
function nvacore_preload_resources() {
    // Preload critical fonts
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=VT323&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    
    // Preload critical CSS
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/styles.css" as="style">';
    
    // DNS prefetch for external resources
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">';
}

/**
 * Input Sanitization and Validation
 */

/**
 * Enhanced input sanitization for custom fields
 */
function nvacore_sanitize_input($input, $type = 'text') {
    switch ($type) {
        case 'email':
            return sanitize_email($input);
            
        case 'url':
            return esc_url_raw($input);
            
        case 'textarea':
            return sanitize_textarea_field($input);
            
        case 'html':
            return wp_kses_post($input);
            
        case 'color':
            return sanitize_hex_color($input);
            
        case 'number':
            return intval($input);
            
        case 'boolean':
            return wp_validate_boolean($input);
            
        case 'slug':
            return sanitize_title($input);
            
        default:
            return sanitize_text_field($input);
    }
}

/**
 * Validate music URLs
 */
function nvacore_validate_music_url($url) {
    if (empty($url)) {
        return true; // Optional field
    }
    
    $parsed_url = parse_url($url);
    
    if (!$parsed_url || !isset($parsed_url['host'])) {
        return false;
    }
    
    $allowed_hosts = array(
        'open.spotify.com',
        'spotify.com',
        'www.youtube.com',
        'youtube.com',
        'youtu.be',
        'soundcloud.com',
        'www.soundcloud.com',
    );
    
    $host = strtolower($parsed_url['host']);
    
    foreach ($allowed_hosts as $allowed_host) {
        if ($host === $allowed_host || str_ends_with($host, '.' . $allowed_host)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Content Security Policy
 */
function nvacore_content_security_policy() {
    if (!is_admin()) {
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com *.gstatic.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' *.googleapis.com *.gstatic.com; ";
        $csp .= "font-src 'self' *.googleapis.com *.gstatic.com; ";
        $csp .= "img-src 'self' data: *.gravatar.com *.w.org; ";
        $csp .= "media-src 'self'; ";
        $csp .= "connect-src 'self' *.spotify.com *.youtube.com *.soundcloud.com; ";
        $csp .= "frame-src 'self' *.youtube.com *.soundcloud.com; ";
        
        header("Content-Security-Policy: $csp");
    }
}
add_action('send_headers', 'nvacore_content_security_policy');

/**
 * CSRF Protection for Forms
 */
function nvacore_verify_csrf_token($token_name = 'nvacore_nonce') {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $token_name)) {
        wp_die('Security check failed', 'Security Error', array('response' => 403));
    }
}

/**
 * Clean up WordPress head
 */
function nvacore_cleanup_head() {
    // Remove unnecessary head elements
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    
    // Remove emoji scripts and styles
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // Remove block library CSS if not needed
    if (!is_admin()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
    }
}
add_action('init', 'nvacore_cleanup_head');

/**
 * Disable unnecessary WordPress features
 */
function nvacore_disable_unnecessary_features() {
    // Disable pingbacks and trackbacks
    add_filter('xmlrpc_methods', function($methods) {
        unset($methods['pingback.ping']);
        unset($methods['pingback.extensions.getPingbacks']);
        return $methods;
    });
    
    // Disable self pingbacks
    add_action('pre_ping', function(&$links) {
        $home = get_option('home');
        foreach ($links as $l => $link) {
            if (strpos($link, $home) === 0) {
                unset($links[$l]);
            }
        }
    });
    
    // Disable WordPress REST API for non-logged in users (optional)
    if (get_theme_mod('nvacore_disable_rest_api', false)) {
        add_filter('rest_authentication_errors', function($result) {
            if (!is_user_logged_in()) {
                return new WP_Error('rest_disabled', 'REST API disabled', array('status' => 401));
            }
            return $result;
        });
    }
}
add_action('init', 'nvacore_disable_unnecessary_features');

/**
 * Database Query Optimization
 */
function nvacore_optimize_queries() {
    // Remove unnecessary queries on frontend
    if (!is_admin()) {
        remove_action('wp_head', 'wp_resource_hints', 2);
        
        // Disable WordPress embeds
        add_filter('embed_oembed_discover', '__return_false');
        add_filter('oembed_response_data', '__return_false');
        wp_deregister_script('wp-embed');
    }
}
add_action('init', 'nvacore_optimize_queries');

/**
 * Log security events
 */
function nvacore_log_security_event($event, $details = '') {
    if (get_theme_mod('nvacore_enable_security_logging', false)) {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'event' => $event,
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        );
        
        // Store in database or file
        update_option('nvacore_security_log', $log_entry);
    }
}

/**
 * Honeypot field for forms
 */
function nvacore_add_honeypot_field() {
    echo '<div style="position: absolute; left: -9999px;">';
    echo '<input type="text" name="website_url" value="" autocomplete="off" tabindex="-1">';
    echo '</div>';
}

/**
 * Check honeypot field
 */
function nvacore_check_honeypot() {
    if (!empty($_POST['website_url'])) {
        nvacore_log_security_event('honeypot_triggered', 'Bot detected in form submission');
        wp_send_json_error('Invalid form submission');
    }
}

/**
 * Backup and restore functions
 */
function nvacore_backup_theme_options() {
    $options = array();
    
    // Get all theme mods
    $theme_mods = get_theme_mods();
    if ($theme_mods) {
        $options['theme_mods'] = $theme_mods;
    }
    
    // Get custom post type content counts
    $post_types = array('artwork', 'guestbook_entry', 'log_entry', 'music_track');
    foreach ($post_types as $post_type) {
        $options['content_counts'][$post_type] = wp_count_posts($post_type)->publish;
    }
    
    return $options;
}

/**
 * Export theme data
 */
function nvacore_export_theme_data() {
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied');
    }
    
    $data = nvacore_backup_theme_options();
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="nvacore-theme-backup-' . date('Y-m-d') . '.json"');
    
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

// Add admin action for backup
add_action('wp_ajax_nvacore_export_data', 'nvacore_export_theme_data');