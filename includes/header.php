<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = $page_title ?? 'Blood Saathi';
$page_description = $page_description ?? 'Blood Saathi connects blood donors, volunteers, and urgent requests through a clear frontend experience.';
$active_page = $active_page ?? '';
$flash = get_flash();
$user = current_user();
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="<?php echo e(trim($body_class . ' page-' . $active_page)); ?>">
  <div class="site-shell">
    <header class="site-header">
      <div class="topbar">
        <p>24/7 support for urgent blood requests</p>
        <a href="contact.php">Emergency helpline: +91 98765 43210</a>
      </div>

      <nav class="navbar">
        <a href="index.php" class="brand" aria-label="Blood Saathi Home">
          <img src="Blood_Saathi.png" alt="Blood Saathi logo" />
          <span>
            <strong>Blood Saathi</strong>
            <small>Need support, quickly</small>
          </span>
        </a>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-menu">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <div class="nav-panel" id="site-menu">
          <ul class="nav-links">
            <?php if ($active_page === 'home'): ?>
              <li><a href="index.php" class="<?php echo $active_page === 'home' ? 'active' : ''; ?>">Home</a></li>
              <li><a href="about.php" class="<?php echo $active_page === 'about' ? 'active' : ''; ?>">About Us</a></li>
              <li><a href="find-blood.php" class="<?php echo $active_page === 'find-blood' ? 'active' : ''; ?>">Find Blood</a></li>
              <li><a href="contact.php" class="<?php echo $active_page === 'contact' ? 'active' : ''; ?>">Contact Us</a></li>
            <?php else: ?>
              <li><a href="index.php" class="<?php echo $active_page === 'home' ? 'active' : ''; ?>">Home</a></li>
              <li><a href="find-blood.php" class="<?php echo $active_page === 'find-blood' ? 'active' : ''; ?>">Find Donor</a></li>
              <li><a href="become-donor.php" class="<?php echo $active_page === 'donor' ? 'active' : ''; ?>">Become a Donor</a></li>
              <li><a href="blood-groups.php" class="<?php echo $active_page === 'blood-groups' ? 'active' : ''; ?>">Blood Groups</a></li>
              <li><a href="how-it-works.php" class="<?php echo $active_page === 'how-it-works' ? 'active' : ''; ?>">How It Works</a></li>
              <li><a href="emergency-help.php" class="<?php echo $active_page === 'emergency-help' ? 'active' : ''; ?>">Emergency Help</a></li>
              <li><a href="faq.php" class="<?php echo $active_page === 'faq' ? 'active' : ''; ?>">FAQs</a></li>
            <?php endif; ?>
          </ul>
          <div class="nav-actions">
            <?php
            $dashboardHref = $user ? dashboard_path_for_role($user['role']) : 'donor-dashboard.php';
            ?>
            <?php if ($user): ?>
              <?php if ($active_page !== 'home'): ?>
                <a href="<?php echo e($dashboardHref); ?>" class="btn btn-secondary <?php echo $active_page === 'dashboard' || $active_page === 'admin-dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <span class="nav-user"><?php echo e($user['full_name']); ?></span>
                <a href="logout.php" class="btn btn-primary nav-cta">Log Out</a>
              <?php else: ?>
                <a href="<?php echo e($dashboardHref); ?>" class="btn btn-primary nav-cta">Dashboard</a>
              <?php endif; ?>
            <?php else: ?>
              <a href="login.php" class="btn btn-primary nav-cta <?php echo $active_page === 'login' ? 'active' : ''; ?>">Log In</a>
            <?php endif; ?>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <?php if ($database_error): ?>
        <div class="page-alert warning">
          <strong>Database setup required.</strong>
          <span><?php echo e($database_error); ?> Import [schema.sql](/C:/New%20folder/htdocs/PHP%20Project/PHP%20Project/project/files/database/schema.sql) into MySQL and configure `DB_*` environment variables.</span>
        </div>
      <?php endif; ?>

      <?php if ($flash): ?>
        <div class="page-alert <?php echo e($flash['type']); ?>">
          <span><?php echo e($flash['message']); ?></span>
        </div>
      <?php endif; ?>
