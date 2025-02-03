<?php

class UploadVideo
{

    public static function CreateVideo($file)
    {
        // Check if the file is valid
        if (isset($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK && isset($file['tmp_name']) &&  isset($file['name'])) {
            // Generate a unique name for the video
            $fileTmpPath = $file['tmp_name'];
            $originalFileName = $file['name'];
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $uniqueName = uniqid('video_', true) . '.' . $fileExtension;
            // Define the upload directory
            $uploadDir = 'upload/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Define the full path for the uploaded file
            $destinationPath = $uploadDir . $uniqueName;
            // Move the uploaded file to the upload directory
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                return [
                    'name' => $uniqueName,
                    'path' =>"/api/". $uploadDir,
                    'size' => $file['size'] ?? 1
                ];
            } else {
                throw new Exception('Failed to move the uploaded file');
            }
        } else {
            throw new Exception('Invalid file or upload error ');
        }
    }

    public static function CreateImage($file)
    {
        // Check if the file is valid
        if (isset($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK && isset($file['tmp_name']) &&  isset($file['name'])) {
            // Generate a unique name for the video
            $fileTmpPath = $file['tmp_name'];
            $originalFileName = $file['name'];
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $uniqueName = uniqid('IMAGE_', true) . '.' . $fileExtension;
            // Define the upload directory
            $uploadDir = 'upload/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Define the full path for the uploaded file
            $destinationPath = $uploadDir . $uniqueName;
            // Move the uploaded file to the upload directory
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                return [
                    'name' => $uniqueName,
                    'path' =>  "/api/".$uploadDir,
                    'size' => $file['size'] ?? 1,
                    'type' => $fileExtension
                ];
            } else {
                throw new Exception('Failed to move the uploaded file');
            }
        } else {
            throw new Exception('Invalid file or upload error ');
        }
    }
}
