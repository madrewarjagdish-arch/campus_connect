<?php

include "db.php";

/* =========================================================
   GET SUBJECT ID
========================================================= */

$subject_id = 0;

if (isset($_GET['id'])) {
    $subject_id = intval($_GET['id']);
}

if ($subject_id <= 0) {
    die("Subject not selected.");
}


/* =========================================================
   GET SUBJECT
========================================================= */

$stmt = $conn->prepare("
    SELECT
        s.id,
        s.subject_name,
        s.department_id,
        d.department_name
    FROM subjects s
    INNER JOIN departments d
        ON s.department_id = d.id
    WHERE s.id = ?
");

$stmt->bind_param(
    "i",
    $subject_id
);

$stmt->execute();

$subject_result =
    $stmt->get_result();

$subject =
    $subject_result->fetch_assoc();


if (!$subject) {
    die("Subject not found.");
}


/* =========================================================
   GET TOPICS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        id,
        subject_id,
        topic_name,
        topic_description
    FROM topics
    WHERE subject_id = ?
    ORDER BY id ASC
");

$stmt->bind_param(
    "i",
    $subject_id
);

$stmt->execute();

$topics =
    $stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>
<?= htmlspecialchars(
    $subject['subject_name']
) ?>
</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f7fb;
}

.container{
    width:90%;
    max-width:1000px;
    margin:40px auto;
}

.header{
    background:#0d47a1;
    color:white;
    padding:25px;
    border-radius:12px;
    margin-bottom:25px;
}

.header h1{
    margin:0 0 10px;
}

.header p{
    margin:0;
}

.topic{
    background:white;
    padding:22px;
    margin:18px 0;
    border-radius:12px;
    box-shadow:0 4px 15px #0002;
}

.topic h2{
    margin-top:0;
}

.topic h2 a{
    text-decoration:none;
    color:#0d47a1;
}

.topic p{
    color:#555;
    line-height:1.7;
}

.topic-btn{
    display:inline-block;
    margin-top:10px;
    padding:10px 16px;
    background:#0d47a1;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.no-topic{
    background:white;
    padding:25px;
    border-radius:10px;
    color:#777;
}

.back{
    display:inline-block;
    margin-bottom:20px;
    color:#0d47a1;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="container">


<a
    class="back"
    href="javascript:history.back()"
>

← Back

</a>


<!-- SUBJECT -->

<div class="header">

<h1>

📘

<?= htmlspecialchars(
    $subject['subject_name']
) ?>

</h1>

<p>

Department:

<?= htmlspecialchars(
    $subject['department_name']
) ?>

</p>

</div>


<!-- TOPICS -->

<h2>
📚 Topics
</h2>


<?php if ($topics->num_rows > 0): ?>


<?php while ($t = $topics->fetch_assoc()): ?>


<div class="topic">


<h2>

<a
    href="topic.php?id=<?= (int)$t['id'] ?>"
>

📖

<?= htmlspecialchars(
    $t['topic_name']
) ?>

</a>

</h2>


<?php if (
    !empty($t['topic_description'])
): ?>

<p>

<?= nl2br(
    htmlspecialchars(
        $t['topic_description']
    )
) ?>

</p>

<?php endif; ?>


<a
    class="topic-btn"
    href="topic.php?id=<?= (int)$t['id'] ?>"
>

📖 Open Topic

</a>


</div>


<?php endwhile; ?>


<?php else: ?>


<div class="no-topic">

⚠️ No topics available for this subject.

</div>


<?php endif; ?>


</div>

</body>

</html>