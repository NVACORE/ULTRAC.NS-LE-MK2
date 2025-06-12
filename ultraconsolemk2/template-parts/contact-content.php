<!-- Window Content - Contact -->
<div class="file-content" id="contact-content">
    <div class="file-content-header">
        <div class="file-content-title">⚙ /contact</div>
        <div class="window-controls">
            <div class="window-control window-minimize">─</div>
            <div class="window-control window-maximize">□</div>
            <div class="window-control window-close">×</div>
        </div>
    </div>
    <div class="content-section">
        <div class="ascii-container">
            <pre class="ascii-art">
 ██████╗ ██████╗ ███╗   ██╗████████╗ █████╗  ██████╗████████╗
██╔════╝██╔═══██╗████╗  ██║╚══██╔══╝██╔══██╗██╔════╝╚══██╔══╝
██║     ██║   ██║██╔██╗ ██║   ██║   ███████║██║        ██║   
██║     ██║   ██║██║╚██╗██║   ██║   ██╔══██║██║        ██║   
╚██████╗╚██████╔╝██║ ╚████║   ██║   ██║  ██║╚██████╗   ██║   
 ╚═════╝ ╚═════╝ ╚═╝  ╚═══╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝   ╚═╝   
            </pre>
        </div>
        
        <?php
        $contact_page = get_page_by_title('Contact');
        if ($contact_page) {
            echo apply_filters('the_content', $contact_page->post_content);
        } else {
            echo '<p>Feel free to reach out for collaborations, commissions, or just to chat!</p>';
        }
        ?>
        
        <div class="contact-card">
            <div class="contact-header">
                <div class="contact-title">Instagram (N°VACORE)</div>
            </div>
            <div class="contact-description">
                Follow my main account for digital artwork and announcements.
            </div>
            <div class="contact-description">
                <a href="<?php echo get_theme_mod('nvacore_instagram_main', 'https://www.instagram.com/n.vacore/'); ?>" target="_blank" id="instagram-link" class="contact-button">
                    <span class="contact-button-icon">📷</span>
                    <span class="contact-button-text">@n.vacore</span>
                </a>
            </div>
        </div>
        
        <div class="contact-card">
            <div class="contact-header">
                <div class="contact-title">Instagram (ULTRAC°RE)</div>
            </div>
            <div class="contact-description">
                Follow my alter ego for experimental glitch art and digital explorations.
            </div>
            <div class="contact-description">
                <a href="<?php echo get_theme_mod('nvacore_instagram_alt', 'https://www.instagram.com/ultrac_re/'); ?>" target="_blank" id="ultracore-instagram-link" class="contact-button">
                    <span class="contact-button-icon">📷</span>
                    <span class="contact-button-text">@ultrac_re</span>
                </a>
            </div>
        </div>
        
        <div class="contact-card">
            <div class="contact-header">
                <div class="contact-title">Discord</div>
            </div>
            <div class="contact-description">
                Find me on Discord or click to copy my username.
            </div>
            <div class="contact-description">
                <button type="button" id="discord-copy" class="contact-button">
                    <span class="contact-button-icon">💬</span>
                    <span class="contact-button-text"><?php echo get_theme_mod('nvacore_discord_username', 'n.vacore'); ?></span>
                    <span id="copy-icon" style="margin-left: 10px; display: none;">✓</span>
                </button>
                <div id="copy-message" style="display: none; margin-top: 8px; color: #00ff00; font-size: 12px; transition: opacity 0.3s ease;">Username copied to clipboard!</div>
            </div>
        </div>
        
        <div class="contact-card">
            <div class="contact-header">
                <div class="contact-title">Email</div>
            </div>
            <div class="contact-description">
                Send me an email directly. I typically respond within 48 hours.
            </div>
            <div class="contact-description">
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode(get_theme_mod('nvacore_email', 'cadenbunce@gmail.com')); ?>" target="_blank" id="email-link" class="contact-button">
                    <span class="contact-button-icon">✉️</span>
                    <span class="contact-button-text"><?php echo get_theme_mod('nvacore_email', 'cadenbunce@gmail.com'); ?></span>
                </a>
            </div>
        </div>
        
        <div class="contact-card">
            <div class="contact-header">
                <div class="contact-title">Commission Inquiries</div>
            </div>
            <div class="contact-description">
                For commission requests, please include the following details:
                <ul style="margin-top: 10px;">
                    <li>Project type and scope</li>
                    <li>Timeline and deadline</li>
                    <li>Budget range</li>
                    <li>Reference materials (if available)</li>
                </ul>
            </div>
        </div>
    </div>
</div>