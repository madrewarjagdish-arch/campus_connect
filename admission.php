<?php

include "db.php";

/* =========================================================
   SELECTED YEAR
========================================================= */

$selectedYear = $_GET['year'] ?? '';


/* =========================================================
   GET ACADEMIC YEARS
========================================================= */

$yearResult = $conn->query("
    SELECT DISTINCT academic_year
    FROM admissions
    ORDER BY academic_year DESC
");


/* =========================================================
   GET ADMISSION DATA
========================================================= */

if (!empty($selectedYear)) {

    $stmt = $conn->prepare("
        SELECT *
        FROM admissions
        WHERE academic_year = ?
        ORDER BY id DESC
    ");

    $stmt->bind_param("s", $selectedYear);

    $stmt->execute();

    $result = $stmt->get_result();
} else {

    $result = $conn->query("
        SELECT *
        FROM admissions
        ORDER BY academic_year DESC, id DESC
    ");
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Admissions | Yeshwant Mahavidyalaya
    </title>


    <style>
        /* =========================================================
   RESET
========================================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* =========================================================
   BODY
========================================================= */

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f8fc;

            color: #222;

            line-height: 1.6;
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


        /* =========================================================
   HERO
========================================================= */

        .hero {

            background:
                linear-gradient(135deg,
                    #063b73,
                    #1683d8);

            color: white;

            padding: 65px 6%;

            text-align: center;
        }


        .hero h1 {

            font-size: 42px;

            margin-bottom: 10px;
        }


        .hero p {

            font-size: 18px;

            opacity: 0.95;
        }


        /* =========================================================
   CONTAINER
========================================================= */

        .container {

            width: 92%;

            max-width: 1250px;

            margin: 40px auto;
        }


        /* =========================================================
   SECTION TITLE
========================================================= */

        .section-title {

            text-align: center;

            color: #063b73;

            font-size: 30px;

            margin-bottom: 30px;
        }


        .section-subtitle {

            text-align: center;

            color: #666;

            margin-top: -20px;

            margin-bottom: 30px;
        }


        /* =========================================================
   DOCUMENT SECTION
========================================================= */

        .documents {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 25px;

            margin-bottom: 50px;
        }


        .document-card {

            background: white;

            padding: 30px;

            border-radius: 16px;

            text-align: center;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.08);

            border-top: 5px solid #063b73;

            transition: 0.3s;
        }


        .document-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 10px 25px rgba(0, 0, 0, 0.12);
        }


        .document-icon {

            font-size: 50px;

            margin-bottom: 10px;
        }


        .document-card h3 {

            color: #063b73;

            font-size: 22px;

            margin-bottom: 10px;
        }


        .document-card p {

            color: #666;

            margin-bottom: 20px;
        }


        .document-buttons {

            display: flex;

            justify-content: center;

            gap: 10px;

            flex-wrap: wrap;
        }


        /* =========================================================
   BUTTON
========================================================= */

        .btn {

            display: inline-block;

            padding: 11px 18px;

            border-radius: 8px;

            text-decoration: none;

            color: white;

            font-weight: bold;

            transition: 0.3s;
        }


        .btn:hover {

            transform: translateY(-2px);

            opacity: 0.9;
        }


        .btn-view {

            background: #0d6efd;
        }


        .btn-download {

            background: #198754;
        }


        /* =========================================================
   NOT AVAILABLE
========================================================= */

        .not-available {

            color: #dc3545 !important;

            font-weight: bold;
        }


        /* =========================================================
   YEAR FILTER
========================================================= */

        .filter-box {

            background: white;

            padding: 25px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.07);

            margin-bottom: 30px;
        }


        .filter-form {

            display: flex;

            justify-content: center;

            align-items: end;

            gap: 15px;

            flex-wrap: wrap;
        }


        .filter-group {

            display: flex;

            flex-direction: column;

            min-width: 250px;
        }


        .filter-group label {

            font-weight: bold;

            margin-bottom: 7px;

            color: #333;
        }


        .filter-group select {

            padding: 12px;

            border: 1px solid #ccd5df;

            border-radius: 8px;

            font-size: 15px;

            background: white;
        }


        .filter-btn {

            padding: 12px 22px;

            background: #063b73;

            color: white;

            border: none;

            border-radius: 8px;

            cursor: pointer;

            font-weight: bold;
        }


        .reset-btn {

            padding: 12px 22px;

            background: #6c757d;

            color: white;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;
        }


        /* =========================================================
   ADMISSION TABLE
========================================================= */

        .table-card {

            background: white;

            padding: 25px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.07);

            margin-bottom: 40px;
        }


        .table-wrapper {

            width: 100%;

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;
        }


        thead th {

            background: #063b73;

            color: white;

            padding: 14px;

            text-align: left;

            font-size: 14px;
        }


        tbody td {

            padding: 13px;

            border-bottom: 1px solid #e1e5ea;

            vertical-align: top;
        }


        tbody tr:hover {

            background: #f4f8fc;
        }


        /* =========================================================
   BADGES
========================================================= */

        .badge {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
        }


        .badge-faculty {

            background: #e7f1ff;

            color: #0d6efd;
        }


        .badge-type {

            background: #fff3cd;

            color: #856404;
        }


        .badge-intake {

            background: #d1e7dd;

            color: #0f5132;
        }


        /* =========================================================
   NO DATA
========================================================= */

        .no-data {

            text-align: center;

            padding: 45px 20px;

            color: #777;

            font-size: 17px;
        }


        /* =========================================================
   INFO SECTION
========================================================= */

        .info-section {

            background:
                linear-gradient(135deg,
                    #063b73,
                    #1683d8);

            color: white;

            padding: 35px;

            border-radius: 15px;

            margin-bottom: 40px;
        }


        .info-section h2 {

            margin-bottom: 15px;
        }


        .info-section ul {

            padding-left: 20px;
        }


        .info-section li {

            margin-bottom: 8px;
        }


        /* =========================================================
   FOOTER
========================================================= */

        .footer {

            background: #063b73;

            color: white;

            text-align: center;

            padding: 25px 15px;

            margin-top: 50px;
        }


        .footer p {

            margin: 5px 0;

            font-size: 14px;
        }


        /* =========================================================
   RESPONSIVE
========================================================= */

        @media (max-width: 768px) {

            .navbar {

                flex-direction: column;

                text-align: center;
            }


            .nav-links {

                justify-content: center;

                gap: 12px;
            }


            .hero {

                padding: 45px 20px;
            }


            .hero h1 {

                font-size: 30px;
            }


            .hero p {

                font-size: 16px;
            }


            .documents {

                grid-template-columns: 1fr;
            }


            .filter-form {

                align-items: stretch;

                flex-direction: column;
            }


            .filter-group {

                width: 100%;

                min-width: 0;
            }


            .filter-btn,
            .reset-btn {

                text-align: center;

                width: 100%;
            }

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


    <!-- =========================================================
     HERO
========================================================= -->

    <section class="hero">

        <h1>

            🎓 Admissions

        </h1>


        <p>

            Welcome to the Admission Portal
            of Yeshwant Mahavidyalaya, Nanded

        </p>

    </section>



    <div class="container">


        <!-- =========================================================
     ADMISSION DOCUMENTS
========================================================= -->

        <h2 class="section-title">

            📚 Admission Documents

        </h2>


        <p class="section-subtitle">

            Download or view the latest admission
            documents.

        </p>


        <div class="documents">


            <!-- =====================================================
         ADMISSION POLICY
    ====================================================== -->

            <div class="document-card">

                <div class="document-icon">

                    📜

                </div>


                <h3>

                    Admission Policy

                </h3>


                <p>

                    Read the official Admission Policy
                    of the college.

                </p>


                <?php

                $policy =
                    "uploads/admission_policy.pdf";


                if (file_exists($policy)) {

                ?>

                    <div class="document-buttons">


                        <a
                            href="uploads/admission_policy.pdf"
                            target="_blank"
                            class="btn btn-view">

                            👁 View Policy

                        </a>


                        <a
                            href="uploads/admission_policy.pdf"
                            download
                            class="btn btn-download">

                            ⬇ Download

                        </a>


                    </div>

                <?php

                } else {

                ?>

                    <p class="not-available">

                        ❌ Admission Policy
                        is currently unavailable.

                    </p>

                <?php

                }

                ?>

            </div>



            <!-- =====================================================
         ADMISSION PROSPECTUS
    ====================================================== -->

            <div class="document-card">

                <div class="document-icon">

                    📘

                </div>


                <h3>

                    Admission Prospectus

                </h3>


                <p>

                    View the latest college
                    Admission Prospectus.

                </p>


                <?php

                $prospectus =
                    "uploads/admission_prospectus.pdf";


                if (file_exists($prospectus)) {

                ?>

                    <div class="document-buttons">


                        <a
                            href="uploads/admission_prospectus.pdf"
                            target="_blank"
                            class="btn btn-view">

                            👁 View Prospectus

                        </a>


                        <a
                            href="uploads/admission_prospectus.pdf"
                            download
                            class="btn btn-download">

                            ⬇ Download

                        </a>


                    </div>

                <?php

                } else {

                ?>

                    <p class="not-available">

                        ❌ Admission Prospectus
                        is currently unavailable.

                    </p>

                <?php

                }

                ?>

            </div>

        </div>



        <!-- =========================================================
     YEAR FILTER
========================================================= -->

        <h2 class="section-title">

            📅 Year-wise Admissions

        </h2>


        <p class="section-subtitle">

            Select an academic year to view
            available programmes.

        </p>



        <div class="filter-box">

            <form
                method="GET"
                class="filter-form">


                <div class="filter-group">

                    <label>

                        Academic Year

                    </label>


                    <select
                        name="year">

                        <option value="">

                            All Academic Years

                        </option>


                        <?php

                        if (
                            $yearResult &&
                            $yearResult->num_rows > 0
                        ) {

                            while (
                                $year =
                                $yearResult->fetch_assoc()
                            ) {

                                $yearValue =
                                    $year['academic_year'];

                                $selected =
                                    (
                                        $selectedYear
                                        ==
                                        $yearValue
                                    )
                                    ? "selected"
                                    : "";

                        ?>

                                <option
                                    value="<?php
                                            echo htmlspecialchars(
                                                $yearValue
                                            );
                                            ?>"
                                    <?php echo $selected; ?>>

                                    <?php
                                    echo htmlspecialchars(
                                        $yearValue
                                    );
                                    ?>

                                </option>

                        <?php

                            }
                        }

                        ?>

                    </select>

                </div>



                <button
                    type="submit"
                    class="filter-btn">

                    🔍 View Admissions

                </button>


                <a
                    href="admission.php"
                    class="reset-btn">

                    Reset

                </a>

            </form>

        </div>



        <!-- =========================================================
     ADMISSION RECORDS
========================================================= -->

        <div class="table-card">


            <?php

            if (!empty($selectedYear)) {

            ?>

                <h2 class="section-title">

                    Admissions for
                    <?php
                    echo htmlspecialchars(
                        $selectedYear
                    );
                    ?>

                </h2>

            <?php

            } else {

            ?>

                <h2 class="section-title">

                    Available Admissions

                </h2>

            <?php

            }


            if (
                $result &&
                $result->num_rows > 0
            ) {

            ?>


                <div class="table-wrapper">


                    <table>


                        <thead>

                            <tr>

                                <th>
                                    Academic Year
                                </th>

                                <th>
                                    Faculty
                                </th>

                                <th>
                                    Programme
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Intake
                                </th>

                                <th>
                                    Eligibility
                                </th>

                                <th>
                                    Last Date
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php

                            while (
                                $row =
                                $result->fetch_assoc()
                            ) {

                            ?>

                                <tr>


                                    <td>

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $row['academic_year']
                                            );

                                            ?>

                                        </strong>

                                    </td>



                                    <td>

                                        <span
                                            class="badge badge-faculty">

                                            <?php

                                            echo htmlspecialchars(
                                                $row['faculty']
                                            );

                                            ?>

                                        </span>

                                    </td>



                                    <td>

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $row['program_name']
                                            );

                                            ?>

                                        </strong>

                                    </td>



                                    <td>

                                        <span
                                            class="badge badge-type">

                                            <?php

                                            echo htmlspecialchars(
                                                $row['program_type']
                                            );

                                            ?>

                                        </span>

                                    </td>



                                    <td>

                                        <span
                                            class="badge badge-intake">

                                            <?php

                                            echo htmlspecialchars(
                                                $row['intake_capacity']
                                            );

                                            ?>

                                        </span>

                                    </td>



                                    <td>

                                        <?php

                                        echo nl2br(
                                            htmlspecialchars(
                                                $row['eligibility']
                                            )
                                        );

                                        ?>

                                    </td>



                                    <td>

                                        <?php

                                        if (
                                            !empty($row['last_date'])
                                        ) {

                                            echo date(
                                                "d M Y",
                                                strtotime(
                                                    $row['last_date']
                                                )
                                            );
                                        } else {

                                            echo "—";
                                        }

                                        ?>

                                    </td>


                                </tr>

                            <?php

                            }

                            ?>


                        </tbody>

                    </table>

                </div>


            <?php

            } else {

            ?>


                <div class="no-data">

                    📭

                    <br><br>

                    No admission records are
                    currently available.

                </div>


            <?php

            }

            ?>

        </div>



        <!-- =========================================================
     ADMISSION INFORMATION
========================================================= -->

        <div class="info-section">

            <h2>

                ℹ️ Admission Information

            </h2>


            <ul>

                <li>
                    Students should check the eligibility
                    criteria before applying.
                </li>

                <li>
                    Admission is subject to availability
                    of seats.
                </li>

                <li>
                    Students should submit all required
                    documents during the admission process.
                </li>

                <li>
                    Admission dates may change according
                    to university and government guidelines.
                </li>

                <li>
                    Please refer to the Admission Policy
                    and Prospectus for complete details.
                </li>

            </ul>

        </div>


    </div>



    <!-- =========================================================
     FOOTER
========================================================= -->

    <footer class="footer">

        <p>

            © <?php echo date("Y"); ?>

            Yeshwant Mahavidyalaya, Nanded

        </p>


        <p>

            All Rights Reserved.

        </p>

    </footer>


</body>

</html>