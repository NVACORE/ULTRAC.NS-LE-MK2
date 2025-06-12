<?php
/**
 * AJAX Handlers for N°VACORE Terminal Theme
 * 
 * Handles all AJAX requests for dynamic functionality including
 * guestbook operations, music metadata fetching, and admin functions.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Guestbook AJAX Handlers
 */

/**
 * Submit guestbook entry with music integration
 */
function nvacore_submit_guestbook_entry() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    // Sanitize and validate input
    $guest_name = sanitize_text_field($_POST['guest_name'] ?? '');
    $guest_email = sanitize_email($_POST['guest_email'] ?? '');
    $guest_website = sanitize_url($_POST['guest_website'] ?? '');
    $guest_message = sanitize_textarea_field($_POST['guest_message'] ?? '');
    $music_url = sanitize_url($_POST['music_url'] ?? '');
    $music_title = sanitize_text_field($_POST['music_title'] ?? '');
    $music_artist = sanitize_text_field($_POST['music_artist'] ?? '');
    $music_source = sanitize_text_field($_POST['music_source'] ?? '');
    
    // Validate required fields
    if (empty($guest_name)) {
        wp_send_json_error('Name is required');
    }
    
    if (empty($guest_message)) {
        wp_send_json_error('Message is required');
    }
    
    // Validate email format if provided
    if (!empty($guest_email) && !is_email($guest_email)) {
        wp_send_json_error('Please enter a valid email address');
    }
    
    // Rate limiting - check for recent submissions from same IP
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $recent_entries = get_posts(array(
        'post_type' => 'guestbook_entry',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_user_ip',
                'value' => $user_ip,
            ),
        ),
        'date_query' => array(
            array(
                'after' => '1 hour ago',
            ),
        ),
    ));
    
    if (!empty($recent_entries) && !current_user_can('manage_options')) {
        wp_send_json_error('Please wait before submitting another entry');
    }
    
    // Create guestbook entry
    $entry_data = array(
        'post_type' => 'guestbook_entry',
        'post_title' => 'Entry by ' . $guest_name . ' - ' . date('Y-m-d H:i:s'),
        'post_content' => $guest_message,
        'post_status' => 'publish',
        'meta_input' => array(
            '_guest_name' => $guest_name,
            '_guest_email' => $guest_email,
            '_guest_website' => $guest_website,
            '_music_url' => $music_url,
            '_music_title' => $music_title,
            '_music_artist' => $music_artist,
            '_music_source' => $music_source,
            '_entry_date' => date('m/d/y'),
            '_user_ip' => $user_ip,
            '_user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
        ),
    );
    
    $entry_id = wp_insert_post($entry_data);
    
    if (is_wp_error($entry_id)) {
        wp_send_json_error('Failed to submit entry: ' . $entry_id->get_error_message());
    }
    
    if (!$entry_id) {
        wp_send_json_error('Failed to submit entry');
    }
    
    // Return success with entry data
    $response_data = array(
        'id' => $entry_id,
        'name' => $guest_name,
        'email' => $guest_email,
        'website' => $guest_website,
        'message' => $guest_message,
        'date' => date('m/d/y'),
        'music' => $music_url ? array(
            'url' => $music_url,
            'title' => $music_title,
            'artist' => $music_artist,
            'source' => $music_source,
        ) : null,
    );
    
    wp_send_json_success($response_data);
}
add_action('wp_ajax_submit_guestbook_entry', 'nvacore_submit_guestbook_entry');
add_action('wp_ajax_nopriv_submit_guestbook_entry', 'nvacore_submit_guestbook_entry');

/**
 * Fetch music metadata from various services
 */
function nvacore_fetch_music_metadata() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    $music_url = sanitize_url($_POST['music_url'] ?? '');
    
    if (empty($music_url)) {
        wp_send_json_error('URL is required');
    }
    
    // Parse URL to determine service
    $parsed_url = parse_url($music_url);
    $host = $parsed_url['host'] ?? '';
    
    $metadata = null;
    
    try {
        if (strpos($host, 'spotify.com') !== false) {
            $metadata = nvacore_fetch_spotify_metadata($music_url);
        } elseif (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) {
            $metadata = nvacore_fetch_youtube_metadata($music_url);
        } elseif (strpos($host, 'soundcloud.com') !== false) {
            $metadata = nvacore_fetch_soundcloud_metadata($music_url);
        } else {
            wp_send_json_error('Unsupported music service');
        }
        
        if ($metadata) {
            wp_send_json_success($metadata);
        } else {
            wp_send_json_error('Could not fetch metadata');
        }
        
    } catch (Exception $e) {
        wp_send_json_error('Error fetching metadata: ' . $e->getMessage());
    }
}
add_action('wp_ajax_fetch_music_metadata', 'nvacore_fetch_music_metadata');
add_action('wp_ajax_nopriv_fetch_music_metadata', 'nvacore_fetch_music_metadata');

/**
 * Fetch Spotify metadata using oEmbed
 */
function nvacore_fetch_spotify_metadata($url) {
    $oembed_url = 'https://open.spotify.com/oembed?url=' . urlencode($url);
    
    $response = wp_remote_get($oembed_url, array(
        'timeout' => 10,
        'headers' => array(
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ),
    ));
    
    if (is_wp_error($response)) {
        return null;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || !isset($data['title'])) {
        return null;
    }
    
    // Parse title to extract artist and song
    $title = $data['title'];
    $parts = explode(' - ', $title, 2);
    
    return array(
        'title' => isset($parts[1]) ? trim($parts[1]) : $title,
        'artist' => isset($parts[1]) ? trim($parts[0]) : '',
        'source' => 'Spotify',
        'url' => $url,
        'thumbnail' => $data['thumbnail_url'] ?? '',
    );
}

/**
 * Fetch YouTube metadata using oEmbed
 */
function nvacore_fetch_youtube_metadata($url) {
    $oembed_url = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
    
    $response = wp_remote_get($oembed_url, array(
        'timeout' => 10,
        'headers' => array(
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ),
    ));
    
    if (is_wp_error($response)) {
        return null;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || !isset($data['title'])) {
        return null;
    }
    
    return array(
        'title' => $data['title'],
        'artist' => $data['author_name'] ?? '',
        'source' => 'YouTube',
        'url' => $url,
        'thumbnail' => $data['thumbnail_url'] ?? '',
    );
}

/**
 * Fetch SoundCloud metadata using oEmbed
 */
function nvacore_fetch_soundcloud_metadata($url) {
    $oembed_url = 'https://soundcloud.com/oembed?url=' . urlencode($url) . '&format=json';
    
    $response = wp_remote_get($oembed_url, array(
        'timeout' => 10,
        'headers' => array(
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ),
    ));
    
    if (is_wp_error($response)) {
        return null;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || !isset($data['title'])) {
        return null;
    }
    
    return array(
        'title' => $data['title'],
        'artist' => $data['author_name'] ?? '',
        'source' => 'SoundCloud',
        'url' => $url,
        'thumbnail' => $data['thumbnail_url'] ?? '',
    );
}

/**
 * Load guestbook entries with pagination
 */
function nvacore_load_guestbook_entries() {
    $page = intval($_POST['page'] ?? 1);
    $per_page = 10;
    
    $entries = get_posts(array(
        'post_type' => 'guestbook_entry',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    $formatted_entries = array();
    
    foreach ($entries as $entry) {
        $music_data = null;
        $music_url = get_post_meta($entry->ID, '_music_url', true);
        
        if ($music_url) {
            $music_data = array(
                'url' => $music_url,
                'title' => get_post_meta($entry->ID, '_music_title', true),
                'artist' => get_post_meta($entry->ID, '_music_artist', true),
                'source' => get_post_meta($entry->ID, '_music_source', true),
            );
        }
        
        // Get replies
        $replies = get_comments(array(
            'post_id' => $entry->ID,
            'status' => 'approve',
            'order' => 'ASC',
        ));
        
        $formatted_replies = array();
        foreach ($replies as $reply) {
            $formatted_replies[] = array(
                'id' => $reply->comment_ID,
                'name' => $reply->comment_author,
                'message' => $reply->comment_content,
                'date' => date('m/d/y', strtotime($reply->comment_date)),
                'is_admin' => get_comment_meta($reply->comment_ID, '_is_admin_reply', true),
            );
        }
        
        $formatted_entries[] = array(
            'id' => $entry->ID,
            'name' => get_post_meta($entry->ID, '_guest_name', true),
            'email' => get_post_meta($entry->ID, '_guest_email', true),
            'website' => get_post_meta($entry->ID, '_guest_website', true),
            'message' => $entry->post_content,
            'date' => get_post_meta($entry->ID, '_entry_date', true),
            'music' => $music_data,
            'replies' => $formatted_replies,
        );
    }
    
    // Check if there are more entries
    $total_entries = wp_count_posts('guestbook_entry')->publish;
    $has_more = ($page * $per_page) < $total_entries;
    
    wp_send_json_success(array(
        'entries' => $formatted_entries,
        'has_more' => $has_more,
        'page' => $page,
        'total' => $total_entries,
    ));
}
add_action('wp_ajax_load_guestbook_entries', 'nvacore_load_guestbook_entries');
add_action('wp_ajax_nopriv_load_guestbook_entries', 'nvacore_load_guestbook_entries');

/**
 * Get artwork collection for JavaScript
 */
function nvacore_get_artwork_collection_ajax() {
    $artwork_posts = get_posts(array(
        'post_type' => 'artwork',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    $collection = array();
    foreach ($artwork_posts as $artwork) {
        $thumbnail_id = get_post_thumbnail_id($artwork->ID);
        $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'artwork-thumbnail');
        $full_url = wp_get_attachment_image_url($thumbnail_id, 'artwork-full');
        
        $collection[] = array(
            'id' => 'artwork-' . $artwork->ID,
            'title' => $artwork->post_title,
            'date' => get_post_meta($artwork->ID, '_artwork_date', true),
            'image' => $thumbnail_url ? basename($thumbnail_url) : 'placeholder.png',
            'full_image' => $full_url,
            'fallbackColor' => get_post_meta($artwork->ID, '_fallback_color', true) ?: '#333333',
            'description' => $artwork->post_content,
        );
    }
    
    wp_send_json_success($collection);
}
add_action('wp_ajax_get_artwork_collection', 'nvacore_get_artwork_collection_ajax');
add_action('wp_ajax_nopriv_get_artwork_collection', 'nvacore_get_artwork_collection_ajax');

/**
 * Get music playlist for JavaScript
 */
function nvacore_get_music_playlist_ajax() {
    $music_posts = get_posts(array(
        'post_type' => 'music_track',
        'posts_per_page' => -1,
        'meta_key' => '_track_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ));
    
    $playlist = array();
    foreach ($music_posts as $track) {
        $track_url = get_post_meta($track->ID, '_track_url', true);
        
        // Skip tracks without URLs
        if (!$track_url) {
            continue;
        }
        
        $playlist[] = array(
            'title' => $track->post_title,
            'artist' => get_post_meta($track->ID, '_track_artist', true),
            'url' => $track_url,
        );
    }
    
    wp_send_json_success($playlist);
}
add_action('wp_ajax_get_music_playlist', 'nvacore_get_music_playlist_ajax');
add_action('wp_ajax_nopriv_get_music_playlist', 'nvacore_get_music_playlist_ajax');

/**
 * Get log entries for JavaScript
 */
function nvacore_get_log_entries_ajax() {
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
    
    wp_send_json_success($entries);
}
add_action('wp_ajax_get_log_entries', 'nvacore_get_log_entries_ajax');
add_action('wp_ajax_nopriv_get_log_entries', 'nvacore_get_log_entries_ajax');

/**
 * Save user preferences (theme, volume, etc.)
 */
function nvacore_save_user_preferences() {
    $preferences = array();
    
    // Sanitize and validate preferences
    if (isset($_POST['theme'])) {
        $theme = sanitize_text_field($_POST['theme']);
        if (in_array($theme, array('dark', 'light'))) {
            $preferences['theme'] = $theme;
        }
    }
    
    if (isset($_POST['volume'])) {
        $volume = intval($_POST['volume']);
        if ($volume >= 0 && $volume <= 100) {
            $preferences['volume'] = $volume;
        }
    }
    
    if (isset($_POST['jukebox_muted'])) {
        $preferences['jukebox_muted'] = wp_validate_boolean($_POST['jukebox_muted']);
    }
    
    if (isset($_POST['current_track'])) {
        $preferences['current_track'] = intval($_POST['current_track']);
    }
    
    // Save to user meta if logged in, otherwise use cookies
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'nvacore_preferences', $preferences);
    } else {
        // Set cookies for anonymous users (expires in 30 days)
        foreach ($preferences as $key => $value) {
            setcookie("nvacore_{$key}", $value, time() + (30 * 24 * 60 * 60), '/');
        }
    }
    
    wp_send_json_success('Preferences saved');
}
add_action('wp_ajax_save_user_preferences', 'nvacore_save_user_preferences');
add_action('wp_ajax_nopriv_save_user_preferences', 'nvacore_save_user_preferences');

/**
 * Get user preferences
 */
function nvacore_get_user_preferences() {
    $preferences = array();
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $preferences = get_user_meta($user_id, 'nvacore_preferences', true) ?: array();
    } else {
        // Get from cookies for anonymous users
        $preferences = array(
            'theme' => $_COOKIE['nvacore_theme'] ?? 'dark',
            'volume' => intval($_COOKIE['nvacore_volume'] ?? 50),
            'jukebox_muted' => wp_validate_boolean($_COOKIE['nvacore_jukebox_muted'] ?? false),
            'current_track' => intval($_COOKIE['nvacore_current_track'] ?? 0),
        );
    }
    
    wp_send_json_success($preferences);
}
add_action('wp_ajax_get_user_preferences', 'nvacore_get_user_preferences');
add_action('wp_ajax_nopriv_get_user_preferences', 'nvacore_get_user_preferences');

/**
 * Handle contact form submission (if needed)
 */
function nvacore_submit_contact_form() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error('All fields are required');
    }
    
    if (!is_email($email)) {
        wp_send_json_error('Please enter a valid email address');
    }
    
    // Send email to admin
    $to = get_option('admin_email');
    $email_subject = 'Contact Form: ' . ($subject ?: 'New Message');
    $email_message = "Name: {$name}\n";
    $email_message .= "Email: {$email}\n\n";
    $email_message .= "Message:\n{$message}";
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    
    $sent = wp_mail($to, $email_subject, $email_message, $headers);
    
    if ($sent) {
        wp_send_json_success('Message sent successfully');
    } else {
        wp_send_json_error('Failed to send message');
    }
}
add_action('wp_ajax_submit_contact_form', 'nvacore_submit_contact_form');
add_action('wp_ajax_nopriv_submit_contact_form', 'nvacore_submit_contact_form');