<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "campusconnect");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle form submission
$success_message = '';
$error_message = '';

if (isset($_POST['submit_contact'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Insert into database
        $insert_sql = "INSERT INTO contact_messages (name, email, subject, message, created_at, status) 
                       VALUES ('$name', '$email', '$subject', '$message', NOW(), 'unread')";

        if (mysqli_query($conn, $insert_sql)) {
            $success_message = "Your message has been sent successfully! We'll get back to you soon.";

            // Clear form fields
            $name = $email = $subject = $message = '';
        } else {
            $error_message = "Failed to send message. Please try again.";
        }
    }
}

// Get user's messages (if logged in)
$user_messages = [];
if (isset($_SESSION['user_email'])) {
    $user_email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
    $user_messages_sql = "SELECT * FROM contact_messages WHERE email = '$user_email' ORDER BY created_at DESC LIMIT 10";
    $user_messages_result = mysqli_query($conn, $user_messages_sql);
    while ($row = mysqli_fetch_assoc($user_messages_result)) {
        $user_messages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us – Yeshwant Mahavidyalaya</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f7fc;
            color: #1a2634;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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


        /* ============================================
           PAGE LAYOUT
           ============================================ */
        .page-wrapper {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 32px 24px 0 24px;
        }

        /* ============================================
           PAGE HEADER
           ============================================ */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #0b2b4a;
            margin-bottom: 8px;
        }

        .page-header h1 i {
            color: #1a4a7a;
            margin-right: 12px;
        }

        .page-header p {
            color: #6b85a0;
            font-size: 1.05rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ============================================
           CONTACT GRID
           ============================================ */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 40px;
        }

        /* ===== CONTACT FORM ===== */
        .contact-form-wrapper {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f7;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .contact-form-wrapper h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #0b2b4a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-form-wrapper h2 i {
            color: #1a4a7a;
        }

        .contact-form-wrapper .subtitle {
            color: #6b85a0;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        /* Alert Messages */
        .alert {
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #e8f5e9;
            color: #1e7e34;
            border: 1px solid #c8e6c9;
        }

        .alert-success i {
            color: #2e7d32;
            font-size: 1.2rem;
        }

        .alert-error {
            background: #fce4ec;
            color: #c62828;
            border: 1px solid #f8bbd0;
        }

        .alert-error i {
            color: #c62828;
            font-size: 1.2rem;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #2c405a;
            margin-bottom: 6px;
        }

        .form-group label i {
            color: #1a4a7a;
            width: 20px;
            margin-right: 6px;
        }

        .form-group .required {
            color: #e74c3c;
            margin-left: 2px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 11px 16px;
            border: 1.5px solid #dce3ec;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s ease;
            background: #fafcff;
            color: #0b2b4a;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1a4a7a;
            box-shadow: 0 0 0 4px rgba(26, 74, 122, 0.08);
            background: white;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #0b2b4a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Inter', sans-serif;
        }

        .btn-submit:hover {
            background: #0a1f35;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 43, 74, 0.2);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit i {
            font-size: 1.1rem;
        }

        /* ===== CONTACT INFO ===== */
        .contact-info-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f7;
            padding: 24px 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .info-card .icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #e8f0fe;
            color: #1a4a7a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 14px;
        }

        .info-card h3 {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0b2b4a;
            margin-bottom: 4px;
        }

        .info-card p {
            color: #6b85a0;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .info-card p i {
            margin-right: 8px;
            color: #1a4a7a;
            width: 16px;
        }

        .info-card .social-links {
            display: flex;
            gap: 12px;
            margin-top: 12px;
        }

        .info-card .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f0f4fa;
            color: #2c405a;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.25s ease;
            font-size: 1.1rem;
        }

        .info-card .social-links a:hover {
            background: #0b2b4a;
            color: white;
            transform: translateY(-2px);
        }

        /* ============================================
           USER MESSAGES SECTION
           ============================================ */
        .user-messages-section {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f7;
            padding: 32px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .user-messages-section h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #0b2b4a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-messages-section h2 i {
            color: #1a4a7a;
        }

        .message-item {
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .message-item:hover {
            border-color: #d0dffa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .message-item .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .message-item .message-subject {
            font-weight: 600;
            color: #0b2b4a;
            font-size: 1rem;
        }

        .message-item .message-date {
            color: #6b85a0;
            font-size: 0.8rem;
        }

        .message-item .message-text {
            color: #2c405a;
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .message-item .message-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .message-item .message-status.unread {
            background: #fce4ec;
            color: #c62828;
        }

        .message-item .message-status.read {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .message-item .message-status.replied {
            background: #fff3e0;
            color: #e65100;
        }

        .message-item .message-reply {
            background: #f8faff;
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
            border-left: 4px solid #1a4a7a;
        }

        .message-item .message-reply strong {
            color: #0b2b4a;
            display: block;
            margin-bottom: 4px;
        }

        .message-item .message-reply p {
            color: #2c405a;
            margin: 0;
        }

        .message-item .message-reply .reply-meta {
            color: #6b85a0;
            font-size: 0.75rem;
            margin-top: 8px;
        }

        .no-messages {
            text-align: center;
            padding: 40px 20px;
            color: #8aa0b8;
        }

        .no-messages i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            display: block;
            color: #dce3ec;
        }

        /* ============================================
           MAP SECTION
           ============================================ */
        .map-section {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f7;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            margin-bottom: 40px;
        }

        .map-section iframe {
            width: 100%;
            height: 350px;
            border: none;
            display: block;
        }

        .map-section .map-content {
            padding: 20px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            border-top: 1px solid #eef2f7;
        }

        .map-section .map-content h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #0b2b4a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .map-section .map-content h3 i {
            color: #e74c3c;
        }

        .map-section .map-content a {
            color: #1a4a7a;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .map-section .map-content a:hover {
            color: #0b2b4a;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .footer {
            background: #0b2b4a;
            color: rgba(255, 255, 255, 0.7);
            padding: 24px 40px;
            text-align: center;
            margin-top: 32px;
            font-size: 0.85rem;
            border-top: 3px solid #1a4a7a;
        }

        .footer a {
            color: #6aadda;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer i {
            color: #6aadda;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .top-nav {
                padding: 0 1rem;
                height: 60px;
            }

            .nav-menu a {
                font-size: 0.7rem;
                padding: 6px 10px;
            }

            .nav-menu a span {
                display: none;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .top-nav {
                padding: 0 1rem;
                flex-direction: column;
                height: auto;
                padding: 12px 1rem;
                gap: 8px;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 2px;
            }

            .nav-menu a {
                font-size: 0.7rem;
                padding: 6px 10px;
            }

            .page-wrapper {
                padding: 20px 16px 0 16px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .contact-form-wrapper {
                padding: 24px 18px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .info-card {
                padding: 20px;
            }

            .map-section iframe {
                height: 250px;
            }

            .map-section .map-content {
                flex-direction: column;
                text-align: center;
            }

            .footer {
                padding: 20px 16px;
                font-size: 0.75rem;
            }

            .user-messages-section {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .nav-brand h2 {
                font-size: 0.9rem;
            }

            .nav-brand small {
                font-size: 0.55rem;
            }

            .page-header h1 {
                font-size: 1.2rem;
            }

            .page-header h1 i {
                font-size: 1.2rem;
            }

            .page-header p {
                font-size: 0.9rem;
            }

            .contact-form-wrapper {
                padding: 18px 14px;
            }

            .contact-form-wrapper h2 {
                font-size: 1.1rem;
            }

            .info-card .icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .btn-submit {
                padding: 12px;
                font-size: 0.9rem;
            }

            .message-item .message-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* ============================================
           ANIMATIONS
           ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .contact-form-wrapper {
            animation: fadeInUp 0.4s ease-out;
        }

        .info-card {
            animation: fadeInUp 0.5s ease-out;
        }

        .info-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .info-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .map-section {
            animation: fadeInUp 0.6s ease-out;
        }

        .user-messages-section {
            animation: fadeInUp 0.5s ease-out;
        }

        /* ============================================
           SCROLLBAR
           ============================================ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f0f4fc;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #1a4a7a, #0b2b4a);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0a1f35;
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

    <!-- ============================================
    PAGE CONTENT
    ============================================ -->
    <div class="page-wrapper">

        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-envelope-open-text"></i>
                Contact Us
            </h1>
            <p>Have questions or feedback? We'd love to hear from you. Reach out to us using the form below.</p>
        </div>

        <!-- Contact Grid -->
        <div class="contact-grid">

            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h2>
                    <i class="fas fa-paper-plane"></i>
                    Send a Message
                </h2>
                <p class="subtitle">Fill in the form below and we'll get back to you within 24 hours.</p>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user"></i> Full Name
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                                placeholder="Enter your full name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Email Address
                                <span class="required">*</span>
                            </label>
                            <input type="email" id="email" name="email"
                                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                                placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">
                            <i class="fas fa-tag"></i> Subject
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="subject" name="subject"
                            value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>"
                            placeholder="What is this regarding?" required>
                    </div>

                    <div class="form-group">
                        <label for="message">
                            <i class="fas fa-comment-dots"></i> Message
                            <span class="required">*</span>
                        </label>
                        <textarea id="message" name="message"
                            placeholder="Write your message here..." required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                    </div>

                    <button type="submit" name="submit_contact" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="contact-info-wrapper">
                <div class="info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Us</h3>
                    <p>
                        <i class="fas fa-location-dot"></i> Yeshwant Mahavidyalaya<br>
                        <i class="fas fa-road"></i> Main Road, Nanded<br>
                        <i class="fas fa-city"></i> Maharashtra, India - 431601
                    </p>
                </div>

                <div class="info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Contact Details</h3>
                    <p>
                        <i class="fas fa-phone"></i> +91 98765 43210<br>
                        <i class="fas fa-phone"></i> +91 87654 32109<br>
                        <i class="fas fa-envelope"></i> info@yeshwantcollege.edu.in<br>
                        <i class="fas fa-clock"></i> Mon-Fri: 9:00 AM - 5:00 PM
                    </p>
                </div>

                <div class="info-card">
                    <div class="icon-wrapper">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <h3>Connect With Us</h3>
                    <p>Follow us on social media for updates and announcements.</p>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Messages Section -->
        <?php if (!empty($user_messages)): ?>
            <div class="user-messages-section">
                <h2>
                    <i class="fas fa-history"></i>
                    Your Message History
                </h2>
                <?php foreach ($user_messages as $msg):
                    $status_class = isset($msg['status']) ? $msg['status'] : 'unread';
                    $status_icon = $status_class == 'unread' ? 'fa-circle' : ($status_class == 'read' ? 'fa-check-circle' : 'fa-reply-all');
                ?>
                    <div class="message-item">
                        <div class="message-header">
                            <span class="message-subject">
                                <i class="fas fa-tag" style="color:#1a4a7a;"></i>
                                <?php echo htmlspecialchars($msg['subject']); ?>
                            </span>
                            <span class="message-date">
                                <i class="fas fa-clock"></i>
                                <?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?>
                            </span>
                        </div>
                        <div class="message-text">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <span class="message-status <?php echo $status_class; ?>">
                                <i class="fas <?php echo $status_icon; ?>"></i>
                                <?php echo ucfirst($status_class); ?>
                            </span>
                        </div>
                        <?php if (!empty($msg['reply'])): ?>
                            <div class="message-reply">
                                <strong><i class="fas fa-reply" style="color:#1a4a7a;"></i> Admin Reply:</strong>
                                <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                                <div class="reply-meta">
                                    <i class="fas fa-user"></i> Replied by: <?php echo htmlspecialchars($msg['replied_by']); ?>
                                    <span style="margin-left: 16px;">
                                        <i class="fas fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($msg['replied_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Map Section -->
        <div class="map-section">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3791.9441937489433!2d77.31568861487883!3d19.14031918706296!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bd1d6f0c3b8e6d7%3A0x4f8c9e7d6c5b4a3!2sYeshwant%20Mahavidyalaya%2C%20Nanded!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            <div class="map-content">
                <h3>
                    <i class="fas fa-map-pin"></i>
                    Find Us on Google Maps
                </h3>
                <a href="#" target="_blank">
                    <i class="fas fa-directions"></i> Get Directions
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- ============================================
    FOOTER
    ============================================ -->
    <footer class="footer">
        <i class="fas fa-university"></i> Yeshwant Mahavidyalaya, Nanded &bull;
        &copy; 2026 All rights reserved &bull;
        <i class="fas fa-heart" style="color:#ff6b6b;"></i>
        <a href="#">CampusConnect</a>
    </footer>

</body>

</html>