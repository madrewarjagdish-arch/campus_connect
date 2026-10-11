<?php
include "db.php";

/* =====================================================
   GET ABOUT DATA
===================================================== */

$result = $conn->query(
    "SELECT * FROM about ORDER BY id DESC LIMIT 1"
);

$about = $result ? $result->fetch_assoc() : null;


/* =====================================================
   IMAGE PATH
===================================================== */

$imageUrl = "";

if ($about && !empty($about['image'])) {

    $imageFile = basename($about['image']);

    $physicalPath = __DIR__ . "/uploads/" . $imageFile;

    $browserPath = "uploads/" . rawurlencode($imageFile);

    if (file_exists($physicalPath)) {
        $imageUrl = $browserPath;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?php
        echo !empty($about['title'])
            ? htmlspecialchars($about['title'])
            : "About Us";
        ?>
    </title>


    <style>
        /* =====================================================
   RESET
===================================================== */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        /* =====================================================
   BODY
===================================================== */

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f8fc;

            color: #333;

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



        /* PROFILE BUTTON */

        .profile-btn {

            background: #063f86;

            margin-left: 5px;

        }


        /* =====================================================
   HERO
===================================================== */

        .hero {

            background:
                linear-gradient(135deg,
                    #0756b5,
                    #0d8cff);

            color: white;

            padding: 80px 20px;

            text-align: center;

        }


        .hero h1 {

            font-size: 45px;

            margin-bottom: 10px;

        }


        .hero p {

            font-size: 18px;

        }


        /* =====================================================
   CONTAINER
===================================================== */

        .container {

            max-width: 1100px;

            margin: 50px auto;

            padding: 0 20px;

        }


        /* =====================================================
   ABOUT BOX
===================================================== */

        .about-box {

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.08);

            margin-bottom: 30px;

        }


        .about-flex {

            display: flex;

            gap: 35px;

            align-items: center;

        }


        /* =====================================================
   IMAGE
===================================================== */

        .about-image {

            width: 45%;

        }


        .about-image img {

            width: 100%;

            height: 350px;

            object-fit: cover;

            display: block;

            border-radius: 15px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.15);

        }


        /* =====================================================
   CONTENT
===================================================== */

        .about-content {

            width: 55%;

        }


        .about-content h2 {

            color: #0756b5;

            font-size: 30px;

            margin-bottom: 15px;

        }


        .about-content p {

            line-height: 1.8;

            font-size: 16px;

        }


        /* =====================================================
   SECTIONS
===================================================== */

        .section {

            background: white;

            padding: 30px;

            border-radius: 15px;

            margin-bottom: 25px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.06);

        }


        .section h2 {

            color: #0756b5;

            margin-bottom: 15px;

        }


        .section p {

            line-height: 1.8;

            font-size: 16px;

            white-space: pre-line;

        }


        /* =====================================================
   IMAGE ERROR
===================================================== */

        .image-error {

            width: 100%;

            min-height: 300px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            padding: 20px;

            background: #f8d7da;

            color: #721c24;

            border-radius: 15px;

        }


        .image-error h3 {

            margin-bottom: 10px;

        }


        .image-error code {

            word-break: break-all;

        }


        /* =====================================================
   FOOTER
===================================================== */

        .footer {

            background: #063f86;

            color: white;

            text-align: center;

            padding: 25px;

            margin-top: 50px;

        }


        .footer p {

            margin: 5px;

        }


        /* =====================================================
   MOBILE MENU
===================================================== */

        @media (max-width: 1000px) {

            .college-header {

                padding: 15px 20px;

            }


            .nav-container {

                flex-wrap: wrap;

            }


            .navbar a {

                padding: 14px 9px;

                font-size: 12px;

            }

        }


        @media (max-width: 768px) {

            .college-header {

                flex-direction: column;

                text-align: center;

            }


            .nav-container {

                flex-direction: column;

                width: 100%;

            }


            .navbar a {

                width: 100%;

                text-align: center;

                padding: 13px;

            }


            .about-flex {

                flex-direction: column;

            }


            .about-image,
            .about-content {

                width: 100%;

            }


            .hero {

                padding: 55px 20px;

            }


            .hero h1 {

                font-size: 32px;

            }


            .about-image img {

                height: 250px;

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
            <a href="notifications.php" class="notification-link" title="Notifications">
                <i class="fas fa-bell"></i>
                <span id="notificationBadge" class="notification-badge">0</span>
            </a>
        </ul>
    </nav>



    <?php if ($about): ?>


        <!-- =====================================================
     HERO
===================================================== -->

        <section class="hero">

            <h1>

                <?php
                echo htmlspecialchars(
                    $about['title']
                );
                ?>

            </h1>

            <p>
                Welcome to Yeshwant Mahavidyalaya, Nanded
            </p>

        </section>



        <div class="container">


            <!-- =====================================================
     ABOUT
===================================================== -->

            <div class="about-box">

                <div class="about-flex">


                    <!-- IMAGE -->

                    <div class="about-image">

                        <?php if (!empty($imageUrl)): ?>

                            <img
                                src="<?php
                                        echo htmlspecialchars($imageUrl);
                                        ?>"
                                alt="<?php
                                        echo htmlspecialchars(
                                            $about['title']
                                        );
                                        ?>"
                                onerror="this.style.display='none'; document.getElementById('imgError').style.display='flex';">


                            <!-- FALLBACK -->

                            <div
                                id="imgError"
                                class="image-error"
                                style="display:none;">

                                <div>

                                    <h3>
                                        Image could not be loaded
                                    </h3>

                                    <p>
                                        Image URL:
                                    </p>

                                    <code>

                                        <?php
                                        echo htmlspecialchars(
                                            $imageUrl
                                        );
                                        ?>

                                    </code>

                                </div>

                            </div>


                        <?php else: ?>


                            <div class="image-error">

                                <div>

                                    <h3>
                                        Image Not Found
                                    </h3>

                                    <p>
                                        Database Image:
                                    </p>

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $about['image']
                                                ?? 'No image'
                                        );
                                        ?>

                                    </strong>

                                    <p>
                                        Expected Location:
                                    </p>

                                    <code>

                                        <?php
                                        echo htmlspecialchars(
                                            __DIR__ .
                                                "/uploads/" .
                                                (
                                                    $about['image']
                                                    ?? ''
                                                )
                                        );
                                        ?>

                                    </code>

                                </div>

                            </div>


                        <?php endif; ?>

                    </div>



                    <!-- CONTENT -->

                    <div class="about-content">

                        <h2>
                            About Our College
                        </h2>

                        <p>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $about['description']
                                        ??
                                        'No description available.'
                                )
                            );

                            ?>

                        </p>

                    </div>


                </div>

            </div>



            <!-- =====================================================
     MISSION
===================================================== -->

            <?php if (!empty($about['mission'])): ?>

                <div class="section">

                    <h2>
                        Our Mission
                    </h2>

                    <p>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $about['mission']
                            )
                        );

                        ?>

                    </p>

                </div>

            <?php endif; ?>



            <!-- =====================================================
     VISION
===================================================== -->

            <?php if (!empty($about['vision'])): ?>

                <div class="section">

                    <h2>
                        Our Vision
                    </h2>

                    <p>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $about['vision']
                            )
                        );

                        ?>

                    </p>

                </div>

            <?php endif; ?>



            <!-- =====================================================
     OBJECTIVES
===================================================== -->

            <?php if (!empty($about['objectives'])): ?>

                <div class="section">

                    <h2>
                        Our Objectives
                    </h2>

                    <p>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $about['objectives']
                            )
                        );

                        ?>

                    </p>

                </div>

            <?php endif; ?>


        </div>



    <?php else: ?>


        <!-- =====================================================
     NO CONTENT
===================================================== -->

        <div style="
    text-align:center;
    padding:100px 20px;
">

            <h1>
                About Content Not Available
            </h1>

            <p>
                Admin has not added About content yet.
            </p>

        </div>


    <?php endif; ?>



    <!-- =====================================================
     FOOTER
===================================================== -->

    <footer class="footer">

        <p>
            © <?php echo date("Y"); ?>
            Yeshwant Mahavidyalaya, Nanded
        </p>

        <p>
            Shri Sharda Bhavan Education Society's
        </p>

    </footer>


</body>

</html>