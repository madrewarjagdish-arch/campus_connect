<?php

include "db.php";

$departments = $conn->query("
SELECT *
FROM departments
ORDER BY department_name
");

?>

<!DOCTYPE html>

<html>

<head>

    <title>Departments</title>

    <style>
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

        body {
            font-family: Arial;
            background: #f4f7fb;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .department {
            background: white;
            padding: 25px;
            margin: 20px 0;
            border-radius: 12px;
            box-shadow: 0 5px 20px #0002;
        }

        .subject {
            display: block;
            padding: 12px;
            margin: 8px 0;
            background: #f1f5fa;
            text-decoration: none;
            color: #222;
            border-left: 4px solid #1976d2;
            border-radius: 6px;
        }

        .subject:hover {
            background: #1976d2;
            color: white;
        }
    </style>

</head>

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
        </ul>
    </nav>

    <div class="container">

        <h1>🎓 Departments</h1>


        <?php

        while (
            $d = $departments->fetch_assoc()
        ) {

        ?>

            <div class="department">

                <h2>

                    <?= htmlspecialchars(
                        $d['department_name']
                    ) ?>

                </h2>


                <?php

                $id = $d['id'];

                $subjects = $conn->query("
SELECT *
FROM subjects
WHERE department_id=$id
ORDER BY subject_name
");


                while (
                    $s = $subjects->fetch_assoc()
                ) {

                ?>

                    <a
                        class="subject"
                        href="subject.php?id=<?= $s['id'] ?>">

                        📘

                        <?= htmlspecialchars(
                            $s['subject_name']
                        ) ?>

                    </a>

                <?php } ?>


            </div>

        <?php } ?>


    </div>

</body>

</html>