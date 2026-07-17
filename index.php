<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Blood Saathi | जरूरत पर साथ';
$page_description = 'Blood Saathi home page';
$active_page = 'home';
$body_class = 'home-ui';

include 'includes/header.php';
?>

<section class="hero">
  <article class="hero-card">
    <img src="heart_logo.png" alt="Blood Saathi support graphic" />
  </article>

  <div>
    <h1>Save <span class="accent">Life</span> Donate <span class="accent">Blood</span></h1>
    <p>
      Donate blood and be a hero - your generosity can<br>
      save lives.<br>
      Join a proud tradition that has made a difference<br>
      for centuries.<br>
      Make an impact today with a simple, lifesaving<br>
      act
    </p>
    <div class="hero-actions">
      <a href="find-blood.php" class="btn btn-secondary">Donate Now</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
