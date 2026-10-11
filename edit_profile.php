<?php
session_start();

/*
=====================================================
DATABASE CONNECTION
=====================================================
*/
$conn = mysqli_connect("localhost", "root", "", "campusconnect");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

/*
=====================================================
CHECK LOGIN
=====================================================
*/
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: registration/login.php");
    exit();
}

/*
=====================================================
GET USER
=====================================================
*/
$user = null;

if (isset($_SESSION['user_id'])) {

    $user_id = (int)$_SESSION['user_id'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
} elseif (isset($_SESSION['username'])) {

    $username = $_SESSION['username'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE username = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}

/*
=====================================================
USER NOT FOUND
=====================================================
*/
if (!$user) {
    session_unset();
    session_destroy();

    header("Location: registration/login.php");
    exit();
}

/*
=====================================================
USER DATA
=====================================================
*/
$user_id = (int)$user['id'];

$username = $user['username'] ?? '';
$name     = $user['name'] ?? '';
$email    = $user['email'] ?? '';
$mobile   = $user['mobile'] ?? '';
$address  = $user['address'] ?? '';
$college  = $user['college'] ?? '';
$course   = $user['course'] ?? '';
$year     = $user['year'] ?? '';

$profile_image = $user['profile_image'] ?? '';

$error = '';
$success = '';

/*
=====================================================
UPDATE PROFILE
=====================================================
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $mobile  = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $college = trim($_POST['college'] ?? '');
    $course  = trim($_POST['course'] ?? '');
    $year    = trim($_POST['year'] ?? '');

    /*
    =================================================
    BASIC VALIDATION
    =================================================
    */

    if ($name === '') {
        $error = "Please enter your full name.";
    } elseif ($email === '') {
        $error = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    /*
    =================================================
    IMAGE UPLOAD
    =================================================
    */

    $new_image_name = $profile_image;

    if ($error === '' && isset($_FILES['profile_image'])) {

        if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {

                $error = "There was an error uploading the image.";
            } else {

                $allowed_extensions = [
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'webp'
                ];

                $file_name = $_FILES['profile_image']['name'];
                $file_tmp  = $_FILES['profile_image']['tmp_name'];
                $file_size = $_FILES['profile_image']['size'];

                $extension = strtolower(
                    pathinfo($file_name, PATHINFO_EXTENSION)
                );

                /*
                -----------------------------------------
                FILE TYPE
                -----------------------------------------
                */

                if (!in_array($extension, $allowed_extensions, true)) {

                    $error = "Only JPG, JPEG, PNG, GIF and WEBP images are allowed.";

                    /*
                -----------------------------------------
                FILE SIZE
                -----------------------------------------
                */
                } elseif ($file_size > 5 * 1024 * 1024) {

                    $error = "Profile image must be less than 5MB.";
                } else {

                    /*
                    -------------------------------------
                    CHECK REAL IMAGE
                    -------------------------------------
                    */

                    $image_info = @getimagesize($file_tmp);

                    if ($image_info === false) {

                        $error = "Please select a valid image file.";
                    } else {

                        /*
                        ---------------------------------
                        CREATE FOLDER
                        ---------------------------------
                        */

                        $upload_directory =
                            __DIR__ . DIRECTORY_SEPARATOR .
                            "uploads" . DIRECTORY_SEPARATOR .
                            "profile_images";

                        if (!is_dir($upload_directory)) {

                            if (!mkdir($upload_directory, 0777, true)) {
                                $error = "Unable to create upload folder.";
                            }
                        }

                        /*
                        ---------------------------------
                        UPLOAD IMAGE
                        ---------------------------------
                        */

                        if ($error === '') {

                            $new_image_name =
                                time() . '_' .
                                mt_rand(1000, 9999) . '.' .
                                $extension;

                            $destination =
                                $upload_directory .
                                DIRECTORY_SEPARATOR .
                                $new_image_name;

                            if (!move_uploaded_file(
                                $file_tmp,
                                $destination
                            )) {

                                $error = "Unable to upload profile image.";

                                $new_image_name = $profile_image;
                            } else {

                                /*
                                ---------------------------------
                                DELETE OLD IMAGE
                                ---------------------------------
                                */

                                if (!empty($profile_image)) {

                                    $old_image =
                                        $upload_directory .
                                        DIRECTORY_SEPARATOR .
                                        basename($profile_image);

                                    if (
                                        file_exists($old_image) &&
                                        is_file($old_image)
                                    ) {
                                        @unlink($old_image);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /*
    =================================================
    UPDATE DATABASE
    =================================================
    */

    if ($error === '') {

        $update_sql = "
            UPDATE users SET
                name = ?,
                email = ?,
                mobile = ?,
                address = ?,
                college = ?,
                course = ?,
                year = ?,
                profile_image = ?
            WHERE id = ?
        ";

        $stmt = mysqli_prepare($conn, $update_sql);

        if (!$stmt) {

            $error = "Database error: " . mysqli_error($conn);
        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssi",
                $name,
                $email,
                $mobile,
                $address,
                $college,
                $course,
                $year,
                $new_image_name,
                $user_id
            );

            if (mysqli_stmt_execute($stmt)) {

                /*
                -----------------------------------------
                UPDATE SESSION
                -----------------------------------------
                */

                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user_id;

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                echo "
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Profile Updated</title>
                    <meta charset='UTF-8'>
                </head>
                <body>

                <script>
                    alert('Profile Updated Successfully!');
                    window.location.href = 'dashboard.php';
                </script>

                </body>
                </html>
                ";

                exit();
            } else {

                $error = "Unable to update profile: " .
                    mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }

    /*
    If error occurred, keep new value only if upload failed.
    */
    $profile_image = $new_image_name;
}

/*
=====================================================
PROFILE IMAGE FOR PREVIEW
=====================================================
*/

$profile_image_url = '';

if (!empty($profile_image)) {

    $safe_image = basename($profile_image);

    $physical_path =
        __DIR__ .
        "/uploads/profile_images/" .
        $safe_image;

    if (file_exists($physical_path)) {

        $profile_image_url =
            "uploads/profile_images/" .
            rawurlencode($safe_image);
    }
}

/*
=====================================================
INITIALS
=====================================================
*/

$display_name = trim($name);

if ($display_name === '') {
    $display_name = $username;
}

$name_parts = preg_split('/\s+/', $display_name);

if (count($name_parts) >= 2) {

    $initials =
        strtoupper(
            substr($name_parts[0], 0, 1) .
                substr($name_parts[1], 0, 1)
        );
} else {

    $initials =
        strtoupper(substr($display_name, 0, 2));
}

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Profile | Yeshwant Mahavidyalaya</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f7fc;
            color: #163b63;
        }

        .topbar {
            background: #06458f;
            color: white;
            padding: 12px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .topbar span {
            margin-right: 8px;
        }

        .header {
            background: white;
            min-height: 85px;
            padding: 12px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .college-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .college-logo {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            object-fit: cover;
        }

        .college-info h1 {
            color: #06458f;
            font-size: 22px;
        }

        .college-info p {
            color: #607d99;
            margin-top: 5px;
            font-size: 13px;
        }

        .nav a {
            text-decoration: none;
            color: #06458f;
            font-weight: bold;
            margin-left: 22px;
        }

        .nav a:hover {
            color: #e5a900;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5eaf0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .card-header h2 {
            color: #123d67;
        }

        .profile-section {
            display: flex;
            gap: 30px;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f7faff;
            border-radius: 12px;
        }

        .avatar-preview {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            overflow: hidden;
            background: #123f68;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 42px;
            font-weight: bold;
            flex-shrink: 0;
            border: 5px solid #e5edf7;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-box {
            flex: 1;
        }

        .upload-box h3 {
            margin-bottom: 8px;
        }

        .file-input {
            margin-top: 15px;
        }

        input[type="file"] {
            display: none;
        }

        .choose-btn {
            display: inline-block;
            background: #06458f;
            color: white;
            padding: 11px 18px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
        }

        .choose-btn:hover {
            background: #082f5e;
        }

        .file-name {
            margin-left: 10px;
            color: #71869c;
            font-size: 14px;
        }

        .hint {
            margin-top: 10px;
            color: #71869c;
            font-size: 13px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #254d73;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd8e6;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #06458f;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            border: none;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
        }

        .btn-primary {
            background: #06458f;
            color: white;
        }

        .btn-primary:hover {
            background: #082f5e;
        }

        .btn-secondary {
            background: #e8edf3;
            color: #234564;
        }

        .btn-secondary:hover {
            background: #dce4ec;
        }

        .alert {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #ffe8e8;
            color: #a40000;
            border: 1px solid #f4bcbc;
        }

        @media(max-width: 800px) {

            .header {
                flex-direction: column;
                gap: 15px;
            }

            .nav {
                text-align: center;
            }

            .nav a {
                display: inline-block;
                margin: 5px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }
        }

        @media(max-width: 600px) {

            .topbar {
                flex-direction: column;
                gap: 5px;
            }

            .college-info h1 {
                font-size: 18px;
            }

            .profile-section {
                flex-direction: column;
                text-align: center;
            }

            .file-name {
                display: block;
                margin: 10px 0;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>

</head>

<body>

    <!-- TOP BAR -->

    <div class="topbar">

        <div>
            <i class="fas fa-building-columns"></i>
            Shri Sharda Bhavan Education Society's
        </div>

        <div>
            <i class="fas fa-envelope"></i>
            principalymn@gmail.com
        </div>

    </div>


    <!-- HEADER -->

    <header class="header">

        <div class="college-info">

            <img
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Emblem_of_India.svg/1200px-Emblem_of_India.svg.png"
                class="college-logo"
                alt="College Logo">

            <div>

                <h1>
                    Yeshwant Mahavidyalaya, Nanded
                </h1>

                <p>
                    Affiliated to S. R. T. M. University, Nanded
                </p>

            </div>

        </div>

        <nav class="nav">

            <a href="dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>

            <a href="dashboard.php">
                <i class="fas fa-user"></i> Profile
            </a>

        </nav>

    </header>


    <!-- MAIN -->

    <div class="container">

        <div class="card">

            <div class="card-header">

                <h2>
                    <i class="fas fa-user-edit"></i>
                    Edit Profile
                </h2>

                <span>
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($username); ?>
                </span>

            </div>


            <?php if ($error !== ''): ?>

                <div class="alert alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                enctype="multipart/form-data">

                <!-- PROFILE IMAGE -->

                <div class="profile-section">

                    <div class="avatar-preview">

                        <?php if ($profile_image_url !== ''): ?>

                            <img
                                src="<?php echo htmlspecialchars($profile_image_url); ?>"
                                alt="Profile Picture">

                        <?php else: ?>

                            <?php echo htmlspecialchars($initials); ?>

                        <?php endif; ?>

                    </div>


                    <div class="upload-box">

                        <h3>
                            <i class="fas fa-camera"></i>
                            Profile Picture
                        </h3>

                        <p>
                            Upload your profile picture.
                        </p>

                        <div class="file-input">

                            <input
                                type="file"
                                id="profile_image"
                                name="profile_image"
                                accept=".jpg,.jpeg,.png,.gif,.webp,image/*">

                            <label
                                for="profile_image"
                                class="choose-btn">
                                <i class="fas fa-upload"></i>
                                Choose Image
                            </label>

                            <span
                                class="file-name"
                                id="file-name">
                                No file selected
                            </span>

                        </div>

                        <div class="hint">
                            JPG, JPEG, PNG, GIF or WEBP | Maximum 5MB
                        </div>

                    </div>

                </div>


                <!-- FORM -->

                <div class="form-grid">

                    <div class="form-group">

                        <label for="name">
                            <i class="fas fa-user"></i>
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?php echo htmlspecialchars($name); ?>"
                            required>

                    </div>


                    <div class="form-group">

                        <label for="username">
                            <i class="fas fa-at"></i>
                            Username
                        </label>

                        <input
                            type="text"
                            value="<?php echo htmlspecialchars($username); ?>"
                            disabled>

                    </div>


                    <div class="form-group">

                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($email); ?>"
                            required>

                    </div>


                    <div class="form-group">

                        <label for="mobile">
                            <i class="fas fa-phone"></i>
                            Mobile
                        </label>

                        <input
                            type="text"
                            id="mobile"
                            name="mobile"
                            value="<?php echo htmlspecialchars($mobile); ?>">

                    </div>


                    <div class="form-group full">

                        <label for="address">
                            <i class="fas fa-location-dot"></i>
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"><?php echo htmlspecialchars($address); ?></textarea>

                    </div>


                    <div class="form-group">

                        <label for="college">
                            <i class="fas fa-building-columns"></i>
                            College
                        </label>

                        <input
                            type="text"
                            id="college"
                            name="college"
                            value="<?php echo htmlspecialchars($college); ?>">

                    </div>


                    <div class="form-group">

                        <label for="course">
                            <i class="fas fa-book"></i>
                            Course
                        </label>

                        <input
                            type="text"
                            id="course"
                            name="course"
                            value="<?php echo htmlspecialchars($course); ?>">

                    </div>


                    <div class="form-group">

                        <label for="year">
                            <i class="fas fa-calendar"></i>
                            Year
                        </label>

                        <input
                            type="text"
                            id="year"
                            name="year"
                            value="<?php echo htmlspecialchars($year); ?>">

                    </div>

                </div>


                <!-- BUTTONS -->

                <div class="buttons">

                    <button
                        type="submit"
                        name="update"
                        class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>

                    <a
                        href="dashboard.php"
                        class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>


    <script>
        document
            .getElementById('profile_image')
            .addEventListener('change', function() {

                const file = this.files[0];

                const fileName =
                    document.getElementById('file-name');

                if (file) {

                    fileName.innerHTML =
                        '<i class="fas fa-check-circle"></i> ' +
                        file.name;

                } else {

                    fileName.innerHTML =
                        'No file selected';

                }

            });
    </script>

</body>

</html>