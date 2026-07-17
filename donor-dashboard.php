<?php
require_once __DIR__ . '/includes/bootstrap.php';

require_login('donor');

$user = current_user();
$page_title = 'Donor Dashboard | Blood Saathi';
$page_description = 'M';
$active_page = 'dashboard';
$errors = [];
$today = date('Y-m-d');

$donorId = (int) ($user['donor_id'] ?? 0);
$donor = null;

if ($donorId > 0) {
    $donor = db_one(
        'SELECT d.*, p.email_enabled, p.sms_enabled, p.whatsapp_enabled
         FROM donors d
         LEFT JOIN donor_notification_preferences p ON p.donor_id = d.id
         WHERE d.id = :id LIMIT 1',
        ['id' => $donorId]
    );
}

if (!$donor) {
    logout_user();
    set_flash('error', 'Your donor profile could not be found. Please log in again.');
    redirect_to('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $action = post_value('action');

    if ($action === 'profile') {
        $city = post_value('city');
        $availability = post_value('availability');
        $contactPreference = post_value('contact_preference');
        $lastDonationDate = post_value('last_donation_date') ?: null;
        $notes = post_value('notes');

        if ($city === '' || $availability === '' || $contactPreference === '') {
            $errors[] = 'Please complete the donor profile fields before saving.';
        }

        if ($lastDonationDate !== null && $lastDonationDate > $today) {
            $errors[] = 'Last donation date cannot be later than today.';
        }

        if (!$errors) {
            $statement = $pdo->prepare(
                'UPDATE donors
                 SET city = :city,
                     availability = :availability,
                     contact_preference = :contact_preference,
                     last_donation_date = :last_donation_date,
                     notes = :notes,
                     status = :status
                 WHERE id = :id'
            );
            $statement->execute([
                'city' => $city,
                'availability' => $availability,
                'contact_preference' => $contactPreference,
                'last_donation_date' => $lastDonationDate,
                'notes' => $notes !== '' ? $notes : null,
                'status' => str_contains(strtolower($availability), 'unavailable') ? 'unavailable' : 'available',
                'id' => $donorId,
            ]);

            set_flash('success', 'Your donor profile has been updated.');
            redirect_to('donor-dashboard.php');
        }
    }

    if ($action === 'notifications') {
        $statement = $pdo->prepare(
            'UPDATE donor_notification_preferences
             SET email_enabled = :email_enabled,
                 sms_enabled = :sms_enabled,
                 whatsapp_enabled = :whatsapp_enabled
             WHERE donor_id = :donor_id'
        );
        $statement->execute([
            'email_enabled' => isset($_POST['email_enabled']) ? 1 : 0,
            'sms_enabled' => isset($_POST['sms_enabled']) ? 1 : 0,
            'whatsapp_enabled' => isset($_POST['whatsapp_enabled']) ? 1 : 0,
            'donor_id' => $donorId,
        ]);

        set_flash('success', 'Notification preferences updated.');
        redirect_to('donor-dashboard.php');
    }
}

$donor = db_one(
    'SELECT d.*, p.email_enabled, p.sms_enabled, p.whatsapp_enabled
     FROM donors d
     LEFT JOIN donor_notification_preferences p ON p.donor_id = d.id
     WHERE d.id = :id LIMIT 1',
    ['id' => $donorId]
);

$openRequestCount = db_one('SELECT COUNT(*) AS total FROM blood_requests WHERE status = :status', ['status' => 'open']);

include 'includes/header.php';
?>

<section class="page-hero">
  <p class="eyebrow">Donor dashboard</p>
  <h1>Stay ready to help during urgent blood emergencies.</h1>
  <p>
    The Blood Saathi donor dashboard allows volunteers to manage their profile, update availability status, and receive emergency blood request alerts quickly and easily.
  </p>
</section>

<section class="section">
  <div class="metric-row">
    <article class="dashboard-metric">
      <strong><?php echo e($donor['blood_group']); ?></strong>
      <p>Registered blood group</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e($donor['city']); ?></strong>
      <p>Active city</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e($donor['availability']); ?></strong>
      <p>Current availability</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e($donor['contact_preference']); ?></strong>
      <p>Preferred contact</p>
    </article>
  </div>
</section>

<section class="section">
  <?php if ($errors): ?>
    <div class="page-alert error">
      <span><?php echo e(implode(' ', $errors)); ?></span>
    </div>
  <?php endif; ?>

  <div class="dashboard-grid">
    <form class="dashboard-card" method="post" action="donor-dashboard.php">
      <input type="hidden" name="action" value="profile" />
      <p class="eyebrow">Profile updates</p>
      <h3>Update donor information</h3>
      <div class="form-grid">
        <div class="form-group">
          <label for="dash-city">City</label>
          <input id="dash-city" name="city" type="text" value="<?php echo e($donor['city']); ?>" />
        </div>
        <div class="form-group">
          <label for="dash-availability">Availability</label>
          <select id="dash-availability" name="availability">
            <?php foreach (['Available now', 'Available today', 'Available this week', 'Available weekends only', 'Temporarily unavailable'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo $donor['availability'] === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="dash-contact">Contact preference</label>
          <select id="dash-contact" name="contact_preference">
            <?php foreach (['Phone call', 'SMS', 'WhatsApp', 'Email'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo $donor['contact_preference'] === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="dash-last-donation">Last donation</label>
          <input id="dash-last-donation" name="last_donation_date" type="date" max="<?php echo e($today); ?>" value="<?php echo e((string) ($donor['last_donation_date'] ?? '')); ?>" />
        </div>
        <div class="form-group full">
          <label for="dash-notes">Notes</label>
          <textarea id="dash-notes" name="notes"><?php echo e((string) ($donor['notes'] ?? '')); ?></textarea>
        </div>
      </div>
      <div class="button-row">
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>

    <div class="dashboard-card">
      <p class="eyebrow">Account snapshot</p>
      <h3>Availability and request summary</h3>
      <ul class="list-clean">
        <li>Signed in as: <?php echo e($user['email']); ?></li>
        <li>Donor status: <?php echo e($donor['status']); ?></li>
        <li>Verification: <?php echo (int) $donor['is_verified'] === 1 ? 'Verified profile' : 'Awaiting verification'; ?></li>
        <li>Open requests in the system: <?php echo e((string) ($openRequestCount['total'] ?? '0')); ?></li>
      </ul>
    </div>
  </div>
</section>

<section class="section">
  <div class="notification-grid">
    <form class="notification-card" method="post" action="donor-dashboard.php">
      <input type="hidden" name="action" value="notifications" />
      <p class="eyebrow">Notifications</p>
      <h3>Email, SMS, and WhatsApp alert preferences</h3>
      <div class="checkbox-grid">
        <label class="check-row"><input type="checkbox" name="email_enabled" <?php echo (int) ($donor['email_enabled'] ?? 0) === 1 ? 'checked' : ''; ?> /> Email alerts</label>
        <label class="check-row"><input type="checkbox" name="sms_enabled" <?php echo (int) ($donor['sms_enabled'] ?? 0) === 1 ? 'checked' : ''; ?> /> SMS alerts</label>
        <label class="check-row"><input type="checkbox" name="whatsapp_enabled" <?php echo (int) ($donor['whatsapp_enabled'] ?? 0) === 1 ? 'checked' : ''; ?> /> WhatsApp alerts</label>
      </div>
      <div class="button-row">
        <button type="submit" class="btn btn-primary">Save Preferences</button>
      </div>
    </form>

    <div class="notification-card">
      <p class="eyebrow">Notifications</p>
      <h3>Donors can choose how they want to receive emergency alerts through Email, SMS, or WhatsApp so they can respond quickly when blood is urgently needed.</h3>
      <p>
        Blood Saathi securely saves notification preferences and donor settings to keep emergency communication organized and ready for future real-time alert integration.
      </p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
