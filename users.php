<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($action == 'register') {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $response = array(
                'status' => 'error',
                'message' => 'Email already exists.'
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);

            if ($stmt->execute()) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Registration successful! Please log in.'
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Error: ' . $stmt->error
                );
            }
            $stmt->close();
        }
        $checkEmail->close();
    } elseif ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
        
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email;
        
                $response = array(
                    'status' => 'success',
                    'message' => 'Login successful'
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Invalid password!'
                );
            }
        } else {
            $response = array(
                'status' => 'no_user',
                'message' => 'No user found with this email! <br> Redirecting to sign up...'
            );
        }
        
        $stmt->close();
    }

    $conn->close();
    echo json_encode($response);
}
?>