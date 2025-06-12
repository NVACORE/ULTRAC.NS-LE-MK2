<?php
/**
 * N°VACORE Terminal WordPress Theme Functions
 * 
 * Handles theme setup, custom post types, scripts, styles, and custom functionality
 * for the terminal-style portfolio website.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function nvacore_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Add custom image sizes for artwork
    add_image_size('artwork-thumbnail', 300, 200, true);
    add_image_size('artwork-full', 1200, 800, false);
    
    // Register navigation menus (though we'll use custom navigation)
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'nvacore-terminal'),
    ));
}
add_action('after_setup_theme', 'nvacore_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function nvacore_enqueue_assets() {
    // Get theme version for cache busting
    $theme_version = wp_get_theme()->get('Version');
    
    // Enqueue styles
    wp_enqueue_style('nvacore-main-styles', get_template_directory_uri() . '/assets/css/styles.css', array(), $theme_version);
    
    // Enqueue scripts with dependencies
    wp_enqueue_script('nvacore-config', get_template_directory_uri() . '/assets/js/config.js', array(), $theme_version, true);
    wp_enqueue_script('nvacore-main', get_template_directory_uri() . '/assets/js/main.js', array('nvacore-config'), $theme_version, true);
    wp_enqueue_script('nvacore-windows', get_template_directory_uri() . '/assets/js/windows.js', array('nvacore-main'), $theme_version, true);
    wp_enqueue_script('nvacore-jukebox', get_template_directory_uri() . '/assets/js/jukebox.js', array('nvacore-config'), $theme_version, true);
    wp_enqueue_script('nvacore-artwork', get_template_directory_uri() . '/assets/js/artwork.js', array('nvacore-windows'), $theme_version, true);
    wp_enqueue_script('nvacore-guestbook', get_template_directory_uri() . '/assets/js/guestbook.js', array('nvacore-config'), $theme_version, true);
    wp_enqueue_script('nvacore-interests', get_template_directory_uri() . '/assets/js/interests.js', array('nvacore-main'), $theme_version, true);
    
    // Localize script for AJAX and WordPress data
    wp_localize_script('nvacore-main', 'nvacore_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nvacore_nonce'),
        'theme_url' => get_template_directory_uri(),
        'home_url' => home_url(),
        'is_admin' => current_user_can('manage_options'),
        'user_id' => get_current_user_id(),
    ));
}
add_action('wp_enqueue_scripts', 'nvacore_enqueue_assets');

/**
 * Register Custom Post Types
 */
function nvacore_register_post_types() {
    
    // Artwork Post Type
    register_post_type('artwork', array(
        'labels' => array(
            'name' => 'Artwork',
            'singular_name' => 'Artwork',
            'add_new' => 'Add New Artwork',
            'add_new_item' => 'Add New Artwork',
            'edit_item' => 'Edit Artwork',
            'new_item' => 'New Artwork',
            'view_item' => 'View Artwork',
            'search_items' => 'Search Artwork',
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-art',
        'show_in_rest' => true,
    ));
    
    // Log Entries Post Type
    register_post_type('log_entry', array(
        'labels' => array(
            'name' => 'Log Entries',
            'singular_name' => 'Log Entry',
            'add_new' => 'Add New Entry',
            'add_new_item' => 'Add New Log Entry',
            'edit_item' => 'Edit Log Entry',
            'new_item' => 'New Log Entry',
            'view_item' => 'View Log Entry',
            'search_items' => 'Search Log Entries',
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-edit',
        'show_in_rest' => true,
    ));
    
    // Guestbook Entries Post Type
    register_post_type('guestbook_entry', array(
        'labels' => array(
            'name' => 'Guestbook Entries',
            'singular_name' => 'Guestbook Entry',
            'add_new' => 'Add New Entry',
            'add_new_item' => 'Add New Guestbook Entry',
            'edit_item' => 'Edit Guestbook Entry',
            'new_item' => 'New Guestbook Entry',
            'view_item' => 'View Guestbook Entry',
            'search_items' => 'Search Guestbook',
        ),
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-email',
        'show_in_rest' => true,
        'capability_type' => 'post',
    ));
    
    // Music Tracks Post Type
    register_post_type('music_track', array(
        'labels' => array(
            'name' => 'Music Tracks',
            'singular_name' => 'Music Track',
            'add_new' => 'Add New Track',
            'add_new_item' => 'Add New Music Track',
            'edit_item' => 'Edit Music Track',
            'new_item' => 'New Music Track',
            'view_item' => 'View Music Track',
            'search_items' => 'Search Music',
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-format-audio',
        'show_in_rest' => true,
    ));
}
add_action('init', 'nvacore_register_post_types');

/**
 * Custom Meta Boxes
 */
function nvacore_add_meta_boxes() {
    // Artwork meta box
    add_meta_box(
        'artwork_details',
        'Artwork Details',
        'nvacore_artwork_meta_box',
        'artwork',
        'normal',
        'high'
    );
    
    // Guestbook meta box
    add_meta_box(
        'guestbook_details',
        'Guestbook Entry Details',
        'nvacore_guestbook_meta_box',
        'guestbook_entry',
        'normal',
        'high'
    );
    
    // Music track meta box
    add_meta_box(
        'music_details',
        'Music Track Details',
        'nvacore_music_meta_box',
        'music_track',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'nvacore_add_meta_boxes');

/**
 * Artwork Meta Box
 */
function nvacore_artwork_meta_box($post) {
    wp_nonce_field('nvacore_artwork_meta', 'nvacore_artwork_nonce');
    
    $artwork_date = get_post_meta($post->ID, '_artwork_date', true);
    $fallback_color = get_post_meta($post->ID, '_fallback_color', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="artwork_date">Artwork Date</label></th>
            <td><input type="text" id="artwork_date" name="artwork_date" value="<?php echo esc_attr($artwork_date); ?>" placeholder="MM/DD/YY" /></td>
        </tr>
        <tr>
            <th><label for="fallback_color">Fallback Color</label></th>
            <td><input type="color" id="fallback_color" name="fallback_color" value="<?php echo esc_attr($fallback_color ?: '#333333'); ?>" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Guestbook Meta Box
 */
function nvacore_guestbook_meta_box($post) {
    wp_nonce_field('nvacore_guestbook_meta', 'nvacore_guestbook_nonce');
    
    $guest_name = get_post_meta($post->ID, '_guest_name', true);
    $guest_email = get_post_meta($post->ID, '_guest_email', true);
    $guest_website = get_post_meta($post->ID, '_guest_website', true);
    $music_url = get_post_meta($post->ID, '_music_url', true);
    $music_title = get_post_meta($post->ID, '_music_title', true);
    $music_artist = get_post_meta($post->ID, '_music_artist', true);
    $entry_date = get_post_meta($post->ID, '_entry_date', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="guest_name">Guest Name</label></th>
            <td><input type="text" id="guest_name" name="guest_name" value="<?php echo esc_attr($guest_name); ?>" /></td>
        </tr>
        <tr>
            <th><label for="guest_email">Guest Email</label></th>
            <td><input type="email" id="guest_email" name="guest_email" value="<?php echo esc_attr($guest_email); ?>" /></td>
        </tr>
        <tr>
            <th><label for="guest_website">Guest Website</label></th>
            <td><input type="url" id="guest_website" name="guest_website" value="<?php echo esc_attr($guest_website); ?>" /></td>
        </tr>
        <tr>
            <th><label for="music_url">Music URL</label></th>
            <td><input type="url" id="music_url" name="music_url" value="<?php echo esc_attr($music_url); ?>" /></td>
        </tr>
        <tr>
            <th><label for="music_title">Music Title</label></th>
            <td><input type="text" id="music_title" name="music_title" value="<?php echo esc_attr($music_title); ?>" /></td>
        </tr>
        <tr>
            <th><label for="music_artist">Music Artist</label></th>
            <td><input type="text" id="music_artist" name="music_artist" value="<?php echo esc_attr($music_artist); ?>" /></td>
        </tr>
        <tr>
            <th><label for="entry_date">Entry Date</label></th>
            <td><input type="text" id="entry_date" name="entry_date" value="<?php echo esc_attr($entry_date); ?>" placeholder="MM/DD/YY" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Music Track Meta Box
 */
function nvacore_music_meta_box($post) {
    wp_nonce_field('nvacore_music_meta', 'nvacore_music_nonce');
    
    $track_url = get_post_meta($post->ID, '_track_url', true);
    $track_artist = get_post_meta($post->ID, '_track_artist', true);
    $track_order = get_post_meta($post->ID, '_track_order', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="track_url">Track URL</label></th>
            <td><input type="url" id="track_url" name="track_url" value="<?php echo esc_attr($track_url); ?>" /></td>
        </tr>
        <tr>
            <th><label for="track_artist">Artist</label></th>
            <td><input type="text" id="track_artist" name="track_artist" value="<?php echo esc_attr($track_artist); ?>" /></td>
        </tr>
        <tr>
            <th><label for="track_order">Playlist Order</label></th>
            <td><input type="number" id="track_order" name="track_order" value="<?php echo esc_attr($track_order); ?>" min="0" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Save Meta Box Data
 */
function nvacore_save_meta_boxes($post_id) {
    // Check if nonce is valid
    if (!wp_verify_nonce($_POST['nvacore_artwork_nonce'] ?? '', 'nvacore_artwork_meta') &&
        !wp_verify_nonce($_POST['nvacore_guestbook_nonce'] ?? '', 'nvacore_guestbook_meta') &&
        !wp_verify_nonce($_POST['nvacore_music_nonce'] ?? '', 'nvacore_music_meta')) {
        return;
    }
    
    // Check if user has permission
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save artwork meta
    if (isset($_POST['artwork_date'])) {
        update_post_meta($post_id, '_artwork_date', sanitize_text_field($_POST['artwork_date']));
    }
    if (isset($_POST['fallback_color'])) {
        update_post_meta($post_id, '_fallback_color', sanitize_hex_color($_POST['fallback_color']));
    }
    
    // Save guestbook meta
    $guestbook_fields = ['guest_name', 'guest_email', 'guest_website', 'music_url', 'music_title', 'music_artist', 'entry_date'];
    foreach ($guestbook_fields as $field) {
        if (isset($_POST[$field])) {
            if (in_array($field, ['guest_email', 'guest_website', 'music_url'])) {
                update_post_meta($post_id, '_' . $field, sanitize_url($_POST[$field]));
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    // Save music track meta
    if (isset($_POST['track_url'])) {
        update_post_meta($post_id, '_track_url', sanitize_url($_POST['track_url']));
    }
    if (isset($_POST['track_artist'])) {
        update_post_meta($post_id, '_track_artist', sanitize_text_field($_POST['track_artist']));
    }
    if (isset($_POST['track_order'])) {
        update_post_meta($post_id, '_track_order', intval($_POST['track_order']));
    }
}
add_action('save_post', 'nvacore_save_meta_boxes');

/**
 * AJAX Handler for Guestbook Submissions
 */
function nvacore_handle_guestbook_submission() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce')) {
        wp_die('Security check failed');
    }
    
    $guest_name = sanitize_text_field($_POST['guest_name'] ?? '');
    $guest_email = sanitize_email($_POST['guest_email'] ?? '');
    $guest_website = sanitize_url($_POST['guest_website'] ?? '');
    $guest_message = sanitize_textarea_field($_POST['guest_message'] ?? '');
    $music_url = sanitize_url($_POST['music_url'] ?? '');
    $music_title = sanitize_text_field($_POST['music_title'] ?? '');
    $music_artist = sanitize_text_field($_POST['music_artist'] ?? '');
    
    // Validate required fields
    if (empty($guest_name) || empty($guest_message)) {
        wp_send_json_error('Name and message are required');
    }
    
    // Create guestbook entry
    $entry_id = wp_insert_post(array(
        'post_type' => 'guestbook_entry',
        'post_title' => 'Entry by ' . $guest_name,
        'post_content' => $guest_message,
        'post_status' => 'publish',
        'meta_input' => array(
            '_guest_name' => $guest_name,
            '_guest_email' => $guest_email,
            '_guest_website' => $guest_website,
            '_music_url' => $music_url,
            '_music_title' => $music_title,
            '_music_artist' => $music_artist,
            '_entry_date' => date('m/d/y'),
        ),
    ));
    
    if ($entry_id) {
        wp_send_json_success('Entry submitted successfully');
    } else {
        wp_send_json_error('Failed to submit entry');
    }
}
add_action('wp_ajax_nvacore_guestbook_submit', 'nvacore_handle_guestbook_submission');
add_action('wp_ajax_nopriv_nvacore_guestbook_submit', 'nvacore_handle_guestbook_submission');

/**
 * AJAX Handler for Loading Guestbook Entries
 */
function nvacore_load_guestbook_entries() {
    $entries = get_posts(array(
        'post_type' => 'guestbook_entry',
        'posts_per_page' => 10,
        'meta_key' => '_entry_date',
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    $formatted_entries = array();
    foreach ($entries as $entry) {
        $formatted_entries[] = array(
            'id' => $entry->ID,
            'name' => get_post_meta($entry->ID, '_guest_name', true),
            'email' => get_post_meta($entry->ID, '_guest_email', true),
            'website' => get_post_meta($entry->ID, '_guest_website', true),
            'message' => $entry->post_content,
            'date' => get_post_meta($entry->ID, '_entry_date', true),
            'music_url' => get_post_meta($entry->ID, '_music_url', true),
            'music_title' => get_post_meta($entry->ID, '_music_title', true),
            'music_artist' => get_post_meta($entry->ID, '_music_artist', true),
        );
    }
    
    wp_send_json_success($formatted_entries);
}
add_action('wp_ajax_nvacore_load_guestbook', 'nvacore_load_guestbook_entries');
add_action('wp_ajax_nopriv_nvacore_load_guestbook', 'nvacore_load_guestbook_entries');

/**
 * Get Artwork Collection for JavaScript
 */
function nvacore_get_artwork_collection() {
    $artwork_posts = get_posts(array(
        'post_type' => 'artwork',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    $collection = array();
    foreach ($artwork_posts as $artwork) {
        $thumbnail = get_the_post_thumbnail_url($artwork->ID, 'artwork-thumbnail');
        $full_image = get_the_post_thumbnail_url($artwork->ID, 'artwork-full');
        
        $collection[] = array(
            'id' => 'artwork-' . $artwork->ID,
            'title' => $artwork->post_title,
            'date' => get_post_meta($artwork->ID, '_artwork_date', true),
            'image' => $thumbnail ? basename($thumbnail) : 'placeholder.png',
            'full_image' => $full_image,
            'fallbackColor' => get_post_meta($artwork->ID, '_fallback_color', true) ?: '#333333',
            'description' => $artwork->post_content,
        );
    }
    
    return $collection;
}

/**
 * Get Music Playlist for JavaScript
 */
function nvacore_get_music_playlist() {
    $music_posts = get_posts(array(
        'post_type' => 'music_track',
        'posts_per_page' => -1,
        'meta_key' => '_track_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ));
    
    $playlist = array();
    foreach ($music_posts as $track) {
        $playlist[] = array(
            'title' => $track->post_title,
            'artist' => get_post_meta($track->ID, '_track_artist', true),
            'url' => get_post_meta($track->ID, '_track_url', true),
        );
    }
    
    return $playlist;
}

/**
 * Get Log Entries for JavaScript
 */
function nvacore_get_log_entries() {
    $log_posts = get_posts(array(
        'post_type' => 'log_entry',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    $entries = array();
    foreach ($log_posts as $log) {
        $entries[] = array(
            'date' => get_post_meta($log->ID, '_log_date', true) ?: date('m/d/y', strtotime($log->post_date)),
            'content' => $log->post_content,
        );
    }
    
    return $entries;
}

/**
 * Add data to JavaScript
 */
function nvacore_add_js_data() {
    if (is_admin()) return;
    
    $js_data = array(
        'artwork_collection' => nvacore_get_artwork_collection(),
        'music_playlist' => nvacore_get_music_playlist(),
        'log_entries' => nvacore_get_log_entries(),
    );
    
    echo '<script type="text/javascript">';
    echo 'window.nvacore_data = ' . json_encode($js_data) . ';';
    echo '</script>';
}
add_action('wp_footer', 'nvacore_add_js_data');

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

/**
 * Custom Admin Styles
 */
function nvacore_admin_styles() {
    echo '<style>
        .post-type-artwork .dashicons-art:before,
        .post-type-log_entry .dashicons-edit:before,
        .post-type-guestbook_entry .dashicons-email:before,
        .post-type-music_track .dashicons-format-audio:before {
            color: #c4a0ff;
        }
    </style>';
}
add_action('admin_head', 'nvacore_admin_styles');

/**
 * Disable WordPress admin bar for cleaner frontend experience
 */
function nvacore_disable_admin_bar() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'nvacore_disable_admin_bar');

/**
 * Add body classes for styling
 */
function nvacore_body_classes($classes) {
    $classes[] = 'nvacore-terminal';
    if (wp_is_mobile()) {
        $classes[] = 'mobile-device';
    } else {
        $classes[] = 'desktop-device';
    }
    return $classes;
}
add_filter('body_class', 'nvacore_body_classes');

/**
 * Include theme modules and initialization
 */
require_once get_template_directory() . '/inc/includes.php';