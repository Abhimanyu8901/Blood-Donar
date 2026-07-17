    </main>

    <footer class="site-footer">
      <section class="footer-cta">
        <div>
          <p class="eyebrow">Ready to help?</p>
          <h2>Join the local donor network before an emergency happens.</h2>
        </div>
        <div class="footer-cta-actions">
          <a href="become-donor.php" class="btn btn-primary">Register as a Donor</a>
          <a href="find-blood.php" class="btn btn-secondary">Raise a Blood Request</a>
        </div>
      </section>

      <div class="footer-grid">
        <div>
          <a href="index.php" class="brand footer-brand">
            <img src="Blood_Saathi.png" alt="Blood Saathi logo" />
            <span>
              <strong>Blood Saathi</strong>
              <small>Frontend concept in PHP</small>
            </span>
          </a>
          <p class="footer-copy">
            A clean, trustworthy interface for blood donation, volunteer coordination, and urgent request discovery.
          </p>
        </div>

        <div>
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <li><a href="about.php">About Us</a></li>
            <li><a href="find-blood.php">Find Donor / Request Blood</a></li>
            <li><a href="become-donor.php">Become a Donor</a></li>
            <li><a href="blood-groups.php">Blood Groups</a></li>
            <li><a href="how-it-works.php">How It Works</a></li>
            <li><a href="faq.php">FAQs</a></li>
          </ul>
        </div>

        <div>
          <h3>Emergency & Trust</h3>
          <ul class="footer-links">
            <li><a href="emergency-help.php">Emergency Help</a></li>
            <li><a href="donor-dashboard.php">Donor Dashboard</a></li>
            <li><a href="privacy-policy.php">Privacy Policy</a></li>
            <li><a href="terms.php">Terms of Service</a></li>
            <li><a href="safety-guidelines.php">Safety Guidelines</a></li>
            <li><a href="contact.php">Partner With Us</a></li>
          </ul>
        </div>

        <div>
          <h3>Contact</h3>
          <ul class="footer-links footer-contact">
            <li>Blood Saathi Coordination Desk</li>
            <li>New Delhi, India</li>
            <li>support@bloodsaathi.org</li>
            <li>+91 98765 43210</li>
            <li>Mon-Sun, 24/7 response desk</li>
          </ul>
          <div class="social-links">
            <a href="https://instagram.com" target="_blank" rel="noreferrer">Instagram</a>
            <a href="https://facebook.com" target="_blank" rel="noreferrer">Facebook</a>
            <a href="https://linkedin.com" target="_blank" rel="noreferrer">LinkedIn</a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Blood Saathi. Frontend-only concept built with HTML, CSS, and PHP.</p>
        <div class="footer-bottom-links">
          <a href="faq.php">FAQ</a>
          <a href="privacy-policy.php">Privacy Policy</a>
          <a href="terms.php">Terms of Service</a>
          <a href="safety-guidelines.php">Safety</a>
        </div>
      </div>
    </footer>
  </div>

  <script>
    const menuButton = document.querySelector('.menu-toggle');
    const navPanel = document.querySelector('.nav-panel');

    if (menuButton && navPanel) {
      menuButton.addEventListener('click', () => {
        const expanded = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        navPanel.classList.toggle('open');
      });
    }
  </script>
</body>
</html>
