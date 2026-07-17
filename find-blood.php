<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Find Donor / Request Blood | Blood Saathi';
$page_description = 'M';
$active_page = 'find-blood';
$errors = [];

$searchCity = trim((string) ($_GET['city'] ?? ''));
$searchGroup = trim((string) ($_GET['blood_group'] ?? ''));
$searchAvailability = trim((string) ($_GET['availability'] ?? ''));
$searchContact = trim((string) ($_GET['contact_preference'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $patientName = post_value('patient_name');
    $bloodGroup = post_value('blood_group');
    $requiredUnits = (int) ($_POST['required_units'] ?? 0);
    $urgency = post_value('urgency');
    $hospital = post_value('hospital');
    $city = post_value('city');
    $notes = post_value('notes');

    if ($patientName === '' || $bloodGroup === '' || $requiredUnits < 1 || $urgency === '' || $hospital === '' || $city === '') {
        $errors[] = 'Please complete all required blood request fields.';
    }

    if (!$errors) {
        $statement = $pdo->prepare(
            'INSERT INTO blood_requests (patient_name, blood_group, required_units, urgency, hospital, city, notes)
             VALUES (:patient_name, :blood_group, :required_units, :urgency, :hospital, :city, :notes)'
        );
        $statement->execute([
            'patient_name' => $patientName,
            'blood_group' => $bloodGroup,
            'required_units' => $requiredUnits,
            'urgency' => $urgency,
            'hospital' => $hospital,
            'city' => $city,
            'notes' => $notes !== '' ? $notes : null,
        ]);

        set_flash('success', 'Blood request saved successfully. You can now use the donor search to find matching profiles.');
        redirect_to('find-blood.php');
    }
}

$donorSql = 'SELECT * FROM donors WHERE 1=1';
$donorParams = [];

if ($searchCity !== '') {
    $donorSql .= ' AND city LIKE :city';
    $donorParams['city'] = '%' . $searchCity . '%';
}

if ($searchGroup !== '' && $searchGroup !== 'Any group') {
    $donorSql .= ' AND blood_group = :blood_group';
    $donorParams['blood_group'] = $searchGroup;
}

if ($searchAvailability !== '') {
    $donorSql .= ' AND availability = :availability';
    $donorParams['availability'] = $searchAvailability;
}

if ($searchContact !== '') {
    $donorSql .= ' AND contact_preference = :contact_preference';
    $donorParams['contact_preference'] = $searchContact;
}

$donorSql .= ' ORDER BY is_verified DESC, created_at DESC LIMIT 12';
$donors = db_all($donorSql, $donorParams);

include 'includes/header.php';
?>

<section class="page-hero">
  <p class="eyebrow">Find donor / request blood</p>
  <h1>Find nearby donors quickly during critical situations.</h1>
  <p>
    Blood Saathi helps patients and families search for donors based on city, blood group, and availability so they can connect with the right people faster during emergencies.
  </p>
</section>

<section class="section">
  <form class="filter-card" method="get" action="find-blood.php">
    <p class="eyebrow">Search and filter system</p>
    <div class="search-toolbar">
      <div class="form-group">
        <label for="search-city">City</label>
        <input id="search-city" name="city" type="text" value="<?php echo e($searchCity); ?>" placeholder="New Delhi" />
      </div>
      <div class="form-group">
        <label for="search-group">Blood group</label>
        <select id="search-group" name="blood_group">
          <?php foreach (['Any group', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $option): ?>
            <option value="<?php echo e($option); ?>" <?php echo ($searchGroup === $option || ($searchGroup === '' && $option === 'Any group')) ? 'selected' : ''; ?>><?php echo e($option); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="search-availability">Availability</label>
        <select id="search-availability" name="availability">
          <option value="">Any availability</option>
          <?php foreach (['Available now', 'Available today', 'Available this week', 'Available weekends only', 'Temporarily unavailable'] as $option): ?>
            <option value="<?php echo e($option); ?>" <?php echo $searchAvailability === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="search-contact">Contact preference</label>
        <select id="search-contact" name="contact_preference">
          <option value="">Any contact route</option>
          <?php foreach (['Phone call', 'SMS', 'WhatsApp', 'Email'] as $option): ?>
            <option value="<?php echo e($option); ?>" <?php echo $searchContact === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Apply Filters</button>
    </div>

    <div class="search-results">
      <?php if ($donors): ?>
        <?php foreach ($donors as $donor): ?>
          <article class="result-card">
            <div>
              <h3><?php echo e($donor['full_name']); ?></h3>
              <p><?php echo e($donor['blood_group']); ?> donor, <?php echo e($donor['city']); ?></p>
            </div>
            <div class="result-meta">
              <span class="tag"><?php echo e($donor['availability']); ?></span>
              <span class="tag"><?php echo e($donor['contact_preference']); ?></span>
              <?php if (!empty($donor['last_donation_date'])): ?>
                <span class="tag">Last donation: <?php echo e($donor['last_donation_date']); ?></span>
              <?php endif; ?>
              <?php if ((int) $donor['is_verified'] === 1): ?>
                <span class="tag">Verified profile</span>
              <?php endif; ?>
            </div>
            <a href="contact.php" class="btn btn-secondary">Contact Support</a>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <article class="result-card">
          <div>
            <h3>No donors found</h3>
            <p>Try changing the city, blood group, or availability filters.</p>
          </div>
        </article>
      <?php endif; ?>
    </div>
  </form>
</section>

<section class="section" id="request-form">
  <div class="split-layout">
    <form class="form-card" method="post" action="find-blood.php">
      <p class="eyebrow">Blood request form</p>
      <h2>Raise a request</h2>

      <?php if ($errors): ?>
        <div class="page-alert error form-inline-alert">
          <span><?php echo e(implode(' ', $errors)); ?></span>
        </div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="form-group">
          <label for="patient-name">Patient name</label>
          <input id="patient-name" name="patient_name" type="text" value="<?php echo old('patient_name'); ?>" placeholder="Enter patient name" required />
        </div>
        <div class="form-group">
          <label for="blood-group">Blood group</label>
          <select id="blood-group" name="blood_group" required>
            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo old('blood_group', 'A+') === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="units-needed">Required units</label>
          <input id="units-needed" name="required_units" type="number" min="1" value="<?php echo old('required_units'); ?>" placeholder="2" required />
        </div>
        <div class="form-group">
          <label for="urgency">Urgency</label>
          <select id="urgency" name="urgency" required>
            <?php foreach (['Critical - within hours', 'Today', 'Within 24 hours', 'Planned procedure'] as $option): ?>
              <option value="<?php echo e($option); ?>" <?php echo old('urgency', 'Critical - within hours') === $option ? 'selected' : ''; ?>><?php echo e($option); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="hospital">Hospital</label>
          <input id="hospital" name="hospital" type="text" value="<?php echo old('hospital'); ?>" placeholder="Hospital / blood bank" required />
        </div>
        <div class="form-group">
          <label for="request-city">City</label>
          <input id="request-city" name="city" type="text" value="<?php echo old('city'); ?>" placeholder="New Delhi" required />
        </div>
        <div class="form-group full">
          <label for="request-notes">Patient details and notes</label>
          <textarea id="request-notes" name="notes" placeholder="Ward, attending doctor, contact person, and any details volunteers should know."><?php echo old('notes'); ?></textarea>
        </div>
      </div>
      <div class="button-row">
        <button type="submit" class="btn btn-primary">Save Request</button>
        <a href="emergency-help.php" class="btn btn-secondary">Emergency Instructions</a>
      </div>
    </form>

    <div class="info-stack">
      <article class="info-card">
        <h3>Before you submit</h3>
        <ul class="list-clean">
          <li>Confirm the exact blood group with the treating facility.</li>
          <li>Keep hospital name, patient name, and urgency details ready.</li>
          <li>Use emergency help if the requirement is immediate.</li>
        </ul>
      </article>
      <article class="info-card">
        <h3>Search filters included</h3>
        <p>City, blood group, donor availability, and contact preference are all part of the backend search now.</p>
      </article>
      <article class="info-card">
        <h3>Notifications later</h3>
        <p>This search and request flow is structured so staff alerts can be connected later.</p>
      </article>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
