<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - Blood Saathi</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --bg: #1c1c1c; --red: #cc0000; --red2: #e00000;
      --white: #ffffff; --gray: #aaaaaa; --gray2: #d0d0d0;
    }
    html { scroll-behavior: smooth; }
    body { background: var(--bg); color: var(--white); font-family: 'Roboto', sans-serif; min-height: 100vh; overflow-x: hidden; }

    /* NAVBAR */
    nav {
      display: flex; align-items: center; justify-content: space-between;
      padding: 16px 60px; background: var(--bg);
      border-bottom: 1px solid #2a2a2a; position: sticky; top: 0; z-index: 999;
    }
    .logo img { 
      height: 90px; 
      width: auto; 
      max-width: 150px;
      display: block;
      object-fit: contain;
      image-rendering: -webkit-optimize-contrast;
    }
    .nav-links { display: flex; align-items: center; gap: 38px; list-style: none; }
    .nav-links a {
      color: var(--white); text-decoration: none; font-size: 15px;
      font-weight: 400; letter-spacing: .3px; transition: color .2s; position: relative;
    }
    .nav-links a:hover { color: var(--red); }
    .nav-links .active::after {
      content: ''; position: absolute; bottom: -4px; left: 0; right: 0;
      height: 2px; background: var(--red); border-radius: 2px;
    }
    .nav-links .has-arrow { position: relative; }
    .nav-links .has-arrow .arrow { font-size: 11px; color: var(--gray); margin-left: 3px; }
    .btn-login {
      background: var(--red); color: var(--white); padding: 11px 30px;
      border-radius: 6px; font-size: 15px; font-weight: 600; text-decoration: none;
      transition: background .2s, transform .15s; letter-spacing: .3px;
    }
    .btn-login:hover { background: var(--red2); transform: translateY(-1px); }

    /* CONTACT HERO */
    .contact-hero {
      text-align: center; max-width: 1180px; margin: 0 auto; padding: 120px 60px;
    }
    .contact-hero-title { 
      font-size: 58px; font-weight: 900; line-height: 1.08; 
      margin-bottom: 26px; letter-spacing: -1px;
    }
    .contact-hero-title .red { color: var(--red); }
    .contact-hero-desc {
      font-family: 'Poppins', sans-serif; font-size: 18px; color: var(--gray2);
      line-height: 1.7; margin-bottom: 60px; max-width: 600px; margin-left: auto; margin-right: auto;
    }

    /* CONTACT INFO */
    .section {
      max-width: 1180px; margin: 0 auto; padding: 90px 60px;
    }
    .section-title {
      font-size: 42px; font-weight: 900; text-align: center;
      margin-bottom: 60px; letter-spacing: -0.5px;
    }
    .section-title .red { color: var(--red); }
    .contact-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 40px;
    }
    .contact-card {
      background: rgba(255,255,255,0.03); padding: 40px 32px;
      border-radius: 12px; border: 1px solid rgba(255,255,255,0.08);
      text-align: center; transition: transform 0.3s, box-shadow 0.3s;
    }
    .contact-card:hover {
      transform: translateY(-8px); box-shadow: 0 20px 40px rgba(204,0,0,0.1);
    }
    .contact-icon { font-size: 56px; color: var(--red); margin-bottom: 20px; }
    .contact-card h3 { font-size: 22px; font-weight: 700; margin-bottom: 12px; }
    .contact-card p { color: var(--gray2); line-height: 1.6; }

    /* FORM */
    .form-section {
      background: rgba(255,255,255,0.03); border-radius: 12px;
      padding: 60px; border: 1px solid rgba(255,255,255,0.08); margin-top: 40px;
    }
    .form-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }
    .form-group {
      display: flex; flex-direction: column;
    }
    .form-group label {
      font-weight: 500; margin-bottom: 8px; color: var(--white);
      font-size: 14px; letter-spacing: .3px;
    }
    .form-group input, .form-group textarea {
      background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
      color: var(--white); padding: 16px; border-radius: 8px;
      font-family: inherit; font-size: 16px; transition: border-color .2s, box-shadow .2s;
    }
    .form-group input:focus, .form-group textarea:focus {
      outline: none; border-color: var(--red); box-shadow: 0 0 0 3px rgba(204,0,0,0.1);
    }
    .form-group textarea { min-height: 140px; resize: vertical; }
    .btn-submit {
      background: var(--red); color: var(--white); border: none;
      padding: 18px 50px; border-radius: 8px; font-size: 16px; font-weight: 600;
      cursor: pointer; transition: background .2s, transform .15s;
      font-family: 'Roboto', sans-serif; letter-spacing: .4px; margin-top: 20px;
    }
    .btn-submit:hover { background: var(--red2); transform: translateY(-2px); }

    .divider { height: 1px; background: linear-gradient(to right, transparent, #444, transparent); margin: 0 60px; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
    .contact-hero-title { animation: fadeInUp 0.9s ease both; }
    .contact-hero-desc { animation: fadeInUp 0.9s 0.2s ease both; }
    .contact-card:nth-child(1) { animation: fadeInUp 0.9s 0.4s both; }
    .contact-card:nth-child(2) { animation: fadeInUp 0.9s 0.5s both; }
    .contact-card:nth-child(3) { animation: fadeInUp 0.9s 0.6s both; }

    @media (max-width: 900px) {
      nav, .contact-hero, .section, .form-section { padding-left: 24px; padding-right: 24px; }
      .logo img { height: 70px; max-width: 130px; }
      .contact-hero-title { font-size: 42px; }
      .section-title { font-size: 32px; }
      .form-section { padding: 40px 24px; }
      .form-grid { grid-template-columns: 1fr; }
      .divider { margin: 0 24px; }
    }
    @media (max-width: 600px) {
      .nav-links { display: none; }
      .contact-hero-title { font-size: 32px; }
    }
  </style>
</head>
<body>

<nav>
  <a href="index.html" class="logo">
    <img src="Blood_Saathi.png" alt="Blood Saathi Logo" />
  </a>
  <ul class="nav-links">
    <li><a href="index.html">Home</a></li>
    <li><a href="about.html">About Us</a></li>
    <li><a href="#">Find Blood</a></li>
    <li><a href="contact.html" class="active">Contact Us</a></li>
    <li><a href="#" class="has-arrow">Register Now <span class="arrow">▾</span></a></li>
  </ul>
  <a href="#" class="btn-login">Log In</a>
</nav>

<section class="contact-hero">
  <h1 class="contact-hero-title">
    Get In <span class="red">Touch</span>
  </h1>
  <p class="contact-hero-desc">
    Have questions about blood donation or need urgent assistance? 
    We're here to help. Reach out through any channel below.
  </p>
</section>

<div class="divider"></div>

<section class="section">
  <h2 class="section-title">Contact Information</h2>
  <div class="contact-grid">
    <div class="contact-card">
      <div class="contact-icon">📞</div>
      <h3>Phone</h3>
      <p>+977-1-XXXXXXX<br>Emergency: 102</p>
    </div>
    <div class="contact-card">
      <div class="contact-icon">✉️</div>
      <h3>Email</h3>
      <p>info@bloodsaathi.com<br>donate@bloodsaathi.com</p>
    </div>
    <div class="contact-card">
      <div class="contact-icon">📍</div>
      <h3>Address</h3>
      <p>Blood Saathi HQ<br>Kathmandu, Nepal<br>Mon-Sat: 9AM-6PM</p>
    </div>
  </div>

  <div class="form-section">
    <div class="form-grid">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" required>
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone">
      </div>
      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" placeholder="Tell us how we can help..." required></textarea>
      </div>
    </div>
    <button class="btn-submit">Send Message</button>
  </div>
</section>

</body>
</html>

