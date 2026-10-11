<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Yeshwant Mahavidyalaya, Nanded – Official Website</title>
  <!-- Font Awesome for clean icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    /* ----- RESET & BASE ----- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
    }

    body {
      background: #f5f9ff;
      color: #1e2a41;
      line-height: 1.5;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* ----- TOP HEADER (professional bar) ----- */
    .top-header {
      background: #00337a;
      color: rgba(255, 255, 255, 0.9);
      padding: 6px 5%;
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
      letter-spacing: 0.3px;
      border-bottom: 2px solid #ffc107;
      flex-wrap: wrap;
    }

    .top-header i {
      margin-right: 6px;
      color: #ffc107;
    }

    /* ----- NAVBAR (sticky, glass-morphism) ----- */
    .navbar {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(4px);
      min-height: 80px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 4%;
      box-shadow: 0 6px 24px rgba(0, 40, 80, 0.10);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(0, 70, 150, 0.08);
      transition: all 0.2s;
    }

    .logo-area {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .logo {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #004b91;
      box-shadow: 0 2px 8px rgba(0, 60, 120, 0.2);
      background: white;
      padding: 2px;
    }

    .college-name {
      color: #00337a;
      font-size: 1.2rem;
      font-weight: 700;
      line-height: 1.2;
    }

    .college-name small {
      display: block;
      font-size: 0.7rem;
      font-weight: 500;
      color: #3b5f7a;
      letter-spacing: 0.3px;
      margin-top: 2px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 0px;
      list-style: none;
    }

    .nav-links a {
      text-decoration: none;
      color: #174a7c;
      font-size: 0.82rem;
      font-weight: 600;
      padding: 10px 12px;
      border-radius: 6px;
      transition: 0.25s ease;
      display: inline-block;
      letter-spacing: 0.2px;
    }

    .nav-links a:hover {
      background: #004b91;
      color: white;
      box-shadow: 0 4px 10px rgba(0, 75, 145, 0.25);
      transform: translateY(-1px);
    }

    /* ----- IMAGE SCROLL (cinematic) ----- */
    .image-scroll {
      width: 100%;
      overflow: hidden;
      background: #e1edfa;
      padding: 20px 0;
      border-bottom: 1px solid rgba(0, 60, 120, 0.08);
    }

    .image-track {
      display: flex;
      gap: 20px;
      width: max-content;
      animation: collegeScroll 40s linear infinite;
    }

    .image-track:hover {
      animation-play-state: paused;
    }

    .image-track img {
      width: 420px;
      height: 230px;
      object-fit: cover;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 50, 100, 0.20);
      transition: 0.3s;
      border: 2px solid white;
    }

    .image-track img:hover {
      transform: scale(1.02);
      border-color: #004b91;
    }

    @keyframes collegeScroll {
      0% {
        transform: translateX(0);
      }

      100% {
        transform: translateX(-50%);
      }
    }

    /* ----- HERO (modern, clean) ----- */
    .hero {
      text-align: center;
      padding: 50px 20px 45px;
      background: linear-gradient(145deg, #ffffff 0%, #ecf5ff 100%);
      border-bottom: 1px solid #d8e6f5;
    }

    .hero h1 {
      font-size: 2.8rem;
      color: #00337a;
      font-weight: 700;
      letter-spacing: -0.5px;
      margin-bottom: 6px;
    }

    .hero h2 {
      font-weight: 400;
      font-size: 1.4rem;
      color: #1f3a5e;
      margin-bottom: 8px;
    }

    .hero p {
      color: #2c4a6e;
      font-size: 1rem;
      margin: 4px 0;
    }

    .grade {
      background: #00337a;
      color: white !important;
      display: inline-block;
      padding: 8px 28px;
      border-radius: 40px;
      font-weight: 600;
      margin-top: 16px;
      font-size: 1rem;
      box-shadow: 0 6px 14px rgba(0, 51, 122, 0.25);
    }

    .grade i {
      margin-right: 8px;
      color: #ffc107;
    }

    .buttons {
      margin-top: 30px;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 12px;
    }

    .btn {
      display: inline-block;
      padding: 14px 34px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: 0.25s;
      background: #00337a;
      color: white;
      box-shadow: 0 8px 18px rgba(0, 51, 122, 0.20);
      border: none;
      letter-spacing: 0.3px;
    }

    .btn i {
      margin-right: 8px;
    }

    .btn:hover {
      background: #00245a;
      transform: translateY(-3px);
      box-shadow: 0 12px 24px rgba(0, 51, 122, 0.30);
    }

    .btn.register {
      background: white;
      color: #00337a;
      border: 2px solid #00337a;
      box-shadow: none;
    }

    .btn.register:hover {
      background: #00337a;
      color: white;
      box-shadow: 0 8px 20px rgba(0, 51, 122, 0.25);
    }

    /* ----- SECTION (cards) ----- */
    .section {
      padding: 60px 6%;
    }

    .section-title {
      text-align: center;
      color: #00337a;
      font-size: 2.1rem;
      font-weight: 600;
      margin-bottom: 40px;
      position: relative;
    }

    .section-title:after {
      content: '';
      display: block;
      width: 80px;
      height: 4px;
      background: #ffc107;
      margin: 12px auto 0;
      border-radius: 4px;
    }

    .about-box {
      max-width: 1100px;
      margin: 0 auto;
      background: white;
      padding: 40px 45px;
      border-radius: 24px;
      box-shadow: 0 12px 30px rgba(0, 60, 120, 0.08);
      text-align: center;
      border: 1px solid rgba(0, 60, 120, 0.05);
    }

    .about-box h3 {
      color: #00337a;
      font-size: 1.6rem;
      margin-bottom: 16px;
    }

    .about-box p {
      color: #2c405c;
      line-height: 1.8;
      font-size: 1.05rem;
    }

    /* ----- STATS (achievement cards) ----- */
    .stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 25px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .stat-card {
      background: white;
      text-align: center;
      padding: 30px 12px;
      border-radius: 20px;
      border-top: 5px solid #00337a;
      box-shadow: 0 8px 22px rgba(0, 60, 120, 0.08);
      transition: 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 32px rgba(0, 60, 120, 0.14);
    }

    .stat-card h2 {
      color: #00337a;
      font-size: 2.6rem;
      font-weight: 700;
      letter-spacing: -1px;
    }

    .stat-card p {
      color: #2f4a6e;
      font-weight: 500;
      margin-top: 8px;
    }

    /* ----- PRINCIPAL (professional) ----- */
    .principal {
      background: #e5f0fe;
    }

    .principal-box {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 240px 1fr;
      gap: 40px;
      align-items: center;
      background: white;
      padding: 35px 45px;
      border-radius: 28px;
      box-shadow: 0 14px 34px rgba(0, 60, 120, 0.08);
      border: 1px solid rgba(0, 60, 120, 0.04);
    }

    .principal-img {
      width: 200px;
      height: 230px;
      object-fit: cover;
      border-radius: 18px;
      border: 4px solid #00337a;
      box-shadow: 0 8px 20px rgba(0, 51, 122, 0.20);
    }

    .principal-box h3 {
      color: #00337a;
      font-size: 1.8rem;
      margin-bottom: 14px;
    }

    .principal-box p {
      color: #2c405c;
      line-height: 1.7;
      font-size: 1.02rem;
    }

    .pdf-btn {
      display: inline-block;
      margin-top: 18px;
      background: #00337a;
      color: white;
      padding: 10px 24px;
      border-radius: 40px;
      font-weight: 500;
      font-size: 0.9rem;
      margin-right: 10px;
      transition: 0.2s;
      border: 1px solid transparent;
    }

    .pdf-btn i {
      margin-right: 8px;
    }

    .pdf-btn:hover {
      background: #00245a;
      transform: translateY(-2px);
    }

    .pdf-btn.outline {
      background: white;
      color: #00337a;
      border-color: #00337a;
    }

    .pdf-btn.outline:hover {
      background: #00337a;
      color: white;
    }

    /* ----- ANNOUNCEMENTS (two column) ----- */
    .announcement-area {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .announcement-box {
      background: white;
      padding: 28px 30px;
      border-radius: 24px;
      box-shadow: 0 8px 26px rgba(0, 60, 120, 0.06);
      border: 1px solid rgba(0, 60, 120, 0.04);
    }

    .announcement-box h3 {
      color: #00337a;
      font-size: 1.3rem;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 3px solid #ffc107;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .announcement-box h3 i {
      color: #ffc107;
    }

    .announcement-box ul {
      list-style: none;
    }

    .announcement-box li {
      padding: 12px 0;
      border-bottom: 1px solid #e8f0fa;
    }

    .announcement-box li:last-child {
      border-bottom: none;
    }

    .announcement-box li a {
      color: #174a7c;
      font-weight: 500;
      font-size: 0.95rem;
      transition: 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .announcement-box li a i {
      color: #00337a;
      font-size: 0.8rem;
      opacity: 0.7;
    }

    .announcement-box li a:hover {
      color: #00337a;
      padding-left: 6px;
    }

    /* ----- AWARDS (recognition) ----- */
    .recognition-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      max-width: 1000px;
      margin: 0 auto;
      background: white;
      padding: 30px 35px;
      border-radius: 28px;
      box-shadow: 0 8px 24px rgba(0, 60, 120, 0.06);
    }

    .recognition-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 10px 8px;
      border-radius: 12px;
      transition: 0.2s;
    }

    .recognition-item i {
      font-size: 2rem;
      color: #00337a;
      width: 40px;
      text-align: center;
    }

    .recognition-item span {
      font-weight: 500;
      color: #1f3a5e;
      font-size: 0.98rem;
    }

    /* ----- FOOTER (elegant) ----- */
    footer {
      background: #00245a;
      color: white;
      padding: 50px 6% 30px;
      border-top: 4px solid #ffc107;
    }

    .footer-content {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 40px;
    }

    footer h3 {
      color: white;
      font-size: 1.2rem;
      margin-bottom: 18px;
      font-weight: 600;
    }

    footer p,
    footer li {
      color: #d4e4fc;
      font-size: 0.95rem;
      line-height: 1.8;
    }

    footer ul {
      list-style: none;
    }

    footer a {
      color: #d4e4fc;
      transition: 0.2s;
    }

    footer a:hover {
      color: #ffc107;
      padding-left: 4px;
    }

    .footer-contact i {
      width: 24px;
      color: #ffc107;
    }

    .copyright {
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.10);
      margin-top: 40px;
      padding-top: 25px;
      font-size: 0.9rem;
      color: #b6cef0;
    }

    /* ----- RESPONSIVE (professional breakpoints) ----- */
    @media (max-width: 1100px) {
      .stats {
        grid-template-columns: repeat(2, 1fr);
      }

      .navbar {
        flex-direction: column;
        gap: 6px;
        padding: 12px 4%;
      }

      .nav-links {
        justify-content: center;
        gap: 0px;
      }

      .nav-links a {
        padding: 8px 10px;
        font-size: 0.75rem;
      }
    }

    @media (max-width: 800px) {
      .principal-box {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 30px 25px;
      }

      .principal-img {
        margin: 0 auto;
        width: 180px;
        height: 210px;
      }

      .announcement-area {
        grid-template-columns: 1fr;
      }

      .footer-content {
        grid-template-columns: 1fr;
        gap: 25px;
      }

      .top-header {
        flex-direction: column;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        padding: 6px 8px;
      }

      .hero h1 {
        font-size: 2rem;
      }

      .section {
        padding: 40px 4%;
      }

      .about-box {
        padding: 25px 20px;
      }

      .image-track img {
        width: 280px;
        height: 160px;
      }
    }

    @media (max-width: 500px) {
      .stats {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
      }

      .stat-card {
        padding: 18px 6px;
      }

      .stat-card h2 {
        font-size: 2rem;
      }

      .college-name {
        font-size: 1rem;
      }

      .logo {
        width: 48px;
        height: 48px;
      }

      .btn {
        padding: 12px 22px;
        font-size: 0.85rem;
      }
    }

    /* ==============================
   NOTIFICATION BELL
============================== */

    .notification-wrapper {
      list-style: none;
      position: relative;
    }

    .notification-link {
      width: 42px;
      height: 42px;
      display: flex !important;
      align-items: center;
      justify-content: center;

      text-decoration: none;
      font-size: 21px;

      color: #174a7c;
      border-radius: 50%;

      position: relative;
      transition: 0.2s;
    }

    .notification-link:hover {
      background: #eef2ff;
      color: #4f46e5;
      transform: scale(1.08);
    }

    /* Red notification count */
    .notification-badge {
      position: absolute;
      top: 0px;
      right: -2px;

      min-width: 18px;
      height: 18px;

      padding: 0 5px;

      background: #e53935;
      color: white;

      border-radius: 20px;

      font-size: 11px;
      font-weight: bold;

      display: none;

      align-items: center;
      justify-content: center;

      border: 2px solid white;
    }

    /* Bell shake when new notification arrives */
    .notification-new {
      animation: bellShake 0.7s ease-in-out infinite;
    }

    @keyframes bellShake {

      0% {
        transform: rotate(0deg);
      }

      20% {
        transform: rotate(-15deg);
      }

      40% {
        transform: rotate(15deg);
      }

      60% {
        transform: rotate(-10deg);
      }

      80% {
        transform: rotate(10deg);
      }

      100% {
        transform: rotate(0deg);
      }
    }
  </style>
</head>
<script>
  function checkNotifications() {

    fetch('notification_count.php')
      .then(response => response.json())
      .then(data => {

        const badge = document.getElementById('notificationBadge');
        const bell = document.querySelector('.notification-link');

        if (!badge || !bell) {
          return;
        }

        const count = parseInt(data.count) || 0;

        if (count > 0) {

          badge.innerText = count > 99 ? '99+' : count;
          badge.style.display = 'flex';

          bell.classList.add('notification-new');

        } else {

          badge.innerText = '0';
          badge.style.display = 'none';

          bell.classList.remove('notification-new');
        }

      })
      .catch(error => {
        console.log('Notification check error:', error);
      });
  }


  /* Check immediately */
  checkNotifications();

  /* Check every 5 seconds */
  setInterval(checkNotifications, 5000);
</script>

<body>

  <!-- TOP HEADER -->
  <div class="top-header">
    <div><i class="fas fa-university"></i> Shri Sharda Bhavan Education Society's</div>
    <div><i class="fas fa-envelope"></i> principalymn@gmail.com</div>
  </div>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="logo-area">
      <img src="https://tse2.mm.bing.net/th/id/OIP.nYyQmvWkM4rlNO85xpY68AAAAA?r=0&pid=Api&h=220&P=0g" alt="College Logo" class="logo" />
      <div class="college-name">
        Yeshwant Mahavidyalaya, Nanded
        <small>Affiliated to S. R. T. M. University, Nanded</small>
      </div>
    </div>
    <ul class="nav-links">
      <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="admission.php">Admissions</a></li>
      <li><a href="departments.php">Departments</a></li>
      <li><a href="user_events.php">Event</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="dashboard.php">profile</a></li>
      <li class="notification-wrapper">
        <a href="notifications.php" class="notification-link" title="Notifications">
          <i class="fas fa-bell"></i>
          <span id="notificationBadge" class="notification-badge">0</span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- IMAGE SCROLL -->
  <section class="image-scroll">
    <div class="image-track">

      <img src="https://threebestrated.in/images/YeshwantMahavidyalay-Nanded-MH.jpeg"
        alt="Yeshwant Mahavidyalaya Nanded" />

      <img src="https://tse4.mm.bing.net/th/id/OIP.Rx-qAn0NdDgaH_d9VZ-ufQHaDV?r=0&pid=Api&h=220&P=0"
        alt="Yeshwant Mahavidyalaya Nanded" />

      <img src="https://tse4.mm.bing.net/th/id/OIP.AyuU-dxf7hmiTuN5ln8zKgHaDV?r=0&pid=Api&h=220&P=0"
        alt="Yeshwant Mahavidyalaya Nanded" />

      <img src="https://i.ytimg.com/vi/L6Q-2jqLA1A/maxresdefault.jpg"
        alt="Yeshwant Mahavidyalaya Nanded" />

      <img src="https://tse4.mm.bing.net/th/id/OIP.Etg64GXJE24LCrJHIzySUwHaFj?r=0&pid=Api&h=220&P=0" alt="">

      <img src="https://images.bhaskarassets.com/web2images/5483/2024/09/02/246_172502117766d1bbf9eae6c_dsc_0517.jpg" alt="">

    </div>
  </section>

  <!-- HERO -->
  <section class="hero">
    <h1>Yeshwant Mahavidyalaya, Nanded</h1>
    <h2>Official College Website</h2>
    <p>Shri Sharda Bhavan Education Society's</p>
    <p>Affiliated to S. R. T. M. University, Nanded &nbsp; | &nbsp; AISHE CODE : C-7460</p>
    <div class="grade"><i class="fas fa-star"></i> Awarded 'A+' Grade (3.27 CGPA) in 4th Cycle NAAC Reaccreditation</div>
    <div class="buttons">
      <a href="registration/login.php" class="btn"><i class="fas fa-user-graduate"></i> Student Login</a>
      <a href="registration/register.php" class="btn register"><i class="fas fa-user-plus"></i> Student Registration</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="section">
    <h2 class="section-title">About Yeshwant Mahavidyalaya</h2>
    <div class="about-box">
      <h3>Excellence Since 1964</h3>
      <p>Yeshwant Mahavidyalaya, Nanded is an esteemed institution established under Shri Sharda Bhavan Education Society. The college is affiliated to S. R. T. M. University, Nanded and is committed to providing quality education, research opportunities and holistic development of students.</p>
      <br>
      <p>The institution has been awarded an <strong>A+ Grade with 3.27 CGPA</strong> in the 4th Cycle of NAAC Reaccreditation and has also received UGC-College with Potential for Excellence (CPE) status.</p>
    </div>
  </section>

  <!-- STATS -->
  <section class="section">
    <h2 class="section-title">Our Achievements</h2>
    <div class="stats">
      <div class="stat-card">
        <h2>62+</h2>
        <p>Years of Excellence</p>
      </div>
      <div class="stat-card">
        <h2>8000+</h2>
        <p>Students Enrolled</p>
      </div>
      <div class="stat-card">
        <h2>200+</h2>
        <p>Faculty Members</p>
      </div>
      <div class="stat-card">
        <h2>50+</h2>
        <p>Departments &amp; Cells</p>
      </div>
    </div>
  </section>

  <!-- PRINCIPAL -->
  <section class="section principal">
    <h2 class="section-title">Principal's Desk</h2>
    <div class="principal-box">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSeDDZoBz4R8MQJT-J7PqipgsBDoTIYnMtuHSnaYKkNGa45slPY81SZ5Cs&s=10" alt="Principal" class="principal-img" />
      <div>
        <h3>Welcome to Yeshwant Mahavidyalaya</h3>
        <p>Welcome to Yeshwant Mahavidyalaya, an institution where excellence is a tradition. We are committed to nurturing young minds through quality education, innovation, research and value-based learning.</p>
        <div>
          <a href="#" class="pdf-btn"><i class="fas fa-file-pdf"></i> Principal Profile</a>
          <a href="#" class="pdf-btn outline"><i class="fas fa-file-pdf"></i> Induction Program</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ANNOUNCEMENTS -->
  <section class="section">
    <h2 class="section-title">Important Announcements</h2>
    <div class="announcement-area">
      <div class="announcement-box">
        <h3><i class="fas fa-bullhorn"></i> Latest Notices</h3>
        <ul>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Earn and Learn Scheme 2026-27</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Degree Convocation Form</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> College Uniform &amp; ID Card</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> NCC Notice 2026-27</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Regular Classes Notice</a></li>
        </ul>
      </div>
      <div class="announcement-box">
        <h3><i class="fas fa-graduation-cap"></i> Admissions 2026-27</h3>
        <ul>
          <li><a href="#"><i class="fas fa-chevron-right"></i> UG Admission Advertisement</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> PG Admission Advertisement</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> UG First Year Notice</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> UG First Year Registration</a></li>
          <li><a href="#"><i class="fas fa-chevron-right"></i> Admission Process 2026-27</a></li>
        </ul>
      </div>
    </div>
  </section>

  <!-- AWARDS / RECOGNITION -->
  <section class="section">
    <h2 class="section-title">Recognition &amp; Awards</h2>
    <div class="recognition-grid">
      <div class="recognition-item"><i class="fas fa-trophy"></i><span>A+ Grade – NAAC</span></div>
      <div class="recognition-item"><i class="fas fa-star"></i><span>3.27 CGPA – 4th Cycle</span></div>
      <div class="recognition-item"><i class="fas fa-award"></i><span>UGC-CPE Status</span></div>
      <div class="recognition-item"><i class="fas fa-medal"></i><span>Best College Award</span></div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-content">
      <div>
        <h3><i class="fas fa-university" style="margin-right:10px;color:#ffc107;"></i> Yeshwant Mahavidyalaya, Nanded</h3>
        <p>Shri Sharda Bhavan Education Society's</p>
        <p>Affiliated to S. R. T. M. University, Nanded</p>
        <p>AISHE CODE : C-7460</p>
      </div>
      <div>
        <h3>Quick Links</h3>
        <ul>
          <li><a href="about.php">About</a></li>
          <li><a href="admission.php">Admissions</a></li>
          <li><a href="#">Programs</a></li>
          <li><a href="departments.php">Departments</a></li>
          <li><a href="contact.php">Contact Us</a></li>
        </ul>
      </div>
      <div>
        <h3><i class="fas fa-address-card" style="margin-right:8px;color:#ffc107;"></i> Contact</h3>
        <p><i class="fas fa-envelope" style="width:24px;"></i> principalymn@gmail.com</p>
        <p><i class="fas fa-map-marker-alt" style="width:24px;"></i> Nanded, Maharashtra</p>
        <p><i class="fas fa-globe" style="width:24px;"></i> www.ymnnanded.in</p>
      </div>
    </div>
    <div class="copyright">
      © 2026 Yeshwant Mahavidyalaya, Nanded. All Rights Reserved.
    </div>
  </footer>

</body>

</html>