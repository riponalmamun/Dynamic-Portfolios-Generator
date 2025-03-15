<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the email already exists
    $email_check_query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $email_check_query);
    
    if (mysqli_num_rows($result) > 0) {
        echo "Email is already taken. Please use a different one.";
    } else {
        // Insert the new user into the database
        $query = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
        if (mysqli_query($conn, $query)) {
            // Send confirmation email
            $subject = "Registration Confirmation";
            $message = "Thank you for registering! You can now log in.";
            $headers = "From: no-reply@yourwebsite.com";

            // Send email (make sure mail() is configured on your server)
            mail($email, $subject, $message, $headers);

            echo "Registration successful! You can now <a href='login.php'>login</a>.";
            // Optionally, redirect to login page
            // header('Location: login.php');
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script>
        // Password strength check function
        function checkPasswordStrength(password) {
            var strength = 0;
            if (password.length >= 8) {
                strength += 1;
            }
            if (/[A-Z]/.test(password)) {
                strength += 1;
            }
            if (/[a-z]/.test(password)) {
                strength += 1;
            }
            if (/[0-9]/.test(password)) {
                strength += 1;
            }
            if (/[!@#$%^&*()_+{}\[\]:;"'<>,.?/\\|]/.test(password)) {
                strength += 1;
            }
            return strength;
        }

        function updateStrengthMeter(password) {
            var strength = checkPasswordStrength(password);
            var strengthText = ["Very Weak", "Weak", "Fair", "Good", "Strong"];
            var color = ["#f00", "#f80", "#ff0", "#0f0", "#0f0"];
            document.getElementById('strength-text').textContent = strengthText[strength];
            document.getElementById('strength-text').style.color = color[strength];
        }
    </script>
</head>
<body>
<header><h2>Create an Account</h2></header>

<form action="register.php" method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" id="password" required onkeyup="updateStrengthMeter(this.value)"><br>
    <div id="strength-text" style="font-size: 14px; color: #f00;"></div>
    <button type="submit">Register</button>
</form>

</body>
</html>
