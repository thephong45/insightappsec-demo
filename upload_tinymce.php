<?php
$targetDir = "images/";
$file = $_FILES["file"];
$targetFile = $targetDir . basename($file["name"]);
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
$uploadOk = 1;
$response = ['location' => '', 'error' => ''];

// Check if image file is an actual image or fake image
$check = getimagesize($file["tmp_name"]);
if ($check !== false) {
    $uploadOk = 1;
} else {
    $response['error'] = 'File is not an image.';
    $uploadOk = 0;
}

// Check if file already exists
if (file_exists($targetFile)) {
    // If file exists, generate a new unique name
    $uniqueId = uniqid();
    $targetFile = $targetDir . $uniqueId . '.' . $imageFileType;
}

// Check file size
if ($file["size"] > 5000000) { // 5MB
    $response['error'] = 'Sorry, your file is too large.';
    $uploadOk = 0;
}

// Allow certain file formats
if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
    $response['error'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo json_encode($response);
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $response['location'] = $targetFile;
    } else {
        $response['error'] = 'Sorry, there was an error uploading your file.';
    }
    echo json_encode($response);
}
?>
