<?php
/**
 * Admin Interface for N°VACORE Terminal Theme
 * 
 * Custom admin pages and interfaces for theme management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom admin menu
 */
function nvacore_admin_menu() {
    add_menu_page(
        'N°VACORE Terminal',
        'N°VACORE',
        'manage_options',
        'nvacore-terminal',
        'nvacore_admin_dashboard',
        'dashicons-desktop',
        3
    );
    
    add_submenu_page(
        'nvacore-terminal',
        'Theme Dashboard',
        'Dashboard',
        'manage_options',
        'nvacore-terminal',
        'nvacore_admin_dashboard'
    );
    
    add_submenu_page(
        'nvacore-terminal',
        'Quick Setup',
        'Quick Setup',
        'manage_options',
        'nvacore-setup',
        'nvacore_admin_setup'
    );
    
    add_submenu_page(
        'nvacore-terminal',
        'Import/Export',
        'Import/Export',
        'manage_options',
        'nvacore-import-export',
        'nvacore_admin_import_export'
    );
    
    add_submenu_page(
        'nvacore-terminal',
        'System Status',
        'System Status',
        'manage_options',
        'nvacore-status',
        'nvacore_admin_status'
    );
}
add_action('admin_menu', 'nvacore_admin_menu');

/**
 * Admin Dashboard Page
 */
function nvacore_admin_dashboard() {
    ?>
    <div class="wrap nvacore-admin">
        <h1>N°VACORE Terminal Dashboard</h1>
        
        <div class="nvacore-admin-grid">
            
            <!-- Overview Cards -->
            <div class="nvacore-card">
                <h2>Content Overview</h2>
                <?php
                $artwork_count = wp_count_posts('artwork')->publish;
                $guestbook_count = wp_count_posts('guestbook_entry')->publish;
                $log_count = wp_count_posts('log_entry')->publish;
                $music_count = wp_count_posts('music_track')->publish;
                ?>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $artwork_count; ?></div>
                        <div class="stat-label">Artworks</div>
                        <a href="<?php echo admin_url('edit.php?post_type=artwork'); ?>" class="stat-link">Manage</a>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $guestbook_count; ?></div>
                        <div class="stat-label">Guestbook Entries</div>
                        <a href="<?php echo admin_url('edit.php?post_type=guestbook_entry'); ?>" class="stat-link">Manage</a>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $log_count; ?></div>
                        <div class="stat-label">Log Entries</div>
                        <a href="<?php echo admin_url('edit.php?post_type=log_entry'); ?>" class="stat-link">Manage</a>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $music_count; ?></div>
                        <div class="stat-label">Music Tracks</div>
                        <a href="<?php echo admin_url('edit.php?post_type=music_track'); ?>" class="stat-link">Manage</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="nvacore-card">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=artwork'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-art"></span> Add Artwork
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=log_entry'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-edit"></span> Add Log Entry
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=music_track'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-format-audio"></span> Add Music Track
                    </a>
                    <a href="<?php echo admin_url('customize.php'); ?>" class="button button-secondary">
                        <span class="dashicons dashicons-admin-customizer"></span> Customize Theme
                    </a>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="nvacore-card">
                <h2>Recent Activity</h2>
                <?php
                $recent_posts = get_posts(array(
                    'post_type' => array('artwork', 'guestbook_entry', 'log_entry', 'music_track'),
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                ?>
                
                <?php if ($recent_posts) : ?>
                <ul class="recent-activity">
                    <?php foreach ($recent_posts as $post) : ?>
                    <li class="activity-item">
                        <span class="activity-icon">
                            <?php
                            switch ($post->post_type) {
                                case 'artwork': echo '✿'; break;
                                case 'guestbook_entry': echo '✉'; break;
                                case 'log_entry': echo '✎'; break;
                                case 'music_track': echo '♫'; break;
                                default: echo '📄';
                            }
                            ?>
                        </span>
                        <div class="activity-content">
                            <a href="<?php echo get_edit_post_link($post->ID); ?>">
                                <?php echo esc_html($post->post_title); ?>
                            </a>
                            <small><?php echo get_post_type_object($post->post_type)->labels->singular_name; ?> • <?php echo human_time_diff(strtotime($post->post_date)); ?> ago</small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else : ?>
                <p>No recent activity.</p>
                <?php endif; ?>
            </div>
            
            <!-- System Information -->
            <div class="nvacore-card">
                <h2>System Information</h2>
                <table class="system-info">
                    <tr>
                        <td>Theme Version:</td>
                        <td><?php echo wp_get_theme()->get('Version'); ?></td>
                    </tr>
                    <tr>
                        <td>WordPress Version:</td>
                        <td><?php echo get_bloginfo('version'); ?></td>
                    </tr>
                    <tr>
                        <td>PHP Version:</td>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td>Active Theme:</td>
                        <td><?php echo wp_get_theme()->get('Name'); ?></td>
                    </tr>
                    <tr>
                        <td>Debug Mode:</td>
                        <td><?php echo get_theme_mod('nvacore_enable_debug', false) ? 'Enabled' : 'Disabled'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Theme Status -->
        <div class="nvacore-card full-width">
            <h2>Theme Configuration Status</h2>
            <?php
            $required_pages = array('About', 'Contact', 'Commissions', 'Discord', 'Ultracore');
            $status_items = array();
            
            foreach ($required_pages as $page_title) {
                $page = get_page_by_title($page_title);
                $status_items[] = array(
                    'name' => $page_title . ' Page',
                    'status' => $page ? 'complete' : 'incomplete',
                    'message' => $page ? 'Created' : 'Missing',
                );
            }
            
            // Check for uploaded artwork
            $has_artwork = wp_count_posts('artwork')->publish > 0;
            $status_items[] = array(
                'name' => 'Artwork Gallery',
                'status' => $has_artwork ? 'complete' : 'incomplete',
                'message' => $has_artwork ? 'Has content' : 'No artwork uploaded',
            );
            
            // Check for music tracks
            $has_music = wp_count_posts('music_track')->publish > 0;
            $status_items[] = array(
                'name' => 'Music Playlist',
                'status' => $has_music ? 'complete' : 'incomplete',
                'message' => $has_music ? 'Has tracks' : 'No music tracks added',
            );
            ?>
            
            <div class="status-grid">
                <?php foreach ($status_items as $item) : ?>
                <div class="status-item <?php echo $item['status']; ?>">
                    <span class="status-icon">
                        <?php echo $item['status'] === 'complete' ? '✓' : '⚠'; ?>
                    </span>
                    <div class="status-content">
                        <div class="status-name"><?php echo $item['name']; ?></div>
                        <div class="status-message"><?php echo $item['message']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <style>
    .nvacore-admin {
        font-family: 'Share Tech Mono', monospace;
    }
    
    .nvacore-admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .nvacore-card {
        background: #fff;
        border: 1px solid #c3c4c7;
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    
    .nvacore-card.full-width {
        grid-column: 1 / -1;
    }
    
    .nvacore-card h2 {
        margin-top: 0;
        color: #c4a0ff;
        border-bottom: 2px solid #c4a0ff;
        padding-bottom: 10px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 15px;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px;
        background: #f6f7f7;
        border-radius: 4px;
    }
    
    .stat-number {
        font-size: 2em;
        font-weight: bold;
        color: #c4a0ff;
    }
    
    .stat-label {
        font-size: 0.9em;
        margin: 5px 0;
    }
    
    .stat-link {
        color: #c4a0ff;
        text-decoration: none;
        font-size: 0.8em;
    }
    
    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .quick-actions .button {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .recent-activity {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        font-size: 1.2em;
        width: 20px;
        text-align: center;
    }
    
    .activity-content small {
        display: block;
        color: #666;
        margin-top: 2px;
    }
    
    .system-info {
        width: 100%;
    }
    
    .system-info td {
        padding: 5px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .system-info td:first-child {
        font-weight: bold;
        width: 40%;
    }
    
    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .status-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .status-item.complete {
        background: #d4edda;
        border-color: #c3e6cb;
    }
    
    .status-item.incomplete {
        background: #fff3cd;
        border-color: #ffeaa7;
    }
    
    .status-icon {
        font-size: 1.2em;
        font-weight: bold;
    }
    
    .status-item.complete .status-icon {
        color: #155724;
    }
    
    .status-item.incomplete .status-icon {
        color: #856404;
    }
    
    .status-name {
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .status-message {
        font-size: 0.9em;
        opacity: 0.8;
    }
    </style>
    <?php
}

/**
 * Quick Setup Page
 */
function nvacore_admin_setup() {
    if (isset($_POST['create_pages']) && wp_verify_nonce($_POST['_wpnonce'], 'nvacore_setup')) {
        nvacore_create_default_pages();
        echo '<div class="notice notice-success"><p>Default pages created successfully!</p></div>';
    }
    
    if (isset($_POST['create_sample_content']) && wp_verify_nonce($_POST['_wpnonce'], 'nvacore_setup')) {
        nvacore_create_sample_content();
        echo '<div class="notice notice-success"><p>Sample content created successfully!</p></div>';
    }
    ?>
    
    <div class="wrap nvacore-admin">
        <h1>N°VACORE Terminal Setup</h1>
        
        <div class="nvacore-card">
            <h2>Quick Setup Wizard</h2>
            <p>Use these tools to quickly set up your N°VACORE terminal theme with default content.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('nvacore_setup'); ?>
                
                <h3>1. Create Default Pages</h3>
                <p>Creates the essential pages (About, Contact, Commissions, etc.) with default content.</p>
                <button type="submit" name="create_pages" class="button button-primary">Create Default Pages</button>
                
                <hr>
                
                <h3>2. Add Sample Content</h3>
                <p>Adds sample artwork, log entries, and music tracks to demonstrate the theme functionality.</p>
                <button type="submit" name="create_sample_content" class="button button-secondary">Create Sample Content</button>
            </form>
        </div>
        
        <div class="nvacore-card">
            <h2>Manual Setup Checklist</h2>
            <div class="setup-checklist">
                <label>
                    <input type="checkbox" disabled> Install theme (completed)
                </label>
                <label>
                    <input type="checkbox"> Create default pages (About, Contact, etc.)
                </label>
                <label>
                    <input type="checkbox"> Add your artwork to the gallery
                </label>
                <label>
                    <input type="checkbox"> Configure music playlist
                </label>
                <label>
                    <input type="checkbox"> Update contact information
                </label>
                <label>
                    <input type="checkbox"> Customize theme colors
                </label>
                <label>
                    <input type="checkbox"> Test guestbook functionality
                </label>
                <label>
                    <input type="checkbox"> Upload video backgrounds (optional)
                </label>
            </div>
        </div>
    </div>
    
    <style>
    .setup-checklist {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .setup-checklist label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        background: #f9f9f9;
        border-radius: 4px;
    }
    </style>
    <?php
}

/**
 * Import/Export Page
 */
function nvacore_admin_import_export() {
    ?>
    <div class="wrap nvacore-admin">
        <h1>Import/Export Data</h1>
        
        <div class="nvacore-admin-grid">
            <div class="nvacore-card">
                <h2>Export Theme Data</h2>
                <p>Export your theme settings, content, and configuration for backup or transfer.</p>
                <a href="<?php echo admin_url('admin-ajax.php?action=nvacore_export_data'); ?>" class="button button-primary">
                    Export Data
                </a>
            </div>
            
            <div class="nvacore-card">
                <h2>Clear Cache</h2>
                <p>Clear theme-specific caches to refresh dynamic content.</p>
                <button type="button" id="clear-cache" class="button button-secondary">Clear Cache</button>
                <div id="cache-status"></div>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('clear-cache').addEventListener('click', function() {
        const button = this;
        const status = document.getElementById('cache-status');
        
        button.disabled = true;
        button.textContent = 'Clearing...';
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=nvacore_clear_cache'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                status.innerHTML = '<p style="color: green;">Cache cleared successfully!</p>';
            } else {
                status.innerHTML = '<p style="color: red;">Failed to clear cache.</p>';
            }
        })
        .finally(() => {
            button.disabled = false;
            button.textContent = 'Clear Cache';
            setTimeout(() => {
                status.innerHTML = '';
            }, 3000);
        });
    });
    </script>
    <?php
}

/**
 * System Status Page
 */
function nvacore_admin_status() {
    ?>
    <div class="wrap nvacore-admin">
        <h1>System Status</h1>
        
        <div class="nvacore-card">
            <h2>Debug Information</h2>
            <button type="button" id="get-debug-info" class="button button-primary">Get Debug Info</button>
            <pre id="debug-output" style="background: #f0f0f0; padding: 15px; margin-top: 15px; display: none;"></pre>
        </div>
    </div>
    
    <script>
    document.getElementById('get-debug-info').addEventListener('click', function() {
        const output = document.getElementById('debug-output');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=nvacore_debug_info'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                output.textContent = JSON.stringify(data.data, null, 2);
                output.style.display = 'block';
            }
        });
    });
    </script>
    <?php
}

/**
 * Create sample content for demonstration
 */
function nvacore_create_sample_content() {
    // Create sample artwork
    $sample_artwork = array(
        array(
            'title' => 'Digital Glitch Study #1',
            'content' => 'An exploration of digital corruption and data loss as artistic expression.',
            'date' => '03/15/25',
            'color' => '#ff6b8a',
        ),
        array(
            'title' => 'Neon Terminal Interface',
            'content' => 'Retro-futuristic terminal design with cyberpunk aesthetics.',
            'date' => '03/10/25',
            'color' => '#00ffaa',
        ),
    );
    
    foreach ($sample_artwork as $artwork) {
        wp_insert_post(array(
            'post_type' => 'artwork',
            'post_title' => $artwork['title'],
            'post_content' => $artwork['content'],
            'post_status' => 'publish',
            'meta_input' => array(
                '_artwork_date' => $artwork['date'],
                '_fallback_color' => $artwork['color'],
            ),
        ));
    }
    
    // Create sample log entries
    $sample_logs = array(
        array(
            'content' => 'Started work on new glitch art series. Experimenting with pixel sorting algorithms and audio effects applied to image data.',
            'date' => '03/20/25',
        ),
        array(
            'content' => 'Updated the terminal interface with improved ASCII art scaling for mobile devices. Much better rendering now.',
            'date' => '03/18/25',
        ),
    );
    
    foreach ($sample_logs as $log) {
        wp_insert_post(array(
            'post_type' => 'log_entry',
            'post_title' => 'Log Entry - ' . $log['date'],
            'post_content' => $log['content'],
            'post_status' => 'publish',
            'meta_input' => array(
                '_log_date' => $log['date'],
            ),
        ));
    }
    
    // Create sample music tracks
    $sample_music = array(
        array(
            'title' => 'From Me To You',
            'artist' => 'Quadeca',
            'url' => 'https://open.spotify.com/track/example1',
            'order' => 1,
        ),
        array(
            'title' => 'Synergy',
            'artist' => 'Dance Gavin Dance',
            'url' => 'https://open.spotify.com/track/example2',
            'order' => 2,
        ),
    );
    
    foreach ($sample_music as $track) {
        wp_insert_post(array(
            'post_type' => 'music_track',
            'post_title' => $track['title'],
            'post_status' => 'publish',
            'meta_input' => array(
                '_track_artist' => $track['artist'],
                '_track_url' => $track['url'],
                '_track_order' => $track['order'],
            ),
        ));
    }
}