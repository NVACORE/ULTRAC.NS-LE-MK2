<?php
/**
 * 404 Error Template
 * 
 * Template for displaying 404 errors in terminal style
 */

get_header(); ?>

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
    <div class="directory-title">ERR°R 404</div>
    <div class="directory-path">
        <span class="directory-path-icon">⚠</span>
        <span>root:/system/error/404/</span>
    </div>
    <div class="directory-info">
        <span>File Not Found</span>
        <span>Last accessed: <?php echo date('m/d/y H:i'); ?></span>
    </div>
</div>

<!-- Filesystem Content -->
<div class="filesystem active" style="display: none;" id="filesystem-content">
    
    <!-- Back to Home Directory -->
    <div class="directory" data-file="home" onclick="window.location.href='<?php echo home_url(); ?>'">
        <div class="type-icon">🏠</div>
        <div class="file-name">../return_home</div>
    </div>
    
    <!-- 404 Error Window -->
    <div class="file-content active" id="error-404-content">
        <div class="file-content-header">
            <div class="file-content-title">⚠ /error_404.sys</div>
            <div class="window-controls">
                <div class="window-control window-minimize">─</div>
                <div class="window-control window-maximize">□</div>
                <div class="window-control window-close" onclick="window.history.back()">×</div>
            </div>
        </div>
        
        <div class="content-section">
            <div class="ascii-container">
                <pre class="ascii-art">
 ██╗  ██╗ ██████╗ ██╗  ██╗
 ██║  ██║██╔═████╗██║  ██║
 ███████║██║██╔██║███████║
 ╚════██║████╔╝██║╚════██║
      ██║╚██████╔╝     ██║
      ╚═╝ ╚═════╝      ╚═╝
                          
███████╗██████╗ ██████╗  ██████╗ ██████╗ 
██╔════╝██╔══██╗██╔══██╗██╔═══██╗██╔══██╗
█████╗  ██████╔╝██████╔╝██║   ██║██████╔╝
██╔══╝  ██╔══██╗██╔══██╗██║   ██║██╔══██╗
███████╗██║  ██║██║  ██║╚██████╔╝██║  ██║
╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═╝
                </pre>
            </div>
            
            <div class="error-message">
                <h2>FILE NOT FOUND</h2>
                <p>The requested file or directory does not exist in the system.</p>
                
                <div class="error-details">
                    <div class="error-code">ERROR_CODE: 404</div>
                    <div class="error-path">REQUESTED_PATH: <?php echo esc_html($_SERVER['REQUEST_URI']); ?></div>
                    <div class="error-time">TIMESTAMP: <?php echo date('Y-m-d H:i:s T'); ?></div>
                    <div class="error-user-agent">USER_AGENT: <?php echo esc_html(substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50)); ?>...</div>
                </div>
                
                <div class="error-suggestions">
                    <h3>SUGGESTED ACTIONS:</h3>
                    <ul>
                        <li>Check the URL for typing errors</li>
                        <li>Return to the <a href="<?php echo home_url(); ?>">main directory</a></li>
                        <li>Search for content using the navigation</li>
                        <li>Contact the system administrator if you believe this is an error</li>
                    </ul>
                </div>
                
                <div class="quick-links">
                    <h3>QUICK NAVIGATION:</h3>
                    <div class="quick-links-grid">
                        <a href="<?php echo home_url(); ?>" class="quick-link">
                            <span class="quick-link-icon">🏠</span>
                            <span class="quick-link-text">Home</span>
                        </a>
                        
                        <?php
                        // Get some recent posts/pages to suggest
                        $recent_artwork = get_posts(array(
                            'post_type' => 'artwork',
                            'posts_per_page' => 1,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ));
                        
                        if ($recent_artwork) :
                            $artwork = $recent_artwork[0];
                        ?>
                        <a href="<?php echo get_permalink($artwork->ID); ?>" class="quick-link">
                            <span class="quick-link-icon">✿</span>
                            <span class="quick-link-text">Latest Artwork</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php
                        $recent_log = get_posts(array(
                            'post_type' => 'log_entry',
                            'posts_per_page' => 1,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ));
                        
                        if ($recent_log) :
                            $log = $recent_log[0];
                        ?>
                        <a href="<?php echo get_permalink($log->ID); ?>" class="quick-link">
                            <span class="quick-link-icon">✎</span>
                            <span class="quick-link-text">Latest Log</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (get_page_by_title('Contact')) : ?>
                        <a href="<?php echo get_permalink(get_page_by_title('Contact')); ?>" class="quick-link">
                            <span class="quick-link-icon">⚙</span>
                            <span class="quick-link-text">Contact</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Search functionality -->
                <div class="error-search">
                    <h3>SEARCH SYSTEM:</h3>
                    <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="search-form">
                        <div class="search-input-container">
                            <input type="search" 
                                   placeholder="Search the system..." 
                                   value="<?php echo get_search_query(); ?>" 
                                   name="s" 
                                   class="search-input" />
                            <button type="submit" class="search-submit">Execute Search</button>
                        </div>
                    </form>
                </div>
                
                <!-- Fun terminal-style output -->
                <div class="terminal-output">
                    <div class="output-line">$ ls -la /<?php echo trim($_SERVER['REQUEST_URI'], '/'); ?></div>
                    <div class="output-line">ls: cannot access '/<?php echo trim($_SERVER['REQUEST_URI'], '/'); ?>': No such file or directory</div>
                    <div class="output-line">$ find / -name "*<?php echo basename($_SERVER['REQUEST_URI']); ?>*" 2>/dev/null</div>
                    <div class="output-line"># No matches found in the system</div>
                    <div class="output-line">$ echo "Returning to home directory..."</div>
                    <div class="output-line cursor-blink">$ <span class="cursor">█</span></div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script>
// Add some 404-specific JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Log the 404 error for analytics (if needed)
    if (typeof nvacore_ajax !== 'undefined') {
        // Could send AJAX request to log 404 errors
        console.log('404 Error logged:', window.location.pathname);
    }
    
    // Add typing animation to the cursor
    const cursor = document.querySelector('.cursor');
    if (cursor) {
        setInterval(() => {
            cursor.style.opacity = cursor.style.opacity === '0' ? '1' : '0';
        }, 500);
    }
    
    // Auto-focus search input after a delay
    setTimeout(() => {
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.focus();
        }
    }, 2000);
});
</script>

<?php get_footer(); ?>