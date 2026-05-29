<!-- ============================================================
     Floating Support Button – Mobile App QR + Raise a Ticket
============================================================ -->
<style>
  /* ---------- Floating Button ---------- */
  #lmx-support-btn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 99999;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1565C0 0%, #1E88E5 100%);
    box-shadow: 0 4px 18px rgba(21, 101, 192, 0.55);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    outline: none;
  }

  #lmx-support-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 24px rgba(21, 101, 192, 0.7);
  }

  #lmx-support-btn svg {
    width: 18px;
    height: 18px;
    fill: #fff;
    transition: transform 0.3s ease;
  }

  #lmx-support-btn.open svg {
    transform: rotate(45deg);
  }

  #lmx-support-btn::after {
    content: '';
    position: absolute;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid rgba(30, 136, 229, 0.6);
    animation: lmxPulse 2s infinite;
  }

  @keyframes lmxPulse {
    0% {
      transform: scale(1);
      opacity: 1;
    }

    70% {
      transform: scale(1.5);
      opacity: 0;
    }

    100% {
      transform: scale(1.5);
      opacity: 0;
    }
  }

  /* ---------- Popup Card ---------- */
  #lmx-support-popup {
    position: fixed;
    bottom: 80px;
    right: 28px;
    z-index: 99998;
    width: 300px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
    overflow: hidden;
    display: none;
    transform: translateY(16px);
    opacity: 0;
    transition: transform 0.28s cubic-bezier(.22, .68, 0, 1.3), opacity 0.22s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }

  #lmx-support-popup.visible {
    display: block;
    transform: translateY(0);
    opacity: 1;
  }

  /* Header */
  .lmx-popup-header {
    background: linear-gradient(135deg, #1565C0 0%, #1E88E5 100%);
    padding: 14px 18px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .lmx-popup-header svg {
    width: 20px;
    height: 20px;
    fill: #fff;
    flex-shrink: 0;
  }

  .lmx-popup-header span {
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.2px;
  }

  /* Sections */
  .lmx-section {
    padding: 16px 18px;
    border-bottom: 1px solid #EEF3FA;
  }

  .lmx-section:last-child {
    border-bottom: none;
  }

  .lmx-section-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #1565C0;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: nowrap;
    white-space: nowrap;
  }

  /* QR code */
  .lmx-qr-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }

  .lmx-qr-wrap img {
    display: block;
    width: 150px;
    height: 150px;
    border: 2px solid #E3F0FF;
    border-radius: 10px;
    padding: 6px;
    background: #F5F9FF;
    box-sizing: border-box;
  }

  .lmx-qr-hint {
    font-size: 11px;
    color: #888;
    margin: 0;
    text-align: center;
  }

  /* Platform badges */
  .lmx-platform-badges {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 2px;
  }

  .lmx-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #F0F4FF;
    border: 1px solid #C9D9F8;
    border-radius: 20px;
    padding: 2px 7px 2px 5px;
    font-size: 9px;
    font-weight: 600;
    color: #1565C0;
    letter-spacing: 0.1px;
  }

  .lmx-badge svg {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
  }

  /* Raise a Ticket section */
  .lmx-ticket-section {
    padding: 14px 18px 16px;
  }

  .lmx-ticket-desc {
    font-size: 12px;
    color: #666;
    margin: 0 0 10px 0;
    line-height: 1.5;
    text-align: center;
  }

  .lmx-cta-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #1565C0 0%, #1E88E5 100%);
    color: #fff !important;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none !important;
    padding: 10px 20px;
    border-radius: 10px;
    transition: opacity 0.18s ease, transform 0.18s ease;
    width: 100%;
    box-sizing: border-box;
  }

  .lmx-cta-btn:hover {
    opacity: 0.88;
    transform: translateY(-1px);
  }

  .lmx-cta-btn svg {
    width: 16px;
    height: 16px;
    fill: #fff;
    flex-shrink: 0;
  }
</style>

<!-- Floating Button -->
<button id="lmx-support-btn" title="Support &amp; Help">
  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 2C6.48 2 2 6.48 2 12v3.5c0 .83.67 1.5 1.5 1.5H5c.55 0 1-.45
               1-1v-3c0-.55-.45-1-1-1H3.07C3.52 7.08 7.4 4 12 4s8.48 3.08
               8.93 8H19c-.55 0-1 .45-1 1v3c0 .55.45 1 1 1h1v1c0 1.1-.9
               2-2 2h-2c0-1.66-1.34-3-3-3s-3 1.34-3 3 1.34 3 3 3h5c2.21 0
               4-1.79 4-4v-6.5C22 6.48 17.52 2 12 2z" />
  </svg>
</button>

<!-- Popup Card -->
<div id="lmx-support-popup">

  <!-- Header -->
  <div class="lmx-popup-header">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
    </svg>
    <span>Support &amp; Help</span>
  </div>

  <!-- Section 1 : Mobile App -->
  <div class="lmx-section">
    <p class="lmx-section-label">&#128242; Download App
      <a href="https://play.google.com/store/apps/details?id=support.logimaxindia.com&pcampaignid=web_share" target="_blank" class="lmx-badge" style="text-decoration:none;"><svg viewBox="0 0 24 24" fill="#3DDC84" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M6 18c0 .55.45 1 1 1h1v3.5c0 .83.67 1.5 1.5 1.5S11 23.33 11 22.5V19h2v3.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5V19h1c.55 0 1-.45 1-1V8H6v10zM3.5 8C2.67 8 2 8.67 2 9.5v7c0 .83.67 1.5 1.5 1.5S5 17.33 5 16.5v-7C5 8.67 4.33 8 3.5 8zm17 0c-.83 0-1.5.67-1.5 1.5v7c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5v-7c0-.83-.67-1.5-1.5-1.5zm-4.97-5.84l1.3-1.3c.2-.2.2-.51 0-.71-.2-.2-.51-.2-.71 0l-1.48 1.48C13.85 1.23 12.95 1 12 1c-.96 0-1.86.23-2.66.63L7.85.15c-.2-.2-.51-.2-.71 0-.2.2-.2.51 0 .71l1.31 1.31C6.97 3.26 6 5.01 6 7h12c0-1.99-.97-3.75-2.47-4.84zM10 5H9V4h1v1zm5 0h-1V4h1v1z" />
        </svg> Android</a>
      <a href="https://apps.apple.com/us/app/logimaxcrm/id6761261994" target="_blank" class="lmx-badge" style="text-decoration:none;"><svg viewBox="0 0 24 24" fill="#555" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
        </svg> iOS</a>
    </p>
    <div class="lmx-qr-wrap">
      <img src="<?php echo base_url('assets/img/mobile_app_qr.png'); ?>" alt="Scan to download Mobile App" />
      <p class="lmx-qr-hint">Scan with your camera to install the app</p>
    </div>
  </div>

  <!-- Section 2 : Raise a Ticket -->
  <div class="lmx-section lmx-ticket-section">
    <p class="lmx-section-label">&#127915; Raise a Support Ticket</p>
    <a href="https://support.logimaxindia.com/client" target="_blank" class="lmx-cta-btn">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0
                   2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
      </svg>
      Raise a Ticket
    </a>
  </div>

</div>

<script>
  (function () {
    var btn = document.getElementById('lmx-support-btn');
    var pop = document.getElementById('lmx-support-popup');
    var open = false;

    function showPopup() {
      pop.style.display = 'block';
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          pop.classList.add('visible');
        });
      });
    }

    function hidePopup() {
      pop.classList.remove('visible');
      setTimeout(function () {
        if (!open) pop.style.display = 'none';
      }, 280);
    }

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      open = !open;
      btn.classList.toggle('open', open);
      if (open) { showPopup(); } else { hidePopup(); }
    });

    document.addEventListener('click', function (e) {
      if (open && !pop.contains(e.target) && e.target !== btn) {
        open = false;
        btn.classList.remove('open');
        hidePopup();
      }
    });
  })();
</script>
<!-- ============================================================
     /Floating Support Button
============================================================ -->
