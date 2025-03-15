<?php
session_start();
include('database.php');

// Step 1 - Basic Information (Full Name, Contact Info, Bio, Soft Skills, Technical Skills)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $step = isset($_POST['step']) ? $_POST['step'] : 1;

    // Save form data to session
    if ($step == 1) {
        $_SESSION['full_name'] = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $_SESSION['contact_info'] = isset($_POST['contact_info']) ? trim($_POST['contact_info']) : '';
        $_SESSION['bio'] = isset($_POST['bio']) ? trim($_POST['bio']) : '';
        $_SESSION['soft_skills'] = isset($_POST['soft_skills']) ? trim($_POST['soft_skills']) : '';
        $_SESSION['technical_skills'] = isset($_POST['technical_skills']) ? trim($_POST['technical_skills']) : '';
    } elseif ($step == 2) {
        // Save academic background and work experience as arrays or empty strings if they are empty
        $_SESSION['academic_background'] = !empty($_POST['academic_background']) ? $_POST['academic_background'] : '';
        $_SESSION['work_experience'] = !empty($_POST['work_experience']) ? $_POST['work_experience'] : '';
    } elseif ($step == 3) {
        $_SESSION['projects'] = $_POST['projects'] ?? '';  // Ensure projects key is set

        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo = $_FILES['photo']['name'];
            $target_dir = "uploads/";
            $imageFileType = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
            if (in_array($imageFileType, ['jpg', 'png'])) {
                $unique_filename = time() . '_' . uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $unique_filename;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $_SESSION['photo'] = $unique_filename;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    exit();
                }
            } else {
                echo "Only JPG and PNG files are allowed.";
                exit();
            }
        }
    } elseif ($step == 4) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            echo "User ID is not set.";
            exit();
        }

        // Get data from session or set to empty string if not available
        $full_name = $_SESSION['full_name'] ?? '';
        $contact_info = $_SESSION['contact_info'] ?? '';
        $bio = $_SESSION['bio'] ?? '';
        $soft_skills = $_SESSION['soft_skills'] ?? '';
        $technical_skills = $_SESSION['technical_skills'] ?? '';
        $photo = $_SESSION['photo'] ?? '';
        $academic_background = isset($_SESSION['academic_background']) ? json_encode($_SESSION['academic_background']) : NULL;
        $work_experience = isset($_SESSION['work_experience']) ? json_encode($_SESSION['work_experience']) : NULL;
        $projects = $_SESSION['projects'] ?? '';

        // Prepare SQL query to insert data into the database
        $query = "INSERT INTO portfolios (user_id, full_name, contact_info, photo, bio, soft_skills, technical_skills, academic_background, work_experience, projects) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'isssssssss', 
                $user_id, 
                $full_name, 
                $contact_info, 
                $photo, 
                $bio, 
                $soft_skills, 
                $technical_skills, 
                $academic_background, 
                $work_experience, 
                $projects
            );

            if (mysqli_stmt_execute($stmt)) {
                echo "Portfolio saved successfully!";
                session_unset();  // Clear session data after successful save
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Portfolio</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    

<h2>Create Your Portfolio</h2>

<form action="portfolio_form.php" method="POST" enctype="multipart/form-data">

    <?php
    // Step 1 - Basic Information
    if (!isset($_POST['step']) || $_POST['step'] == 1): ?>
        <h3>Step 1: Basic Information</h3>
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" placeholder="Your full name" value="<?php echo $_SESSION['full_name'] ?? ''; ?>" required><br>

        <label for="contact_info">Contact Info:</label>
        <input type="text" name="contact_info" id="contact_info" placeholder="Your contact details" value="<?php echo $_SESSION['contact_info'] ?? ''; ?>" required><br>

        <label for="bio">Short Bio:</label>
        <textarea name="bio" id="bio" placeholder="Write a short bio" required><?php echo $_SESSION['bio'] ?? ''; ?></textarea><br>

        <label for="soft_skills">Soft Skills:</label>
        <textarea name="soft_skills" id="soft_skills" placeholder="e.g., Communication, Leadership"><?php echo $_SESSION['soft_skills'] ?? ''; ?></textarea><br>

        <label for="technical_skills">Technical Skills:</label>
        <textarea name="technical_skills" id="technical_skills" placeholder="e.g., JavaScript, Python"><?php echo $_SESSION['technical_skills'] ?? ''; ?></textarea><br>

        <button type="submit" name="step" value="2">Next: Academic Background & Work Experience</button>
    <?php endif; ?>

    <?php
    // Step 2 - Academic Background & Work Experience
    if (isset($_POST['step']) && $_POST['step'] == 2): ?>
        <h3>Step 2: Academic Background</h3>
        
        <label for="academic_background[institute]">Institute:</label>
        <input type="text" name="academic_background[institute]" placeholder="e.g., XYZ University" value="<?php echo $_SESSION['academic_background']['institute'] ?? ''; ?>"><br>

        <label for="academic_background[degree]">Degree:</label>
        <input type="text" name="academic_background[degree]" placeholder="e.g., BSc in Computer Science" value="<?php echo $_SESSION['academic_background']['degree'] ?? ''; ?>"><br>

        <label for="academic_background[year]">Year of Graduation:</label>
        <input type="text" name="academic_background[year]" placeholder="e.g., 2023" value="<?php echo $_SESSION['academic_background']['year'] ?? ''; ?>"><br>

        <label for="academic_background[grade]">Grade:</label>
        <input type="text" name="academic_background[grade]" placeholder="e.g., A" value="<?php echo $_SESSION['academic_background']['grade'] ?? ''; ?>"><br>

        <h3>Work Experience</h3>
        <label for="work_experience[job_title][]">Job Title:</label>
        <input type="text" name="work_experience[job_title][]" placeholder="Job Title" value="<?php echo $_SESSION['work_experience']['job_title'][0] ?? ''; ?>"><br>

        <label for="work_experience[company][]">Company:</label>
        <input type="text" name="work_experience[company][]" placeholder="Company Name" value="<?php echo $_SESSION['work_experience']['company'][0] ?? ''; ?>"><br>

        <label for="work_experience[period][]">Period:</label>
        <input type="text" name="work_experience[period][]" placeholder="Work Period" value="<?php echo $_SESSION['work_experience']['period'][0] ?? ''; ?>"><br>

        <label for="work_experience[description][]">Job Description:</label>
        <textarea name="work_experience[description][]" placeholder="Job Description"><?php echo $_SESSION['work_experience']['description'][0] ?? ''; ?></textarea><br>

        <button type="submit" name="step" value="3">Next: Photo Upload & Projects</button>
    <?php endif; ?>

    <?php
    // Step 3 - Photo Upload & Projects
    if (isset($_POST['step']) && $_POST['step'] == 3): ?>
        <h3>Step 3: Photo Upload & Projects</h3>

        <label for="photo">Upload a Photo (JPG/PNG only):</label>
        <input type="file" name="photo" accept=".jpg, .png" required><br>

        <label for="projects">Projects/Publications:</label>
        <textarea name="projects" id="projects" placeholder="Optional information about your projects"><?php echo $_SESSION['projects'] ?? ''; ?></textarea><br>

        <button type="submit" name="step" value="4">Submit Portfolio</button>
    <?php endif; ?>

</form>

</body>
</html>
