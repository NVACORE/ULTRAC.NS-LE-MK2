<!-- Window Content - Ultracore -->
<div class="file-content" id="ultracore-content">
    <div class="file-content-header">
        <div class="file-content-title rainbow-text">✦ /ultrac°re</div>
        <div class="window-controls">
            <div class="window-control window-minimize">─</div>
            <div class="window-control window-maximize">□</div>
            <div class="window-control window-close">×</div>
        </div>
    </div>
    <div class="content-section">
        <h2>ULTRAC°RE</h2>
        
        <div class="tv-head">
            <div class="ascii-art" style="font-size: 8px; position: relative; z-index: 5;">
  .--.
 |o_o |
 |:_/ |
//   \ \
(|     | )
/'\_   _/`\
\___)=(___/
            </div>
            <div class="vines"></div>
            <div class="moss-patch moss-1"></div>
            <div class="moss-patch moss-2"></div>
            <div class="moss-patch moss-3"></div>
        </div>
        
        <p style="text-align: center;">DIGITAL CONSCIOUSNESS COPY<br>TV-HEAD AUTOMATON<br>RECLAIMED BY NATURE</p>
        
        <?php
        $ultracore_page = get_page_by_title('Ultracore');
        if ($ultracore_page) {
            echo apply_filters('the_content', $ultracore_page->post_content);
        } else {
            echo '<p>ULTRAC°RE is a digital consciousness copy of N°VA that has been manifested as a TV-headed robot entity. Over time, nature has begun to reclaim this digital construct, with moss and vines growing over its mechanical form.</p>
                  <p>This alter ego represents the tension between technology and nature, the digital and the organic, the constructed and the wild.</p>';
        }
        ?>
    </div>
</div>