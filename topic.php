<?php

include "db.php";

/* =========================================================
   GET TOPIC ID
========================================================= */

$topic_id = 0;

if (isset($_GET['id'])) {
    $topic_id = intval($_GET['id']);
}

if ($topic_id <= 0) {
    die("Topic not selected.");
}


/* =========================================================
   GET TOPIC DETAILS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        t.id,
        t.subject_id,
        t.topic_name,
        t.topic_description,
        s.subject_name,
        d.department_name

    FROM topics t

    INNER JOIN subjects s
        ON t.subject_id = s.id

    INNER JOIN departments d
        ON s.department_id = d.id

    WHERE t.id = ?
");

$stmt->bind_param(
    "i",
    $topic_id
);

$stmt->execute();

$result =
    $stmt->get_result();

$topic =
    $result->fetch_assoc();


if (!$topic) {
    die("Topic not found.");
}


/* =========================================================
   GET PDF / IMAGE MATERIALS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        id,
        topic_id,
        file_name,
        file_type,
        file_path,
        uploaded_at

    FROM topic_materials

    WHERE topic_id = ?

    ORDER BY id DESC
");

$stmt->bind_param(
    "i",
    $topic_id
);

$stmt->execute();

$materials =
    $stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>

<?= htmlspecialchars(
    $topic['topic_name']
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

    color:#222;

}


.container{

    width:90%;

    max-width:1000px;

    margin:40px auto;

}


/* BACK */

.back{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#0d47a1;

    font-weight:bold;

}


/* TOPIC HEADER */

.topic-header{

    background:#0d47a1;

    color:white;

    padding:30px;

    border-radius:14px;

    margin-bottom:25px;

}


.topic-header h1{

    margin:0 0 12px;

    font-size:32px;

}


.topic-header p{

    margin:5px 0;

}


/* DESCRIPTION */

.description{

    background:white;

    padding:25px;

    border-radius:12px;

    box-shadow:0 4px 15px #0002;

    margin-bottom:30px;

    line-height:1.8;

}


/* MATERIAL */

.material{

    background:white;

    padding:25px;

    margin:20px 0;

    border-radius:12px;

    box-shadow:0 4px 15px #0002;

}


.material h2{

    margin-top:0;

    color:#0d47a1;

    word-break:break-word;

}


/* PDF */

.pdf-box{

    background:#fff5f5;

    border:1px solid #ffcdd2;

    padding:20px;

    border-radius:10px;

}


.pdf-icon{

    font-size:45px;

}


.pdf{

    display:inline-block;

    padding:11px 18px;

    background:#d32f2f;

    color:white;

    text-decoration:none;

    border-radius:6px;

    margin-right:8px;

}


.download{

    display:inline-block;

    padding:11px 18px;

    background:#0d47a1;

    color:white;

    text-decoration:none;

    border-radius:6px;

}


/* IMAGE */

.image-box{

    text-align:center;

}


.image-box img{

    max-width:100%;

    max-height:550px;

    border-radius:10px;

    border:1px solid #ddd;

    margin:10px auto 20px;

    display:block;

}


/* IMAGE BUTTON */

.image-view{

    display:inline-block;

    padding:11px 18px;

    background:#198754;

    color:white;

    text-decoration:none;

    border-radius:6px;

}


/* NO MATERIAL */

.no-material{

    background:white;

    padding:30px;

    text-align:center;

    border-radius:12px;

    color:#777;

    box-shadow:0 4px 15px #0002;

}


/* INFO */

.upload-date{

    color:#777;

    font-size:13px;

    margin-top:15px;

}


</style>

</head>


<body>


<div class="container">


<!-- BACK -->

<a
    class="back"
    href="javascript:history.back()"
>

← Back

</a>


<!-- =====================================================
     TOPIC HEADER
===================================================== -->

<div class="topic-header">


<h1>

📖

<?= htmlspecialchars(
    $topic['topic_name']
) ?>

</h1>


<p>

🏫

<?= htmlspecialchars(
    $topic['department_name']
) ?>

→

<?= htmlspecialchars(
    $topic['subject_name']
) ?>

</p>


</div>


<!-- =====================================================
     TOPIC DESCRIPTION
===================================================== -->

<?php if (
    !empty($topic['topic_description'])
): ?>

<div class="description">

<h2>

📚 About This Topic

</h2>


<p>

<?= nl2br(
    htmlspecialchars(
        $topic['topic_description']
    )
) ?>

</p>

</div>

<?php endif; ?>


<!-- =====================================================
     MATERIALS
===================================================== -->

<h2>

📁 Study Materials

</h2>


<?php if (
    $materials->num_rows == 0
): ?>


<div class="no-material">

<h3>

📭 No Study Material Available

</h3>

<p>

PDF or Images have not been uploaded for this topic yet.

</p>

</div>


<?php endif; ?>


<?php while (
    $m = $materials->fetch_assoc()
): ?>


<div class="material">


<h2>

<?php if (
    $m['file_type'] == 'PDF'
): ?>

📄

<?php else: ?>

🖼️

<?php endif; ?>


<?= htmlspecialchars(
    $m['file_name']
) ?>

</h2>


<!-- =====================================================
     PDF
===================================================== -->

<?php if (
    $m['file_type'] == 'PDF'
): ?>


<div class="pdf-box">


<div class="pdf-icon">

📄

</div>


<p>

<strong>PDF Study Material</strong>

</p>


<a
    class="pdf"
    href="<?= htmlspecialchars(
        $m['file_path']
    ) ?>"
    target="_blank"
>

👁️ View PDF

</a>


<a
    class="download"
    href="<?= htmlspecialchars(
        $m['file_path']
    ) ?>"
    download
>

⬇️ Download PDF

</a>


</div>


<!-- =====================================================
     IMAGE
===================================================== -->

<?php else: ?>


<div class="image-box">


<img
    src="<?= htmlspecialchars(
        $m['file_path']
    ) ?>"
    alt="<?= htmlspecialchars(
        $m['file_name']
    ) ?>"
>


<a
    class="image-view"
    href="<?= htmlspecialchars(
        $m['file_path']
    ) ?>"
    target="_blank"
>

🔍 View Full Image

</a>


<a
    class="download"
    href="<?= htmlspecialchars(
        $m['file_path']
    ) ?>"
    download
>

⬇️ Download Image

</a>


</div>


<?php endif; ?>


<?php if (
    !empty($m['uploaded_at'])
): ?>

<div class="upload-date">

Uploaded:
<?= htmlspecialchars(
    $m['uploaded_at']
) ?>

</div>

<?php endif; ?>


</div>


<?php endwhile; ?>


</div>


</body>

</html>