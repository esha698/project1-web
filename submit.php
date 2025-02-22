<?php
// Define allowed file types and max file size
$allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    // Handle file upload
    if (!empty($_FILES['file']['name'])) {
        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = "Invalid file type. Allowed types: " . implode(', ', $allowedTypes);
        }
        if ($fileSize > $maxFileSize) {
            $errors[] = "File size exceeds the maximum limit of 2MB.";
        }
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Save uploaded file (if any)
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = 'uploads/';
            $uploadPath = $uploadDir . uniqid() . '_' . basename($fileName);
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                $fileStatus = "File uploaded successfully.";
            } else {
                $fileStatus = "File upload failed.";
            }
        } else {
            $fileStatus = "No file uploaded.";
        }

        // Display success message
        echo "<div class='container mt-5'>
                <h2 class='text-success'>Form Submitted Successfully!</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong> $message</p>
                <p><strong>File Status:</strong> $fileStatus</p>
              </div>";
    } else {
        // Display errors
        echo "<div class='container mt-5'>
                <h2 class='text-danger'>Error!</h2>
                <ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></div>";
    }
} else {
    echo "<div class='container mt-5'>
            <h2 class='text-danger'>Invalid request.</h2>
          </div>";
}
?>

