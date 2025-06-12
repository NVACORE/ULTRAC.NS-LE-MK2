<?php
/**
 * Admin Functions for N°VACORE Terminal Theme
 * 
 * Handles admin-specific functionality, enhanced guestbook management,
 * and custom admin interfaces.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Guestbook Admin Functions
 */

/**
 * AJAX Handler for Admin Guestbook Reply
 */
function nvacore_admin_reply_guestbook() {
    // Verify nonce and permissions
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce') || !current_user_can('manage_options')) {
        wp_die('Permission denied');
    }
    
    $entry_id = intval($_POST['entry_id'] ?? 0);
    $reply_message = sanitize_textarea_field($_POST['reply_message'] ?? '');
    
    if (empty($reply_message) || !$entry_id) {
        wp_send_json_error('Reply message and entry ID are required');
    }
    
    // Create admin reply as a comment
    $comment_data = array(
        'comment_post_ID' => $entry_id,
        'comment_content' => $reply_message,
        'comment_author' => 'N°VACORE',
        'comment_author_email' => get_option('admin_email'),
        'comment_approved' => 1,
        'comment_type' => 'admin_reply',
    );
    
    $comment_id = wp_insert_comment($comment_data);
    
    if ($comment_id) {
        // Mark as admin reply
        update_comment_meta($comment_id, '_is_admin_reply', true);
        
        // Format response
        $response = array(
            'id' => $comment_id,
            'content' => $reply_message,
            'date' => date('m/d/y'),
            'is_admin' => true,
        );
        
        wp_send_json_success($response);
    } else {
        wp_send_json_error('Failed to submit reply');
    }
}
add_action('wp_ajax_nvacore_admin_reply', 'nvacore_admin_reply_guestbook');

/**
 * AJAX Handler for Deleting Guestbook Entry
 */
function nvacore_delete_guestbook_entry() {
    // Verify nonce and permissions
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce') || !current_user_can('manage_options')) {
        wp_die('Permission denied');
    }
    
    $entry_id = intval($_POST['entry_id'] ?? 0);
    
    if (!$entry_id) {
        wp_send_json_error('Entry ID is required');
    }
    
    // Delete the post
    $result = wp_delete_post($entry_id, true);
    
    if ($result) {
        wp_send_json_success('Entry deleted successfully');
    } else {
        wp_send_json_error('Failed to delete entry');
    }
}
add_action('wp_ajax_nvacore_delete_entry', 'nvacore_delete_guestbook_entry');

/**
 * AJAX Handler for Deleting Guestbook Reply
 */
function nvacore_delete_guestbook_reply() {
    // Verify nonce and permissions
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'nvacore_nonce') || !current_user_can('manage_options')) {
        wp_die('Permission denied');
    }
    
    $reply_id = intval($_POST['reply_id'] ?? 0);
    
    if (!$reply_id) {
        wp_send_json_error('Reply ID is required');
    }
    
    // Delete the comment
    $result = wp_delete_comment($reply_id, true);
    
    if ($result) {
        wp_send_json_success('Reply deleted successfully');
    } else {
        wp_send_json_error('Failed to delete reply');
    }
}
add_action('wp_ajax_nvacore_delete_reply', 'nvacore_delete_guestbook_reply');

/**
 * Custom Admin Columns for Post Types
 */

/**
 * Add custom columns to Artwork post type
 */
function nvacore_artwork_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['artwork_thumbnail'] = 'Thumbnail';
    $new_columns['artwork_date'] = 'Artwork Date';
    $new_columns['fallback_color'] = 'Fallback Color';
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_artwork_posts_columns', 'nvacore_artwork_admin_columns');

/**
 * Populate custom columns for Artwork post type
 */
function nvacore_artwork_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'artwork_thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            } else {
                echo '<span style="color: #999;">No image</span>';
            }
            break;
            
        case 'artwork_date':
            $artwork_date = get_post_meta($post_id, '_artwork_date', true);
            echo $artwork_date ? esc_html($artwork_date) : '<span style="color: #999;">Not set</span>';
            break;
            
        case 'fallback_color':
            $color = get_post_meta($post_id, '_fallback_color', true);
            if ($color) {
                echo '<div style="width: 20px; height: 20px; background-color: ' . esc_attr($color) . '; border: 1px solid #ccc; display: inline-block;"></div> ' . esc_html($color);
            } else {
                echo '<span style="color: #999;">Default</span>';
            }
            break;
    }
}
add_action('manage_artwork_posts_custom_column', 'nvacore_artwork_admin_column_content', 10, 2);

/**
 * Add custom columns to Guestbook Entries
 */
function nvacore_guestbook_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['guest_name'] = 'Guest Name';
    $new_columns['guest_message'] = 'Message';
    $new_columns['guest_email'] = 'Email';
    $new_columns['music_track'] = 'Music';
    $new_columns['entry_date'] = 'Entry Date';
    $new_columns['replies_count'] = 'Replies';
    
    return $new_columns;
}
add_filter('manage_guestbook_entry_posts_columns', 'nvacore_guestbook_admin_columns');

/**
 * Populate custom columns for Guestbook Entries
 */
function nvacore_guestbook_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'guest_name':
            $name = get_post_meta($post_id, '_guest_name', true);
            echo $name ? esc_html($name) : '<span style="color: #999;">Anonymous</span>';
            break;
            
        case 'guest_message':
            $post = get_post($post_id);
            $message = wp_trim_words($post->post_content, 10);
            echo esc_html($message);
            break;
            
        case 'guest_email':
            $email = get_post_meta($post_id, '_guest_email', true);
            echo $email ? esc_html($email) : '<span style="color: #999;">Not provided</span>';
            break;
            
        case 'music_track':
            $music_title = get_post_meta($post_id, '_music_title', true);
            $music_url = get_post_meta($post_id, '_music_url', true);
            if ($music_title && $music_url) {
                echo '<a href="' . esc_url($music_url) . '" target="_blank">' . esc_html($music_title) . '</a>';
            } else {
                echo '<span style="color: #999;">No music</span>';
            }
            break;
            
        case 'entry_date':
            $entry_date = get_post_meta($post_id, '_entry_date', true);
            echo $entry_date ? esc_html($entry_date) : '<span style="color: #999;">Not set</span>';
            break;
            
        case 'replies_count':
            $replies_count = get_comments_number($post_id);
            echo intval($replies_count);
            break;
    }
}
add_action('manage_guestbook_entry_posts_custom_column', 'nvacore_guestbook_admin_column_content', 10, 2);

/**
 * Add custom columns to Music Tracks
 */
function nvacore_music_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['track_artist'] = 'Artist';
    $new_columns['track_url'] = 'URL';
    $new_columns['track_order'] = 'Order';
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_music_track_posts_columns', 'nvacore_music_admin_columns');

/**
 * Populate custom columns for Music Tracks
 */
function nvacore_music_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'track_artist':
            $artist = get_post_meta($post_id, '_track_artist', true);
            echo $artist ? esc_html($artist) : '<span style="color: #999;">Unknown Artist</span>';
            break;
            
        case 'track_url':
            $url = get_post_meta($post_id, '_track_url', true);
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank">Play Track</a>';
            } else {
                echo '<span style="color: #999;">No URL</span>';
            }
            break;
            
        case 'track_order':
            $order = get_post_meta($post_id, '_track_order', true);
            echo $order ? intval($order) : '<span style="color: #999;">0</span>';
            break;
    }
}
add_action('manage_music_track_posts_custom_column', 'nvacore_music_admin_column_content', 10, 2);

/**
 * Make custom columns sortable
 */
function nvacore_admin_sortable_columns($columns) {
    $columns['track_order'] = 'track_order';
    $columns['artwork_date'] = 'artwork_date';
    $columns['entry_date'] = 'entry_date';
    
    return $columns;
}
add_filter('manage_edit-music_track_sortable_columns', 'nvacore_admin_sortable_columns');
add_filter('manage_edit-artwork_sortable_columns', 'nvacore_admin_sortable_columns');
add_filter('manage_edit-guestbook_entry_sortable_columns', 'nvacore_admin_sortable_columns');

/**
 * Handle sorting by custom meta
 */
function nvacore_admin_posts_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    switch ($orderby) {
        case 'track_order':
            $query->set('meta_key', '_track_order');
            $query->set('orderby', 'meta_value_num');
            break;
            
        case 'artwork_date':
            $query->set('meta_key', '_artwork_date');
            $query->set('orderby', 'meta_value');
            break;
            
        case 'entry_date':
            $query->set('meta_key', '_entry_date');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'nvacore_admin_posts_orderby');

/**
 * Custom Admin Dashboard Widget
 */
function nvacore_dashboard_widget() {
    wp_add_dashboard_widget(
        'nvacore_stats_widget',
        'N°VACORE Terminal Stats',
        'nvacore_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'nvacore_dashboard_widget');

/**
 * Dashboard widget content
 */
function nvacore_dashboard_widget_content() {
    $artwork_count = wp_count_posts('artwork')->publish;
    $guestbook_count = wp_count_posts('guestbook_entry')->publish;
    $log_count = wp_count_posts('log_entry')->publish;
    $music_count = wp_count_posts('music_track')->publish;
    
    $recent_entries = get_posts(array(
        'post_type' => 'guestbook_entry',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 20px;">';
    
    echo '<div style="text-align: center; padding: 10px; background: #f0f0f1; border-radius: 5px;">';
    echo '<strong style="display: block; font-size: 24px; color: #c4a0ff;">' . $artwork_count . '</strong>';
    echo '<span style="font-size: 12px;">Artworks</span>';
    echo '</div>';
    
    echo '<div style="text-align: center; padding: 10px; background: #f0f0f1; border-radius: 5px;">';
    echo '<strong style="display: block; font-size: 24px; color: #c4a0ff;">' . $guestbook_count . '</strong>';
    echo '<span style="font-size: 12px;">Guestbook Entries</span>';
    echo '</div>';
    
    echo '<div style="text-align: center; padding: 10px; background: #f0f0f1; border-radius: 5px;">';
    echo '<strong style="display: block; font-size: 24px; color: #c4a0ff;">' . $log_count . '</strong>';
    echo '<span style="font-size: 12px;">Log Entries</span>';
    echo '</div>';
    
    echo '<div style="text-align: center; padding: 10px; background: #f0f0f1; border-radius: 5px;">';
    echo '<strong style="display: block; font-size: 24px; color: #c4a0ff;">' . $music_count . '</strong>';
    echo '<span style="font-size: 12px;">Music Tracks</span>';
    echo '</div>';
    
    echo '</div>';
    
    if ($recent_entries) {
        echo '<h4 style="margin-top: 20px;">Recent Guestbook Entries</h4>';
        echo '<ul style="margin: 0; padding-left: 20px;">';
        foreach ($recent_entries as $entry) {
            $guest_name = get_post_meta($entry->ID, '_guest_name', true);
            $entry_date = get_post_meta($entry->ID, '_entry_date', true);
            
            echo '<li style="margin-bottom: 5px;">';
            echo '<strong>' . esc_html($guest_name) . '</strong> ';
            echo '<span style="color: #666;">(' . esc_html($entry_date) . ')</span>';
            echo '<br><small>' . wp_trim_words($entry->post_content, 10) . '</small>';
            echo '</li>';
        }
        echo '</ul>';
    }
    
    echo '<p style="margin-top: 20px; text-align: center;">';
    echo '<a href="' . admin_url('edit.php?post_type=guestbook_entry') . '" class="button button-primary">Manage Guestbook</a> ';
    echo '<a href="' . admin_url('edit.php?post_type=artwork') . '" class="button">Manage Artwork</a>';
    echo '</p>';
}

/**
 * Admin Menu Customization
 */
function nvacore_admin_menu_customization() {
    // Remove unnecessary menu items for cleaner admin
    if (!current_user_can('administrator')) {
        remove_menu_page('edit-comments.php');
        remove_menu_page('tools.php');
    }
    
    // Reorder menu items
    add_filter('custom_menu_order', function() { return true; });
    add_filter('menu_order', function($menu_order) {
        return array(
            'index.php', // Dashboard
            'separator1',
            'edit.php?post_type=artwork',
            'edit.php?post_type=guestbook_entry',
            'edit.php?post_type=log_entry',
            'edit.php?post_type=music_track',
            'separator2',
            'edit.php?post_type=page',
            'themes.php',
            'users.php',
            'tools.php',
            'options-general.php',
        );
    });
}
add_action('admin_menu', 'nvacore_admin_menu_customization');

/**
 * Admin Notice for Theme Setup
 */
function nvacore_admin_setup_notice() {
    $required_pages = array('About', 'Contact', 'Commissions', 'Discord', 'Ultracore');
    $missing_pages = array();
    
    foreach ($required_pages as $page_title) {
        if (!get_page_by_title($page_title)) {
            $missing_pages[] = $page_title;
        }
    }
    
    if (!empty($missing_pages) && current_user_can('manage_options')) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>N°VACORE Terminal Theme:</strong> Some pages are missing: ' . implode(', ', $missing_pages) . '</p>';
        echo '<p><a href="' . admin_url('post-new.php?post_type=page') . '" class="button button-primary">Create Pages</a></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'nvacore_admin_setup_notice');

/**
 * Quick Setup Function
 */
function nvacore_create_default_pages() {
    $pages = array(
        'About' => 'Welcome to N°VACORE\'s digital space! This is my personal directory for art, projects, and various digital experiments.',
        'Contact' => 'Feel free to reach out for collaborations, commissions, or just to chat!',
        'Commissions' => 'I offer custom artwork, digital assets, and design services. Specialized in pixel art, glitch aesthetics, and dithered compositions.',
        'Discord' => 'Welcome Traveler!! The GANGO community server is fueled with the energy of creatives of all typeZ! #GANGO111',
        'Ultracore' => 'ULTRAC°RE is a digital consciousness copy of N°VA that has been manifested as a TV-headed robot entity.',
    );
    
    foreach ($pages as $title => $content) {
        if (!get_page_by_title($title)) {
            wp_insert_post(array(
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
        }
    }
}

/**
 * AJAX Handler for Quick Setup
 */
function nvacore_quick_setup() {
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied');
    }
    
    nvacore_create_default_pages();
    wp_send_json_success('Default pages created successfully');
}
add_action('wp_ajax_nvacore_quick_setup', 'nvacore_quick_setup');