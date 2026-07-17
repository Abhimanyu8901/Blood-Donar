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
            A clean, trustworthy blood donation and urgent request.
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
        <p>&copy; <?php echo date('Y'); ?> Blood Saathi</p>
        <div class="footer-bottom-links">
          <a href="faq.php">FAQ</a>
          <a href="privacy-policy.php">Privacy Policy</a>
          <a href="terms.php">Terms of Service</a>
          <a href="safety-guidelines.php">Safety</a>
        </div>
      </div>
    </footer>

    <div class="ai-assistant" data-ai-assistant>
      <button class="ai-toggle" type="button" aria-expanded="false" aria-controls="ai-panel">
        <span class="ai-toggle-icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" role="presentation" focusable="false">
            <path d="M32 8c3.3 0 6 2.7 6 6v4h7c7.2 0 13 5.8 13 13v12c0 7.2-5.8 13-13 13h-3v5a3 3 0 0 1-5.1 2.1L30.8 56H19c-7.2 0-13-5.8-13-13V31c0-7.2 5.8-13 13-13h7v-4c0-3.3 2.7-6 6-6Zm0 4a2 2 0 0 0-2 2v4h4v-4a2 2 0 0 0-2-2Zm-11 18a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm22 0a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM22 46a2 2 0 1 0 0 4h20a2 2 0 1 0 0-4H22Z"></path>
          </svg>
        </span>
        Ask Blood Saathi
      </button>

      <section class="ai-panel" id="ai-panel" hidden>
        <div class="ai-panel-head">
          <div>
            <p class="eyebrow">AI Assistant</p>
            <h3>Ask Blood Saathi</h3>
          </div>
          <button class="ai-close" type="button" aria-label="Close assistant">&times;</button>
        </div>

        <div class="ai-messages" data-ai-messages>
          <article class="ai-message ai-message-bot">
            <p>Hello. I can help with blood donation basics, donor eligibility guidance, emergency request preparation, and finding the right page on this site.</p>
          </article>
        </div>

        <form class="ai-form" data-ai-form>
          <label class="sr-only" for="ai-message">Ask the assistant</label>
          <textarea id="ai-message" name="message" rows="3" placeholder="Ask about donor eligibility, blood groups, emergency steps, or where to go on the site." required></textarea>
          <div class="ai-form-row">
            <p class="ai-note">For emergencies, contact the hospital or blood bank immediately.</p>
            <button class="btn btn-primary" type="submit">Send</button>
          </div>
        </form>
      </section>
    </div>
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

    const aiRoot = document.querySelector('[data-ai-assistant]');
    const aiToggle = aiRoot ? aiRoot.querySelector('.ai-toggle') : null;
    const aiPanel = aiRoot ? aiRoot.querySelector('.ai-panel') : null;
    const aiClose = aiRoot ? aiRoot.querySelector('.ai-close') : null;
    const aiForm = aiRoot ? aiRoot.querySelector('[data-ai-form]') : null;
    const aiMessages = aiRoot ? aiRoot.querySelector('[data-ai-messages]') : null;
    const aiInput = aiRoot ? aiRoot.querySelector('#ai-message') : null;

    const appendAiMessage = (text, role) => {
      if (!aiMessages) {
        return;
      }

      const item = document.createElement('article');
      item.className = `ai-message ${role === 'user' ? 'ai-message-user' : 'ai-message-bot'}`;

      const paragraph = document.createElement('p');
      paragraph.textContent = text;
      item.appendChild(paragraph);
      aiMessages.appendChild(item);
      aiMessages.scrollTop = aiMessages.scrollHeight;
    };

    const setAssistantOpen = (open) => {
      if (!aiToggle || !aiPanel) {
        return;
      }

      aiToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      aiPanel.hidden = !open;

      if (open && aiInput) {
        aiInput.focus();
      }
    };

    if (aiToggle && aiPanel) {
      aiToggle.addEventListener('click', () => {
        const isOpen = aiToggle.getAttribute('aria-expanded') === 'true';
        setAssistantOpen(!isOpen);
      });
    }

    if (aiClose) {
      aiClose.addEventListener('click', () => setAssistantOpen(false));
    }

    if (aiForm && aiInput) {
      aiForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = aiInput.value.trim();

        if (!message) {
          return;
        }

        appendAiMessage(message, 'user');
        aiInput.value = '';

        const submitButton = aiForm.querySelector('button[type="submit"]');

        if (submitButton) {
          submitButton.disabled = true;
          submitButton.textContent = 'Sending...';
        }

        try {
          const response = await fetch('ai-assistant.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message })
          });

          const data = await response.json();
          appendAiMessage(data.message || 'The assistant is unavailable right now. Please try again.', 'bot');
        } catch (error) {
          appendAiMessage('The assistant is unavailable right now. Please try again in a moment.', 'bot');
        } finally {
          if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = 'Send';
          }
        }
      });
    }
  </script>
</body>
</html>
