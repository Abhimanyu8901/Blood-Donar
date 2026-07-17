<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Contact Blood Saathi';
$page_description = 'Contact and emergency support page for Blood Saathi with MySQL-backed intake form.';
$active_page = 'contact';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $fullName = post_value('full_name');
    $email = strtolower(post_value('email'));
    $phone = post_value('phone');
    $reason = post_value('reason');
    $message = post_value('message');

    if ($fullName === '' || $email === '' || $reason === '' || $message === '') {
        $errors[] = 'Please complete the contact form before sending.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please use a valid email address.';
    }

    if (!$errors) {
        $statement = $pdo->prepare(
            'INSERT INTO contact_messages (full_name, email, phone, reason, message)
             VALUES (:full_name, :email, :phone, :reason, :message)'
        );
        $statement->execute([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'reason' => $reason,
            'message' => $message,
        ]);

        set_flash('success', 'Your message has been saved. The support team can now review it from the database.');
        redirect_to('contact.php');
    }
}

include 'includes/header.php';
?>

<section class="page-hero">
  <p class="eyebrow">Contact and support</p>
  <h1>Reliable support when every minute matters.</h1>
  <p>Clear communication builds trust, especially in healthcare-related platforms where reliability and response time are critical.</p>
</section>

<section class="section">
  <div class="split-layout">
    <div class="info-stack">
      <article class="info-card">
        <h3>Emergency helpline</h3>
        <p><strong>+91 98765 43210</strong></p>
        <p>For critical cases where coordination is needed immediately.</p>
      </article>
      <article class="info-card">
        <h3>Email</h3>
        <p>support@bloodsaathi.org<br />partners@bloodsaathi.org</p>
      </article>
      <article class="info-card">
        <h3>Address</h3>
        <p>Blood Saathi Coordination Desk<br />New Delhi, India<br />Mon-Sun, 24/7 support coverage</p>
      </article>
    </div>

    <form class="form-card" method="post" action="contact.php">
      <p class="eyebrow">Message the team</p>
      <h2>Contact form</h2>

      <?php if ($errors): ?>
        <div class="page-alert error form-inline-alert">
          <span><?php echo e(implode(' ', $errors)); ?></span>
        </div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="form-group">
          <label for="contact-name">Full name</label>
          <input id="contact-name" name="full_name" type="text" value="<?php echo old('full_name'); ?>" placeholder="Your name" required />
        </div>
        <div class="form-group">
          <label for="contact-email">Email</label>
          <input id="contact-email" name="email" type="email" value="<?php echo old('email'); ?>" placeholder="you@example.com" required />
        </div>
        <div class="form-group">
          <label for="contact-phone">Phone</label>
          <input id="contact-phone" name="phone" type="tel" value="<?php echo old('phone'); ?>" placeholder="+91" />
        </div>
        <div class="form-group">
          <label for="contact-type">Reason</label>
          <select id="contact-type" name="reason" required>
            <?php foreach (['Emergency request', 'General support', 'Partnership inquiry', 'Media inquiry'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo old('reason', 'Emergency request') === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group full">
          <label for="contact-message">Message</label>
          <textarea id="contact-message" name="message" placeholder="How can we help you?" required><?php echo old('message'); ?></textarea>
        </div>
      </div>
      <div class="button-row">
        <button type="submit" class="btn btn-primary">Send Message</button>
      </div>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
