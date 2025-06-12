<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    
    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<!-- Video Background -->
<div class="video-background">
    <video id="boot-video" class="background-video" muted playsinline></video>
    <video id="loop-video" class="background-video" muted playsinline loop></video>
</div>

<!-- Overlay -->
<div class="overlay"></div>

<!-- Main Terminal -->
<div class="terminal">
    <div class="terminal-header">
        <div class="header-left">
            <div class="terminal-title">ULTRAC<span class="ultraconsole-degree">°</span>NS-LE</div>
        </div>
        <div class="header-right">
            <div class="terminal-subtitle">C°REOS β.20.2.5</div>
            <div id="theme-toggle" class="theme-toggle" title="Toggle light/dark mode">☾</div>
        </div>
    </div>
    
    <div class="terminal-body">
        <!-- Boot Content (will be hidden after boot) -->
        <div class="boot-content" id="boot-content">
            <div class="boot-header">ULTRAC°REOS SYSTEM BOOTUP</div>
            <div class="ascii-circle-container">
                <pre class="ascii-circle" id="loading-circle"></pre>
            </div>
            <div class="boot-message" id="boot-message"></div>
        </div>

        <!-- Directory Header -->
        <div class="directory-header" style="display: none;" id="directory-header">
            <div class="directory-title">DIRECT°RY</div>
            <div class="directory-path">
                <span class="directory-path-icon">⌘</span>
                <span>root:/system/user/n°vacore/<?php echo get_post_type(); ?>/</span>
            </div>
            <div class="directory-info">
                <span>Single Entry</span>
                <span>Last updated: <?php echo get_the_modified_date('m/d/y'); ?></span>
            </div>
        </div>

        <!-- Filesystem Content -->
        <div class="filesystem active" style="display: none;" id="filesystem-content">
            
            <!-- Back to Directory Button -->
            <div class="directory back-to-directory" data-file="home" onclick="window.location.href='<?php echo home_url(); ?>'">
                <div class="type-icon">⬅</div>
                <div class="file-name">../back_to_directory</div>
            </div>
            
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <!-- Single Post Window -->
            <div class="file-content active" id="single-post-content">
                <div class="file-content-header">
                    <div class="file-content-title">
                        <?php
                        switch (get_post_type()) {
                            case 'artwork':
                                echo '✿ ';
                                break;
                            case 'guestbook_entry':
                                echo '✉ ';
                                break;
                            case 'log_entry':
                                echo '✎ ';
                                break;
                            case 'music_track':
                                echo '♫ ';
                                break;
                            default:
                                echo '📄 ';
                        }
                        echo esc_html(get_the_title());
                        ?>
                    </div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close" onclick="window.history.back()">×</div>
                    </div>
                </div>
                
                <div class="content-section">
                    
                    <?php if (get_post_type() === 'artwork') : ?>
                        <!-- Artwork Display -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="artwork-display">
                                <?php 
                                $full_image = wp_get_attachment_image_url(get_post_thumbnail_id(), 'artwork-full');
                                $fallback_color = get_post_meta(get_the_ID(), '_fallback_color', true) ?: '#333333';
                                ?>
                                <img src="<?php echo esc_url($full_image); ?>" 
                                     alt="<?php echo esc_attr(get_the_title()); ?>" 
                                     class="artwork-fullimage"
                                     style="background-color: <?php echo esc_attr($fallback_color); ?>;">
                            </div>
                        <?php endif; ?>
                        
                        <div class="artwork-meta">
                            <div class="artwork-meta-item">
                                <span class="artwork-meta-label">Title:</span>
                                <span><?php the_title(); ?></span>
                            </div>
                            <?php $artwork_date = get_post_meta(get_the_ID(), '_artwork_date', true); ?>
                            <?php if ($artwork_date) : ?>
                            <div class="artwork-meta-item">
                                <span class="artwork-meta-label">Date:</span>
                                <span><?php echo esc_html($artwork_date); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (get_the_content()) : ?>
                            <div class="artwork-meta-item">
                                <span class="artwork-meta-label">Description:</span>
                                <div><?php the_content(); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php elseif (get_post_type() === 'guestbook_entry') : ?>
                        <!-- Guestbook Entry Display -->
                        <div class="guestbook-entry-single">
                            <div class="entry-header">
                                <span class="entry-name"><?php echo esc_html(get_post_meta(get_the_ID(), '_guest_name', true)); ?></span>
                                <span class="entry-date"><?php echo esc_html(get_post_meta(get_the_ID(), '_entry_date', true)); ?></span>
                            </div>
                            
                            <div class="entry-message">
                                <?php the_content(); ?>
                            </div>
                            
                            <?php 
                            $guest_email = get_post_meta(get_the_ID(), '_guest_email', true);
                            $guest_website = get_post_meta(get_the_ID(), '_guest_website', true);
                            $music_url = get_post_meta(get_the_ID(), '_music_url', true);
                            $music_title = get_post_meta(get_the_ID(), '_music_title', true);
                            $music_artist = get_post_meta(get_the_ID(), '_music_artist', true);
                            ?>
                            
                            <?php if ($guest_email) : ?>
                            <div class="entry-email"><?php echo esc_html($guest_email); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($guest_website) : ?>
                            <div class="entry-website">
                                <a href="<?php echo esc_url($guest_website); ?>" target="_blank">
                                    <?php echo esc_html($guest_website); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($music_url && $music_title) : ?>
                            <div class="entry-music">
                                <div class="music-embed">
                                    <div class="music-embed-info">
                                        <div class="music-embed-title"><?php echo esc_html($music_title); ?></div>
                                        <?php if ($music_artist) : ?>
                                        <div class="music-embed-artist"><?php echo esc_html($music_artist); ?></div>
                                        <?php endif; ?>
                                        <div class="music-embed-source"><?php echo esc_html(parse_url($music_url, PHP_URL_HOST)); ?></div>
                                    </div>
                                    <div class="music-embed-play">
                                        <a href="<?php echo esc_url($music_url); ?>" target="_blank" rel="noopener noreferrer">Listen</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Replies Section -->
                            <?php
                            $replies = get_comments(array(
                                'post_id' => get_the_ID(),
                                'status' => 'approve',
                                'order' => 'ASC',
                            ));
                            ?>
                            
                            <?php if ($replies) : ?>
                            <div class="entry-replies">
                                <h4>Replies</h4>
                                <?php foreach ($replies as $reply) : ?>
                                    <?php $is_admin = get_comment_meta($reply->comment_ID, '_is_admin_reply', true); ?>
                                    <div class="entry-reply <?php echo $is_admin ? 'admin-reply' : ''; ?>">
                                        <div class="reply-header">
                                            <?php if ($is_admin) : ?>
                                                <span class="reply-name"><span class="admin-badge">N°VACORE</span></span>
                                            <?php else : ?>
                                                <span class="reply-name"><?php echo esc_html($reply->comment_author); ?></span>
                                            <?php endif; ?>
                                            <span class="reply-date"><?php echo esc_html(date('m/d/y', strtotime($reply->comment_date))); ?></span>
                                        </div>
                                        <div class="reply-message"><?php echo wp_kses_post($reply->comment_content); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php elseif (get_post_type() === 'log_entry') : ?>
                        <!-- Log Entry Display -->
                        <div class="ascii-container">
                            <pre class="ascii-art">
██╗      ██████╗  ██████╗
██║     ██╔═══██╗██╔════╝
██║     ██║   ██║██║  ███╗
██║     ██║   ██║██║   ██║
███████╗╚██████╔╝╚██████╔╝
╚══════╝ ╚═════╝  ╚═════╝
                            </pre>
                        </div>
                        
                        <div class="log-entry">
                            <div class="log-date">
                                <?php echo esc_html(get_post_meta(get_the_ID(), '_log_date', true) ?: date('m/d/y', strtotime(get_the_date()))); ?>
                            </div>
                            <div class="log-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        
                    <?php elseif (get_post_type() === 'music_track') : ?>
                        <!-- Music Track Display -->
                        <div class="ascii-container">
                            <pre class="ascii-art">
♫ ♪ ♫ ♪ ♫ ♪ ♫ ♪ ♫ ♪ ♫ ♪
                            </pre>
                        </div>
                        
                        <div class="music-track-single">
                            <h2><?php the_title(); ?></h2>
                            <?php 
                            $track_artist = get_post_meta(get_the_ID(), '_track_artist', true);
                            $track_url = get_post_meta(get_the_ID(), '_track_url', true);
                            $track_order = get_post_meta(get_the_ID(), '_track_order', true);
                            ?>
                            
                            <?php if ($track_artist) : ?>
                            <p><strong>Artist:</strong> <?php echo esc_html($track_artist); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($track_order) : ?>
                            <p><strong>Playlist Position:</strong> <?php echo esc_html($track_order); ?></p>
                            <?php endif; ?>
                            
                            <?php if (get_the_content()) : ?>
                            <div class="track-description">
                                <h4>About this track:</h4>
                                <?php the_content(); ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($track_url) : ?>
                            <div class="track-player">
                                <a href="<?php echo esc_url($track_url); ?>" target="_blank" class="play-track-button">
                                    ▶ Play Track
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php else : ?>
                        <!-- Default Post Display -->
                        <div class="single-post-content">
                            <h1><?php the_title(); ?></h1>
                            
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date('m/d/y'); ?></span>
                                <?php if (get_the_modified_date() !== get_the_date()) : ?>
                                <span class="post-modified">Updated: <?php echo get_the_modified_date('m/d/y'); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="post-content">
                                <?php the_content(); ?>
                            </div>
                            
                            <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php endif; ?>
                    
                    <!-- Admin Controls for logged-in administrators -->
                    <?php if (current_user_can('edit_post', get_the_ID())) : ?>
                    <div class="admin-controls-single">
                        <a href="<?php echo get_edit_post_link(); ?>" class="edit-post-link">Edit this <?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></a>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <?php endwhile; endif; ?>
            
        </div>
        
        <div class="prompt" style="margin-top: 20px; border-top: 1px solid var(--accent-color); padding-top: 10px;">
            <div class="prompt-symbol" style="font-weight: bold; font-size: 18px;">➤</div>
            <div class="command" style="font-weight: bold; font-size: 18px;">_</div>
            <div class="cursor"></div>
        </div>
    </div>
</div>

<!-- Notification element -->
<div id="notification" class="notification">Notification message</div>

<!-- Context menu -->
<div id="context-menu" class="context-menu">
    <div class="context-item" id="ctx-refresh">Refresh Directory</div>
    <div class="context-item" id="ctx-open">Open File</div>
    <div class="context-separator"></div>
    <div class="context-item" id="ctx-theme">Toggle Theme</div>
</div>

<!-- Taskbar with container for items -->
<div class="taskbar">
    <div class="taskbar-items">
        <div class="taskbar-item single-post" data-window="single-post-content" data-order="1">
            <?php
            switch (get_post_type()) {
                case 'artwork': echo '✿'; break;
                case 'guestbook_entry': echo '✉'; break;
                case 'log_entry': echo '✎'; break;
                case 'music_track': echo '♫'; break;
                default: echo '📄';
            }
            ?>
        </div>
    </div>
</div>

<!-- Custom cursor elements -->
<div class="custom-cursor"></div>
<div class="click-animation"></div>
<div class="cursor-ray"></div>
<div id="real-mouse-position"></div>

<?php wp_footer(); ?>

<!-- WordPress Admin Bar Compatibility -->
<?php if (is_admin_bar_showing()): ?>
<style>
    body.admin-bar .terminal {
        margin-top: 32px;
    }
    @media screen and (max-width: 782px) {
        body.admin-bar .terminal {
            margin-top: 46px;
        }
    }
</style>
<?php endif; ?>

</body>
</html>