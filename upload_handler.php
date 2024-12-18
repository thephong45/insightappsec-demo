<?php
// Define the absolute path to the target directory outside the admin folder
$target_dir = __DIR__ . '/images/';

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = [];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif']; // Define allowed MIME types

    if (isset($_FILES['file'])) {
        $file_name = basename($_FILES['file']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = mime_content_type($_FILES['file']['tmp_name']);

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                echo '/images/' . $file_name; // Return the relative path to the client
            } else {
                http_response_code(500);
                echo "Error uploading file.";
            }
        } else {
            http_response_code(400);
            echo "Invalid file type. Only images are allowed.";
        }
    } elseif (isset($_FILES['files'])) {
        foreach ($_FILES['files']['name'] as $key => $file_name) {
            $target_file = $target_dir . basename($file_name);
            $file_type = mime_content_type($_FILES['files']['tmp_name'][$key]);

            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target_file)) {
                    $response[] = '/images/' . $file_name; // Return the relative path to the client
                } else {
                    http_response_code(500);
                    echo json_encode(["Error uploading files."]);
                    exit;
                }
            } else {
                http_response_code(400);
                echo json_encode(["Invalid file type for $file_name. Only images are allowed."]);
                exit;
            }
        }
        echo json_encode($response);
    } else {
        http_response_code(400);
        echo "No files uploaded.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
?>
