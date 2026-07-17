<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect_to(dashboard_path_for_role(current_user()['role']));
}

$page_title = 'Portal Login | Blood Saathi';
$page_description = 'Login page for donors and coordinators.';
$active_page = 'login';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $email = strtolower(post_value('email'));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errors[] = 'Please enter a valid email and password.';
    } else {
        $user = db_one('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Invalid email or password.';
        } else {
            login_user($user);
            set_flash('success', 'Welcome back, ' . $user['full_name'] . '.');
            redirect_to(dashboard_path_for_role($user['role']));
        }
    }
}

include 'includes/header.php';
?>

<section class="login-shell">
  <div class="login-layout">
    <div class="login-panel">
      <p class="eyebrow">Secure access</p>
      <h1>Log in to manage donors, alerts, or your donor profile.</h1>
      <ul class="login-benefits">
        <li>Review donor profile details</li>
        <li>Update availability and contact preferences</li>
        <li>Manage notification settings</li>
        <li>Prepare for staff workflows later</li>
      </ul>
    </div>

    <form class="form-card" method="post" action="login.php">
      <p class="eyebrow">Portal login</p>
      <h2>Sign in</h2>

      <?php if ($errors): ?>
        <div class="page-alert error form-inline-alert">
          <span><?php echo e(implode(' ', $errors)); ?></span>
        </div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="form-group full">
          <label for="login-email">Email</label>
          <input id="login-email" name="email" type="email" value="<?php echo old('email'); ?>" placeholder="you@example.com" required />
        </div>
        <div class="form-group full">
          <label for="login-password">Password</label>
          <input id="login-password" name="password" type="password" placeholder="Enter your password" required />
        </div>
      </div>
      <div class="button-row">
        <button type="submit" class="btn btn-primary">Log In</button>
        <a href="become-donor.php" class="btn btn-secondary">Create Donor Account</a>
      </div>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
