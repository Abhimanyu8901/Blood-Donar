<?php
require_once __DIR__ . '/includes/bootstrap.php';

require_login('coordinator');

$page_title = 'Coordinator Dashboard | Blood Saathi';
$page_description = 'Coordinator dashboard for donor verification and blood request management.';
$active_page = 'admin-dashboard';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $action = post_value('action');

    if ($action === 'verify_donor') {
        $donorId = (int) ($_POST['donor_id'] ?? 0);
        $isVerified = isset($_POST['is_verified']) ? 1 : 0;

        $statement = $pdo->prepare('UPDATE donors SET is_verified = :is_verified WHERE id = :id');
        $statement->execute([
            'is_verified' => $isVerified,
            'id' => $donorId,
        ]);

        set_flash('success', 'Donor verification status updated.');
        redirect_to('admin-dashboard.php');
    }

    if ($action === 'update_request_status') {
        $requestId = (int) ($_POST['request_id'] ?? 0);
        $status = post_value('status');

        if (!in_array($status, ['open', 'in_progress', 'fulfilled', 'closed'], true)) {
            $errors[] = 'Invalid request status.';
        } else {
            $statement = $pdo->prepare('UPDATE blood_requests SET status = :status WHERE id = :id');
            $statement->execute([
                'status' => $status,
                'id' => $requestId,
            ]);

            set_flash('success', 'Blood request status updated.');
            redirect_to('admin-dashboard.php');
        }
    }
}

$counts = [
    'donors' => db_one('SELECT COUNT(*) AS total FROM donors'),
    'verified' => db_one('SELECT COUNT(*) AS total FROM donors WHERE is_verified = 1'),
    'open_requests' => db_one("SELECT COUNT(*) AS total FROM blood_requests WHERE status IN ('open', 'in_progress')"),
    'messages' => db_one('SELECT COUNT(*) AS total FROM contact_messages'),
];

$recentDonors = db_all('SELECT * FROM donors ORDER BY created_at DESC LIMIT 8');
$recentRequests = db_all('SELECT * FROM blood_requests ORDER BY created_at DESC LIMIT 8');
$recentMessages = db_all('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5');

include 'includes/header.php';
?>

<section class="page-hero">
  <p class="eyebrow">Coordinator dashboard</p>
  <h1>Review donors, verify profiles, and update blood request progress.</h1>
  <p></p>
</section>

<section class="section">
  <?php if ($errors): ?>
    <div class="page-alert error">
      <span><?php echo e(implode(' ', $errors)); ?></span>
    </div>
  <?php endif; ?>

  <div class="metric-row">
    <article class="dashboard-metric">
      <strong><?php echo e((string) ($counts['donors']['total'] ?? 0)); ?></strong>
      <p>Total donors</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e((string) ($counts['verified']['total'] ?? 0)); ?></strong>
      <p>Verified donor profiles</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e((string) ($counts['open_requests']['total'] ?? 0)); ?></strong>
      <p>Open or active requests</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e((string) ($counts['messages']['total'] ?? 0)); ?></strong>
      <p>Saved support messages</p>
    </article>
  </div>
</section>

<section class="section">
  <div class="dashboard-grid">
    <div class="dashboard-card">
      <p class="eyebrow">Donor verification</p>
      <h3>Recent donor records</h3>
      <div class="admin-list">
        <?php foreach ($recentDonors as $donor): ?>
          <form class="admin-item" method="post" action="admin-dashboard.php">
            <input type="hidden" name="action" value="verify_donor" />
            <input type="hidden" name="donor_id" value="<?php echo e((string) $donor['id']); ?>" />
            <div>
              <strong><?php echo e($donor['full_name']); ?></strong>
              <p class="dashboard-meta"><?php echo e($donor['blood_group']); ?>, <?php echo e($donor['city']); ?>, <?php echo e($donor['availability']); ?></p>
            </div>
            <label class="check-row">
              <input type="checkbox" name="is_verified" <?php echo (int) $donor['is_verified'] === 1 ? 'checked' : ''; ?> />
              Verified
            </label>
            <button type="submit" class="btn btn-secondary">Save</button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="dashboard-card">
      <p class="eyebrow">Request operations</p>
      <h3>Latest blood requests</h3>
      <div class="admin-list">
        <?php foreach ($recentRequests as $request): ?>
          <form class="admin-item" method="post" action="admin-dashboard.php">
            <input type="hidden" name="action" value="update_request_status" />
            <input type="hidden" name="request_id" value="<?php echo e((string) $request['id']); ?>" />
            <div>
              <strong><?php echo e($request['patient_name']); ?></strong>
              <p class="dashboard-meta"><?php echo e($request['blood_group']); ?>, <?php echo e($request['hospital']); ?>, <?php echo e($request['city']); ?></p>
            </div>
            <select name="status">
              <?php foreach (['open', 'in_progress', 'fulfilled', 'closed'] as $status): ?>
                <option value="<?php echo e($status); ?>" <?php echo $request['status'] === $status ? 'selected' : ''; ?>><?php echo e($status); ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Update</button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="notification-card">
    <p class="eyebrow">Support inbox preview</p>
    <h3>Recent contact messages</h3>
    <div class="admin-list">
      <?php foreach ($recentMessages as $message): ?>
        <article class="admin-item static">
          <div>
            <strong><?php echo e($message['full_name']); ?></strong>
            <p class="dashboard-meta"><?php echo e($message['reason']); ?> | <?php echo e($message['email']); ?></p>
          </div>
          <p><?php echo e($message['message']); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
