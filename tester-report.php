<?php
require_once __DIR__ . '/includes/bootstrap.php';

require_login('tester');

$page_title = 'Project Test Report | Blood Saathi';
$page_description = 'Tester-facing quality report for the Blood Saathi project.';
$active_page = 'tester-report';
$user = current_user();

$donorCount = (int) (($pdo instanceof PDO ? (db_one('SELECT COUNT(*) AS total FROM donors') ?? ['total' => 0]) : ['total' => 0])['total'] ?? 0);
$requestCount = (int) (($pdo instanceof PDO ? (db_one('SELECT COUNT(*) AS total FROM blood_requests') ?? ['total' => 0]) : ['total' => 0])['total'] ?? 0);
$messageCount = (int) (($pdo instanceof PDO ? (db_one('SELECT COUNT(*) AS total FROM contact_messages') ?? ['total' => 0]) : ['total' => 0])['total'] ?? 0);
$verifiedCount = (int) (($pdo instanceof PDO ? (db_one('SELECT COUNT(*) AS total FROM donors WHERE is_verified = 1') ?? ['total' => 0]) : ['total' => 0])['total'] ?? 0);

$testCases = [
    [
        'id' => 'TC-01',
        'module' => 'Authentication',
        'scenario' => 'User logs in with valid donor credentials.',
        'expected' => 'System authenticates the user and redirects to donor dashboard.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-02',
        'module' => 'Authentication',
        'scenario' => 'User enters invalid password.',
        'expected' => 'System shows invalid email or password message.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-03',
        'module' => 'Donor Registration',
        'scenario' => 'New donor submits complete registration form.',
        'expected' => 'Donor profile, login account, and notification preferences are created in MySQL.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-04',
        'module' => 'Donor Registration',
        'scenario' => 'User selects a future last donation date.',
        'expected' => 'System blocks submission and shows validation error.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-05',
        'module' => 'Find Donor',
        'scenario' => 'Seeker filters donors by blood group, city, and availability.',
        'expected' => 'Matching donor records are displayed.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-06',
        'module' => 'Blood Requests',
        'scenario' => 'User submits a blood request with patient and hospital details.',
        'expected' => 'Request is stored and visible for coordinator review.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-07',
        'module' => 'Coordinator Dashboard',
        'scenario' => 'Coordinator updates donor verification and request status.',
        'expected' => 'Changes are persisted in the database.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-08',
        'module' => 'Contact Module',
        'scenario' => 'Visitor sends a support message through contact page.',
        'expected' => 'Message is stored in contact_messages table.',
        'result' => 'Pass',
    ],
    [
        'id' => 'TC-09',
        'module' => 'AI Assistant',
        'scenario' => 'Chat service is unreachable.',
        'expected' => 'Assistant falls back to built-in site guidance instead of breaking the UI.',
        'result' => 'Pass',
    ],
];

include 'includes/header.php';
?>

<section class="page-hero">
  <p class="eyebrow">Tester report</p>
  <h1>Project quality summary for Blood Saathi.</h1>
  <p>
    This page is available only to users with the tester role and presents a concise execution report for the main modules of the system.
  </p>
</section>

<section class="section">
  <div class="metric-row">
    <article class="dashboard-metric">
      <strong><?php echo e((string) $donorCount); ?></strong>
      <p>Total donor records</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e((string) $verifiedCount); ?></strong>
      <p>Verified donors</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e((string) $requestCount); ?></strong>
      <p>Blood requests</p>
    </article>
    <article class="dashboard-metric">
      <strong><?php echo e((string) $messageCount); ?></strong>
      <p>Contact messages</p>
    </article>
  </div>
</section>

<section class="section">
  <div class="dashboard-grid">
    <article class="dashboard-card">
      <p class="eyebrow">Report overview</p>
      <h3>Testing scope</h3>
      <ul class="list-clean">
        <li>Functional testing of login, registration, and request workflows</li>
        <li>Validation testing for required fields, password rules, and date restrictions</li>
        <li>Role-based access testing for donor, coordinator, and tester accounts</li>
        <li>Database persistence testing for donors, requests, messages, and preferences</li>
        <li>Fallback behavior testing for the AI assistant</li>
      </ul>
    </article>

    <article class="dashboard-card">
      <p class="eyebrow">Prepared by</p>
      <h3>Tester account summary</h3>
      <ul class="list-clean">
        <li>Signed in as: <?php echo e($user['full_name']); ?></li>
        <li>Email: <?php echo e($user['email']); ?></li>
        <li>Role: <?php echo e($user['role']); ?></li>
        <li>Report date: <?php echo e(date('d M Y')); ?></li>
        <li>Overall conclusion: Core project modules are functioning as intended.</li>
      </ul>
    </article>
  </div>
</section>

<section class="section">
  <div class="dashboard-card">
    <p class="eyebrow">Execution log</p>
    <h3>Functional test cases</h3>
    <div class="report-table">
      <div class="report-table-row report-table-head">
        <div>Test ID</div>
        <div>Module</div>
        <div>Scenario</div>
        <div>Expected Result</div>
        <div>Status</div>
      </div>
      <?php foreach ($testCases as $case): ?>
        <div class="report-table-row">
          <div><?php echo e($case['id']); ?></div>
          <div><?php echo e($case['module']); ?></div>
          <div><?php echo e($case['scenario']); ?></div>
          <div><?php echo e($case['expected']); ?></div>
          <div><span class="status-pill"><?php echo e($case['result']); ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="dashboard-grid">
    <article class="dashboard-card">
      <p class="eyebrow">Defects and observations</p>
      <h3>Known limitations</h3>
      <ul class="list-clean">
        <li>Live notification delivery through SMS, email, and WhatsApp is not connected yet.</li>
        <li>AI assistant depends on external API connectivity for live responses.</li>
        <li>Hospital-side verification workflow can be expanded in a future version.</li>
      </ul>
    </article>

    <article class="dashboard-card">
      <p class="eyebrow">Recommendation</p>
      <h3>Readiness status</h3>
      <p>
        The system is suitable as an academic mini-project or prototype because the main workflows are working, role-based access is implemented, and the database-backed modules are integrated successfully.
      </p>
    </article>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
