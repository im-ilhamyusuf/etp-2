<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>ETP</title>
  <meta name="description" content="Platform ETP membantu kamu meningkatkan skor TOEFL melalui pretest, pembelajaran singkat, dan simulasi tes yang dirancang menyerupai ujian asli. Cocok untuk persiapan TOEFL secara cepat dan efektif.">
  <meta name="keywords" content="belajar TOEFL, latihan TOEFL online, simulasi tes TOEFL, aplikasi TOEFL terbaik, kursus TOEFL cepat, TOEFL preparation, English test platform, pretest TOEFL, posttest TOEFL">

  <!-- Favicons -->
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
</head>

<body class="index-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <span class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">ETP</h1>
      </span>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#services">Services</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="{{ route('filament.peserta.auth.login') }}">Login</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
            <h1>English Test for Proficiency (ETP)</h1>
            <p>Facilitates TOEFL learning in a concise and structured manner, equipped with pretests, posttests, and test simulations to measure and improve English language skills effectively.</p>
            <div class="d-flex">
              <a href="{{ route('filament.peserta.auth.login') }}" class="btn-get-started">Login</a>
            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="100">
            <img src="img/hero-img.svg" class="img-fluid animated" alt="">
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>About<br></span>
        <h2>About</h2>
        <p>ETP is a TOEFL learning platform designed to help users improve their English skills in a structured and efficient manner.</p>
        <p>Through a pretest and posttest system, users can assess their initial abilities, participate in short lessons, and see real-time score improvements. Equipped with TOEFL test simulations, the ETP provides a practice experience that closely approximates the actual exam.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">
          <div class="text-center" data-aos="fade-up" data-aos-delay="100">
            <img src="img/about.svg" class="img-fluid" alt="">
          </div>
        </div>

      </div>

    </section><!-- /About Section -->


    <!-- Services Section -->
    <section id="services" class="services section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Services</span>
        <h2>Services</h2>
        <p>Our system is designed to help the TOEFL learning and testing process more effectively, with features that are intuitive, structured, and easy to use for all users.</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-activity"></i>
              </div>
              <span class="stretched-link">
                <h3>Pretest & Posttest</h3>
              </span>
              <p>Measure your skills from start to finish. Pretests help determine your initial ability level, while posttests show how much improvement you've achieved after learning.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-broadcast"></i>
              </div>
              <span class="stretched-link">
                <h3>Short Course</h3>
              </span>
              <p>Short, targeted TOEFL lessons that focus on essential material. Designed to help you quickly grasp key strategies and concepts without the hassle of long-winded study sessions.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="bi bi-bounding-box-circles"></i>
              </div>
              <span class="stretched-link">
                <h3>TOEFL Test Simulation</h3>
              </span>
              <p>Practice tests that mimic the actual TOEFL exam. They help you familiarize yourself with the question format, manage your time, and boost your confidence for the real test.</p>
              <span class="stretched-link"></span>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">ETP</strong> <span>All Rights Reserved</span></p>
    </div>

  </footer>

  <!-- Vendor JS Files -->
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="js/main.js"></script>

</body>

</html>
