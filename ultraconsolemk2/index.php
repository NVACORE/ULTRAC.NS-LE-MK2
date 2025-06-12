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
    
    <!-- Music Drawer / Jukebox -->
    <div class="music-drawer" id="music-drawer">
        <div class="music-drawer-tab" id="music-drawer-tab">
            <div class="tab-text">JUKEB°X</div>
        </div>
        <div class="music-drawer-content">
            <div class="music-drawer-header">
                <div class="music-drawer-title">JUKEB°X.WAV</div>
            </div>

            <div class="now-playing-display">
                <div class="now-playing-label">NOW PLAYING:</div>
                <div class="now-playing-title" id="drawer-song-title">Loading...</div>
            </div>

            <div class="drawer-progress">
                <div class="drawer-progress-bar">
                    <div class="drawer-progress-inner" id="drawer-progress-inner"></div>
                </div>
                <div class="drawer-time">
                    <span id="drawer-current-time">0:00</span> / <span id="drawer-total-time">0:00</span>
                </div>
            </div>

            <div class="drawer-controls">
                <button id="drawer-prev-btn" class="drawer-btn">⏮</button>
                <button id="drawer-play-btn" class="drawer-btn play-btn">▶</button>
                <button id="drawer-next-btn" class="drawer-btn">⏭</button>
                <button id="drawer-mute-btn" class="drawer-btn">
                    <span id="drawer-volume-icon">🔊</span>
                </button>
            </div>

            <div class="drawer-playlist-wrapper">
                <div class="drawer-playlist-header">TRACKLIST</div>
                <div class="drawer-playlist" id="drawer-playlist">
                    <!-- Playlist items will be added by JavaScript -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="terminal-body">
        <!-- Boot Content -->
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
                <span>root:/system/user/n°vacore/</span>
            </div>
            <div class="directory-info">
                <span>9 Files</span>
                <span>Last updated: <?php echo date('m/d/y'); ?></span>
            </div>
        </div>
        
        <!-- Filesystem Content -->
        <div class="filesystem active" style="display: none;" id="filesystem-content">
            <!-- Directory Items -->
            <div class="directory" data-file="about">
                <div class="type-icon">✧</div>
                <div class="file-name">/about.html</div>
            </div>
            
            <div class="directory" data-file="interests">
                <div class="type-icon">★</div>
                <div class="file-name">/interests</div>
            </div>
            
            <div class="directory" data-file="art">
                <div class="type-icon">✿</div>
                <div class="file-name">/artwork</div>
            </div>
            
            <div class="directory" data-file="log">
                <div class="type-icon">✎</div>
                <div class="file-name">/l°g</div>
            </div>
            
            <div class="directory" data-file="commissions">
                <div class="type-icon">❂</div>
                <div class="file-name">/commissions</div>
            </div>
            
            <div class="directory" data-file="discord">
                <div class="type-icon">♫</div>
                <div class="file-name">/discord</div>
            </div>
            
            <div class="directory" data-file="guestbook">
                <div class="type-icon">✉</div>
                <div class="file-name">/guestbook</div>
            </div>
            
            <div class="directory" data-file="contact">
                <div class="type-icon">⚙</div>
                <div class="file-name">/contact</div>
            </div>
            
            <div class="directory" data-file="ultracore">
                <div class="type-icon">✦</div>
                <div class="file-name rainbow-text">/ultrac°re</div>
            </div>
            
            <!-- Window Content - About -->
            <div class="file-content" id="about-content">
                <div class="file-content-header">
                    <div class="file-content-title">✧ /about.html</div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close">×</div>
                    </div>
                </div>
                <div class="content-section">
                    <div class="ascii-container">
                        <pre class="ascii-art">
 █████╗ ██████╗  ██████╗ ██╗   ██╗████████╗
██╔══██╗██╔══██╗██╔═══██╗██║   ██║╚══██╔══╝
███████║██████╔╝██║   ██║██║   ██║   ██║   
██╔══██║██╔══██╗██║   ██║██║   ██║   ██║   
██║  ██║██████╔╝╚██████╔╝╚██████╔╝   ██║   
╚═╝  ╚═╝╚═════╝  ╚═════╝  ╚═════╝    ╚═╝   
                        </pre>
                    </div>
                    <?php
                    // Get about page content
                    $about_page = get_page_by_title('About');
                    if ($about_page) {
                        echo apply_filters('the_content', $about_page->post_content);
                    } else {
                        echo '<p>Welcome to N°VACORE\'s digital space!</p>
                              <p>This is my personal directory for art, projects, and various digital experiments.</p>
                              <p>Browse through my work, check out my Discord server, or see my latest L°G entries.</p>
                              <p>Feel free to explore and discover more about my work and interests.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- Window Content - Interests -->
            <div class="file-content" id="interests-content">
                <div class="file-content-header">
                    <div class="file-content-title">★ /interests</div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close">×</div>
                    </div>
                </div>
                <div class="content-section">
                    <div class="ascii-container">
                        <pre class="ascii-art">
██╗███╗   ██╗████████╗███████╗██████╗ ███████╗███████╗████████╗███████╗
██║████╗  ██║╚══██╔══╝██╔════╝██╔══██╗██╔════╝██╔════╝╚══██╔══╝██╔════╝
██║██╔██╗ ██║   ██║   █████╗  ██████╔╝█████╗  ███████╗   ██║   ███████╗
██║██║╚██╗██║   ██║   ██╔══╝  ██╔══██╗██╔══╝  ╚════██║   ██║   ╚════██║
██║██║ ╚████║   ██║   ███████╗██║  ██║███████╗███████║   ██║   ███████║
╚═╝╚═╝  ╚═══╝   ╚═╝   ╚══════╝╚═╝  ╚═╝╚══════╝╚══════╝   ╚═╝   ╚══════╝
                        </pre>
                    </div>
                    
                    <!-- Back Navigation Button -->
                    <div class="interests-back-button" style="display: none;">← Back</div>
                    
                    <!-- Main Buttons View -->
                    <div class="interests-buttons">
                        <h2 class="interests-headline">My Interests</h2>
                        <div class="interests-grid">
                            <!-- Music Button -->
                            <div class="interests-button music-color" data-category="music">
                                <div class="button-icon">♫</div>
                                <div class="button-title">MUSIC</div>
                            </div>
                            
                            <!-- Games Button -->
                            <div class="interests-button games-color" data-category="games">
                                <div class="button-icon">▶</div>
                                <div class="button-title">GAMES</div>
                            </div>
                            
                            <!-- Movies Button -->
                            <div class="interests-button movies-color" data-category="movies">
                                <div class="button-icon">🎬</div>
                                <div class="button-title">MOVIES</div>
                            </div>
                            
                            <!-- Urbex Button -->
                            <div class="interests-button urbex-color" data-category="urbex">
                                <div class="button-icon">⚠</div>
                                <div class="button-title">URBAN EXPLORATION</div>
                            </div>
                            
                            <!-- Digital Aesthetic Button -->
                            <div class="interests-button aesthetic-color" data-category="aesthetic">
                                <div class="button-icon">⚡</div>
                                <div class="button-title">DIGITAL AESTHETIC</div>
                            </div>
                        </div>
                    </div>
                    
                    <?php include get_template_directory() . '/template-parts/interests-categories.php'; ?>
                    
                </div>
            </div>
            
            <!-- Window Content - Art -->
            <div class="file-content" id="art-content">
                <div class="file-content-header">
                    <div class="file-content-title">✿ /artwork</div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close">×</div>
                    </div>
                </div>
                <div class="content-section">
                    <div class="ascii-container">
                        <pre class="ascii-art">
 █████╗ ██████╗ ████████╗██╗    ██╗ ██████╗ ██████╗ ██╗  ██╗
██╔══██╗██╔══██╗╚══██╔══╝██║    ██║██╔═══██╗██╔══██╗██║ ██╔╝
███████║██████╔╝   ██║   ██║ █╗ ██║██║   ██║██████╔╝█████╔╝ 
██╔══██║██╔══██╗   ██║   ██║███╗██║██║   ██║██╔══██╗██╔═██╗ 
██║  ██║██║  ██║   ██║   ╚███╔███╔╝╚██████╔╝██║  ██║██║  ██╗
╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝    ╚══╝╚══╝  ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝
                        </pre>
                    </div>
                    <p>Click on any artwork to view in detail.</p>
                    <div id="artwork-gallery" class="artwork-gallery">
                        <!-- Gallery items will be populated by JavaScript from WordPress data -->
                    </div>
                </div>
            </div>
            
            <!-- Window Content - Log -->
            <div class="file-content" id="log-content">
                <div class="file-content-header">
                    <div class="file-content-title">✎ /l°g</div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close">×</div>
                    </div>
                </div>
                <div class="content-section">
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
                    
                    <!-- Log entries will be populated by JavaScript from WordPress data -->
                    <div id="log-entries-container">
                        <?php
                        $log_entries = get_posts(array(
                            'post_type' => 'log_entry',
                            'posts_per_page' => -1,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ));
                        
                        foreach ($log_entries as $entry) {
                            $log_date = get_post_meta($entry->ID, '_log_date', true) ?: date('m/d/y', strtotime($entry->post_date));
                            echo '<div class="log-entry">';
                            echo '<div class="log-date">' . esc_html($log_date) . '</div>';
                            echo '<p>' . wp_kses_post($entry->post_content) . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Window Content - Commissions -->
            <div class="file-content" id="commissions-content">
                <div class="file-content-header">
                    <div class="file-content-title">❂ /commissions</div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close">×</div>
                    </div>
                </div>
                <div class="content-section">
                    <div class="ascii-container">
                        <pre class="ascii-art">
 ██████╗ ██████╗ ███╗   ███╗███╗   ███╗██╗███████╗███████╗██╗ ██████╗ ███╗   ██╗███████╗
██╔════╝██╔═══██╗████╗ ████║████╗ ████║██║██╔════╝██╔════╝██║██╔═══██╗████╗  ██║██╔════╝
██║     ██║   ██║██╔████╔██║██╔████╔██║██║███████╗███████╗██║██║   ██║██╔██╗ ██║███████╗
██║     ██║   ██║██║╚██╔╝██║██║╚██╔╝██║██║╚════██║╚════██║██║██║   ██║██║╚██╗██║╚════██║
╚██████╗╚██████╔╝██║ ╚═╝ ██║██║ ╚═╝ ██║██║███████║███████║██║╚██████╔╝██║ ╚████║███████║
 ╚═════╝ ╚═════╝ ╚═╝     ╚═╝╚═╝     ╚═╝╚═╝╚══════╝╚══════╝╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝
                        </pre>
                    </div>
                    
                    <?php
                    $commission_status = nvacore_get_commission_status_display();
                    ?>
                    <div style="background-color: rgba(40, 40, 40, 0.7); border: 1px solid var(--border-color); padding: 10px; margin: 15px 0; text-align: center;">
                        <p>Commissions are currently: <span style="color: <?php echo $commission_status['color']; ?>; font-weight: bold;"><?php echo $commission_status['text']; ?></span></p>
                    </div>
                    
                    <?php
                    $commissions_page = get_page_by_title('Commissions');
                    if ($commissions_page) {
                        echo apply_filters('the_content', $commissions_page->post_content);
                    } else {
                        echo '<p>I offer custom artwork, digital assets, and design services.</p>
                              <p>Specialized in pixel art, glitch aesthetics, and dithered compositions.</p>';
                    }
                    ?>
                    
                    <h3>Tools I Use</h3>
                    <div class="tool-grid">
                        <div class="tool-item">
                            <div class="tool-icon">🔄</div>
                            <p>pixdither</p>
                        </div>
                        <div class="tool-item">
                            <div class="tool-icon">🔠</div>
                            <p>l3ttermmapper</p>
                        </div>
                        <div class="tool-item">
                            <div class="tool-icon">💎</div>
                            <p>sapphire</p>
                        </div>
                        <div class="tool-item">
                            <div class="tool-icon">🔄</div>
                            <p>pixel sorter 3</p>
                        </div>
                    </div>
                    
                    <p style="text-align: center; margin-top: 15px;">All After Effects plugins + Adobe Photoshop</p>
                </div>
            </div>
            
            <!-- Window Content - Discord -->
            <div class="file-content" id="discord-content">
                <div class="file-content-header">
                    <div class="file-content-title">♫ /discord</div>
                    <div class="window-controls">
                        <div class="window-control window-minimize">─</div>
                        <div class="window-control window-maximize">□</div>
                        <div class="window-control window-close">×</div>
                    </div>
                </div>
                <div class="content-section">
                    <div class="ascii-container">
                        <pre class="ascii-art">
██████╗ ██╗███████╗ ██████╗ ██████╗ ██████╗ ██████╗ 
██╔══██╗██║██╔════╝██╔════╝██╔═══██╗██╔══██╗██╔══██╗
██║  ██║██║███████╗██║     ██║   ██║██████╔╝██║  ██║
██║  ██║██║╚════██║██║     ██║   ██║██╔══██╗██║  ██║
██████╔╝██║███████║╚██████╗╚██████╔╝██║  ██║██████╔╝
╚═════╝ ╚═╝╚══════╝ ╚═════╝ ╚═════╝ ╚═╝  ╚═╝╚═════╝ 
                        </pre>
                    </div>
                    
                    <?php
                    $discord_page = get_page_by_title('Discord');
                    if ($discord_page) {
                        echo apply_filters('the_content', $discord_page->post_content);
                    } else {
                        echo '<p>Welcome Traveler !! The GANGO community server is fueled with the energy of creatives of all typeZ! #GANGO111</p>';
                    }
                    ?>
                    
                    <div class="content-gallery">
                        <a href="<?php echo get_theme_mod('nvacore_discord_server', 'https://discord.gg/UuhZFmwdWC'); ?>" target="_blank" class="gallery-item">Join</a>
                    </div>
                </div>
            </div>
            
            <?php include get_template_directory() . '/template-parts/guestbook-content.php'; ?>
            
            <?php include get_template_directory() . '/template-parts/contact-content.php'; ?>
            
            <?php include get_template_directory() . '/template-parts/ultracore-content.php'; ?>
            
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
        <div class="taskbar-item about" data-window="about-content" data-order="1">✧</div>
        <div class="taskbar-item interests" data-window="interests-content" data-order="2">★</div>
        <div class="taskbar-item art" data-window="art-content" data-order="3">✿</div>
        <div class="taskbar-item log" data-window="log-content" data-order="4">✎</div>
        <div class="taskbar-item commissions" data-window="commissions-content" data-order="5">❂</div>
        <div class="taskbar-item discord" data-window="discord-content" data-order="6">♫</div>
        <div class="taskbar-item guestbook" data-window="guestbook-content" data-order="7">✉</div>
        <div class="taskbar-item contact" data-window="contact-content" data-order="8">⚙</div>
        <div class="taskbar-item ultracore" data-window="ultracore-content" data-order="9">
            <div class="rainbow-text">✦</div>
        </div>
    </div>
</div>

<!-- Custom cursor elements -->
<div class="custom-cursor"></div>
<div class="click-animation"></div>
<div class="cursor-ray"></div>
<div id="real-mouse-position"></div>

<!-- Admin Panel for Enhanced Guestbook (only show if user can manage options) -->
<?php if (current_user_can('manage_options')): ?>
<div id="admin-panel" class="admin-panel">
    <div class="admin-panel-header">
        <div class="admin-panel-title">Admin Panel</div>
        <div class="admin-panel-close" id="admin-panel-close">×</div>
    </div>
    <div class="admin-panel-content">
        <div id="admin-controls" class="admin-controls">
            <div class="admin-status">
                <span class="admin-status-indicator active"></span>
                <span>Logged in as administrator</span>
            </div>
            <button id="admin-logout-button" class="admin-button admin-button-danger">Logout</button>
        </div>
    </div>
</div>
<?php endif; ?>

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