<!-- Window Content - Guestbook -->
<div class="file-content" id="guestbook-content">
    <div class="file-content-header">
        <div class="file-content-title">✉ /guestbook</div>
        <div class="window-controls">
            <div class="window-control window-minimize">─</div>
            <div class="window-control window-maximize">□</div>
            <div class="window-control window-close">×</div>
        </div>
    </div>
    <div class="content-section">
        <div class="ascii-container">
            <pre class="ascii-art">
 ██████╗ ██╗   ██╗███████╗███████╗████████╗██████╗  ██████╗  ██████╗ ██╗  ██╗
██╔════╝ ██║   ██║██╔════╝██╔════╝╚══██╔══╝██╔══██╗██╔═══██╗██╔═══██╗██║ ██╔╝
██║  ███╗██║   ██║█████╗  ███████╗   ██║   ██████╔╝██║   ██║██║   ██║█████╔╝ 
██║   ██║██║   ██║██╔══╝  ╚════██║   ██║   ██╔══██╗██║   ██║██║   ██║██╔═██╗ 
╚██████╔╝╚██████╔╝███████╗███████║   ██║   ██████╔╝╚██████╔╝╚██████╔╝██║  ██╗
 ╚═════╝  ╚═════╝ ╚══════╝╚══════╝   ╚═╝   ╚═════╝  ╚═════╝  ╚═════╝ ╚═╝  ╚═╝
            </pre>
        </div>

        <p style="text-align: center; margin-bottom: 30px; color: rgba(196, 160, 255, 0.8);">
            Drop a message and share your favorite track.
        </p>

        <div class="guestbook-form">
            <div class="form-row">
                <label for="guest-name">Display Name</label>
                <input type="text" id="guest-name" class="form-input" placeholder="What should we call you?" required>
            </div>
            <div class="form-row">
                <label for="guest-email">Email (Optional)</label>
                <input type="email" id="guest-email" class="form-input" placeholder="your@email.com">
            </div>
            <div class="form-row">
                <label for="guest-website">Website (Optional)</label>
                <input type="url" id="guest-website" class="form-input" placeholder="https://your-awesome-site.com">
            </div>
            <div class="form-row">
                <label for="guest-music">Share a Song (Optional)</label>
                <div class="music-input-container">
                    <input type="url" id="guest-music" class="form-input" placeholder="Drop a Spotify, YouTube, or SoundCloud link">
                    <button id="clear-music-preview" class="clear-music-btn" style="display: none;">Clear</button>
                </div>
                <div id="music-preview" class="music-preview" style="display: none;"></div>
            </div>
            <div class="form-row">
                <label for="guest-message">Your Message</label>
                <textarea id="guest-message" class="form-textarea" rows="4" placeholder="Share your thoughts, drop a hello, or leave some good vibes..." required></textarea>
            </div>
            <div class="form-row form-submit">
                <button id="submit-guestbook" class="submit-button">Sign Guestbook ✨</button>
            </div>
        </div>

        <div class="guestbook-entries">
            <h3>Recent Messages</h3>
            <div id="guestbook-entries-container">
                <?php
                // Load recent guestbook entries
                $guestbook_entries = get_posts(array(
                    'post_type' => 'guestbook_entry',
                    'posts_per_page' => 10,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));

                if ($guestbook_entries) {
                    foreach ($guestbook_entries as $entry) {
                        $guest_name = get_post_meta($entry->ID, '_guest_name', true);
                        $guest_email = get_post_meta($entry->ID, '_guest_email', true);
                        $guest_website = get_post_meta($entry->ID, '_guest_website', true);
                        $entry_date = get_post_meta($entry->ID, '_entry_date', true);
                        $music_url = get_post_meta($entry->ID, '_music_url', true);
                        $music_title = get_post_meta($entry->ID, '_music_title', true);
                        $music_artist = get_post_meta($entry->ID, '_music_artist', true);
                        
                        echo '<div class="guestbook-entry" data-id="' . esc_attr($entry->ID) . '">';
                        echo '<div class="entry-header">';
                        echo '<span class="entry-name">' . esc_html($guest_name) . '</span>';
                        echo '<span class="entry-date">' . esc_html($entry_date) . '</span>';
                        echo '</div>';
                        
                        echo '<div class="entry-message">' . wp_kses_post($entry->post_content) . '</div>';
                        
                        if ($guest_email) {
                            echo '<div class="entry-email">' . esc_html($guest_email) . '</div>';
                        }
                        
                        if ($guest_website) {
                            echo '<div class="entry-website"><a href="' . esc_url($guest_website) . '" target="_blank">' . esc_html($guest_website) . '</a></div>';
                        }
                        
                        // Music embed if available
                        if ($music_url && $music_title) {
                            echo '<div class="entry-music">';
                            echo '<div class="music-embed">';
                            echo '<div class="music-embed-info">';
                            echo '<div class="music-embed-title">' . esc_html($music_title) . '</div>';
                            if ($music_artist) {
                                echo '<div class="music-embed-artist">' . esc_html($music_artist) . '</div>';
                            }
                            echo '<div class="music-embed-source">' . esc_html(parse_url($music_url, PHP_URL_HOST)) . '</div>';
                            echo '</div>';
                            echo '<div class="music-embed-play">';
                            echo '<a href="' . esc_url($music_url) . '" target="_blank" rel="noopener noreferrer">Listen</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        // Admin controls for administrators
                        if (current_user_can('manage_options')) {
                            echo '<div class="entry-admin-controls">';
                            echo '<button type="button" class="admin-reply-button" data-entry-id="' . esc_attr($entry->ID) . '">Reply as N°VACORE</button>';
                            echo '<button type="button" class="admin-delete-button" data-entry-id="' . esc_attr($entry->ID) . '">Delete Entry</button>';
                            echo '</div>';
                        }
                        
                        // Replies container
                        echo '<div class="entry-replies-container" id="replies-' . esc_attr($entry->ID) . '">';
                        
                        // Load replies (using WordPress comments system)
                        $replies = get_comments(array(
                            'post_id' => $entry->ID,
                            'status' => 'approve',
                            'order' => 'ASC',
                        ));
                        
                        if ($replies) {
                            echo '<div class="entry-replies">';
                            foreach ($replies as $reply) {
                                $is_admin = get_comment_meta($reply->comment_ID, '_is_admin_reply', true);
                                $reply_class = $is_admin ? 'entry-reply admin-reply' : 'entry-reply';
                                
                                echo '<div class="' . esc_attr($reply_class) . '" data-reply-id="' . esc_attr($reply->comment_ID) . '">';
                                echo '<div class="reply-header">';
                                
                                if ($is_admin) {
                                    echo '<span class="reply-name"><span class="admin-badge">N°VACORE</span></span>';
                                } else {
                                    echo '<span class="reply-name">' . esc_html($reply->comment_author) . '</span>';
                                }
                                
                                echo '<span class="reply-date">' . esc_html(date('m/d/y', strtotime($reply->comment_date))) . '</span>';
                                echo '</div>';
                                echo '<div class="reply-message">' . wp_kses_post($reply->comment_content) . '</div>';
                                
                                // Admin delete control for replies
                                if (current_user_can('manage_options')) {
                                    echo '<div class="reply-admin-controls">';
                                    echo '<button type="button" class="reply-delete-button" data-reply-id="' . esc_attr($reply->comment_ID) . '" data-entry-id="' . esc_attr($entry->ID) . '">Delete</button>';
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        
                        echo '</div>'; // Close replies container
                        echo '</div>'; // Close guestbook entry
                    }
                } else {
                    echo '<p>No entries yet. Be the first to sign the guestbook!</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>