<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Become a Donor | Blood Saathi';
$page_description = 'Frontend donor onboarding page with blood group, city, availability, and contact preference fields.';
$active_page = 'donor';
$errors = [];
$today = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $fullName = post_value('full_name');
    $phone = post_value('phone');
    $email = strtolower(post_value('email'));
    $bloodGroup = post_value('blood_group');
    $city = post_value('city');
    $availability = post_value('availability');
    $contactPreference = post_value('contact_preference');
    $lastDonation = post_value('last_donation_date') ?: null;
    $notes = post_value('notes');
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $phone === '' || $email === '' || $bloodGroup === '' || $city === '') {
        $errors[] = 'Please fill in all required donor details.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Please choose a password with at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password confirmation does not match.';
    }

    if ($lastDonation !== null && $lastDonation > $today) {
        $errors[] = 'Last donation date cannot be later than today.';
    }

    $existing = db_one(
        'SELECT d.id FROM donors d WHERE d.email = :donor_email
         UNION
         SELECT u.donor_id AS id FROM users u WHERE u.email = :user_email
         LIMIT 1',
        [
            'donor_email' => $email,
            'user_email' => $email,
        ]
    );

    if ($existing) {
        $errors[] = 'A donor account with that email already exists.';
    }

    if (!$errors) {
        $statement = $pdo->prepare(
            'INSERT INTO donors (full_name, phone, email, blood_group, city, availability, contact_preference, last_donation_date, notes, status)
             VALUES (:full_name, :phone, :email, :blood_group, :city, :availability, :contact_preference, :last_donation_date, :notes, :status)'
        );

        $statement->execute([
            'full_name' => $fullName,
            'phone' => $phone,
            'email' => $email,
            'blood_group' => $bloodGroup,
            'city' => $city,
            'availability' => $availability,
            'contact_preference' => $contactPreference,
            'last_donation_date' => $lastDonation,
            'notes' => $notes !== '' ? $notes : null,
            'status' => str_contains(strtolower($availability), 'unavailable') ? 'unavailable' : 'available',
        ]);

        $donorId = (int) $pdo->lastInsertId();

        $prefs = $pdo->prepare(
            'INSERT INTO donor_notification_preferences (donor_id, email_enabled, sms_enabled, whatsapp_enabled)
             VALUES (:donor_id, :email_enabled, :sms_enabled, :whatsapp_enabled)'
        );
        $prefs->execute([
            'donor_id' => $donorId,
            'email_enabled' => isset($_POST['notify_email']) ? 1 : 0,
            'sms_enabled' => isset($_POST['notify_sms']) ? 1 : 0,
            'whatsapp_enabled' => isset($_POST['notify_whatsapp']) ? 1 : 0,
        ]);

        $userStatement = $pdo->prepare(
            'INSERT INTO users (donor_id, full_name, email, password_hash, role) VALUES (:donor_id, :full_name, :email, :password_hash, :role)'
        );
        $userStatement->execute([
            'donor_id' => $donorId,
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'donor',
        ]);

        login_user([
            'id' => (int) $pdo->lastInsertId(),
            'role' => 'donor',
            'full_name' => $fullName,
            'email' => $email,
            'donor_id' => $donorId,
        ]);

        set_flash('success', 'Donor profile created successfully. You can now manage it from your dashboard.');
        redirect_to('donor-dashboard.php');
    }
}

include 'includes/header.php';
?>

<section class="page-hero">
  <p class="eyebrow">Become a donor</p>
  <h1>Join a network of donors ready to save lives.</h1>
  <p>
    Every donor profile helps create a faster connection between people who need blood urgently and those willing to help at the right time.<br> By sharing your blood group, city, and availability, you can make a real difference in critical moments for patients and their families.
  </p>
</section>

<section class="section">
  <div class="split-layout">
    <div class="info-stack">
      <article class="info-card">
        <h3>What donors should know</h3>
        <ul class="list-clean">
          <li>You contact when you are available.</li>
          <li>You can set a preferred contact method.</li>
          <li>You can update your city and donation status later in the dashboard.</li>
        </ul>
      </article>
      <article class="info-card">
        <h3>Trust and consent</h3>
        <p>Professional donor onboarding should mention consent, privacy, communication preferences, and basic safety expectations.</p>
      </article>
      <article class="info-card">
        <h3>Next step after signup</h3>
        <p>The donor dashboard lets users manage profile details, availability, and urgent alert preferences.</p>
      </article>
    </div>

    <form class="form-card" method="post" action="become-donor.php">
      <p class="eyebrow">Donor registration form</p>
      <h2>Create a donor profile</h2>

      <?php if ($errors): ?>
        <div class="page-alert error form-inline-alert">
          <span><?php echo e(implode(' ', $errors)); ?></span>
        </div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="form-group">
          <label for="donor-name">Full name</label>
          <input id="donor-name" name="full_name" type="text" value="<?php echo old('full_name'); ?>" placeholder="Your full name" required />
        </div>
        <div class="form-group">
          <label for="donor-phone">Phone</label>
          <input id="donor-phone" name="phone" type="tel" value="<?php echo old('phone'); ?>" placeholder="+91" required />
        </div>
        <div class="form-group">
          <label for="donor-email">Email</label>
          <input id="donor-email" name="email" type="email" value="<?php echo old('email'); ?>" placeholder="you@example.com" required />
        </div>
        <div class="form-group">
          <label for="donor-group">Blood group</label>
          <select id="donor-group" name="blood_group" required>
            <?php foreach (['', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group): ?>
              <option value="<?php echo e($group); ?>" <?php echo old('blood_group') === $group ? 'selected' : ''; ?>>
                <?php echo $group === '' ? 'Select blood group' : e($group); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="donor-city">City</label>
          <input id="donor-city" name="city" type="text" value="<?php echo old('city'); ?>" placeholder="Current city" required />
        </div>
        <div class="form-group">
          <label for="donor-availability">Availability</label>
          <select id="donor-availability" name="availability" required>
            <?php foreach (['Available now', 'Available this week', 'Available weekends only', 'Temporarily unavailable'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo old('availability', 'Available now') === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="donor-contact">Contact preference</label>
          <select id="donor-contact" name="contact_preference" required>
            <?php foreach (['Phone call', 'SMS', 'WhatsApp', 'Email'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo old('contact_preference', 'Phone call') === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="donor-date">Last donation date</label>
          <input id="donor-date" name="last_donation_date" type="date" max="<?php echo e($today); ?>" value="<?php echo old('last_donation_date'); ?>" />
        </div>
        <div class="form-group">
          <label for="donor-password">Password</label>
          <input id="donor-password" name="password" type="password" placeholder="At least 8 characters" required />
        </div>
        <div class="form-group">
          <label for="donor-confirm-password">Confirm password</label>
          <input id="donor-confirm-password" name="confirm_password" type="password" placeholder="Repeat your password" required />
        </div>
        <div class="form-group full">
          <label for="donor-notes">Availability notes</label>
          <textarea id="donor-notes" name="notes" placeholder="Best time to contact, travel limit, or any note that helps the support team."><?php echo old('notes'); ?></textarea>
        </div>
      </div>

      <div class="checkbox-grid">
        <label class="check-row"><input type="checkbox" name="notify_email" <?php echo isset($_POST['notify_email']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : ''; ?> /> Email alerts</label>
        <label class="check-row"><input type="checkbox" name="notify_sms" <?php echo isset($_POST['notify_sms']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : ''; ?> /> SMS alerts</label>
        <label class="check-row"><input type="checkbox" name="notify_whatsapp" <?php echo isset($_POST['notify_whatsapp']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : ''; ?> /> WhatsApp alerts</label>
      </div>

      <div class="button-row">
        <button type="submit" class="btn btn-primary">Create Profile</button>
        <a href="login.php" class="btn btn-secondary">Already have an account?</a>
      </div>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
