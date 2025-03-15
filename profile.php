<?php
session_start();
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's portfolio details from the database
$query = "SELECT * FROM portfolios WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$portfolio = mysqli_fetch_assoc($result);

// If no portfolio exists, direct them to create one
if (!$portfolio) {
    header('Location: portfolio_form.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Portfolio</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

<h2>Your Portfolio</h2>

<p><strong>Full Name:</strong> <?php echo htmlspecialchars($portfolio['full_name']); ?></p>
<p><strong>Contact Info:</strong> <?php echo htmlspecialchars($portfolio['contact_info']); ?></p>
<p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($portfolio['bio'])); ?></p>
<p><strong>Soft Skills:</strong> <?php echo nl2br(htmlspecialchars($portfolio['soft_skills'])); ?></p>
<p><strong>Technical Skills:</strong> <?php echo nl2br(htmlspecialchars($portfolio['technical_skills'])); ?></p>
<p><strong>Academic Background:</strong> <?php echo nl2br(htmlspecialchars($portfolio['academic_background'])); ?></p>
<p><strong>Work Experience:</strong> <?php echo nl2br(htmlspecialchars($portfolio['work_experience'])); ?></p>
<p><strong>Projects/Publications:</strong> <?php echo nl2br(htmlspecialchars($portfolio['projects'])); ?></p>

<p><strong>Photo:</strong><br><img src="uploads/<?php echo $portfolio['photo']; ?>" alt="Profile Photo" width="200"></p>

<a href="portfolio_form.php">Edit Portfolio</a>
<p><a href="generate_pdf.php">Download PDF</a></p>


</body>
</html>
