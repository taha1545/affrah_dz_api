<?php


//  methode create video 
//  methode create image 

class UploadVideo
{
    public static function CreateVideo($file)
    {
        // Check if the file is valid
        if (isset($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK && isset($file['tmp_name']) &&  isset($file['name'])) {
            // 
            $fileTmpPath = $file['tmp_name'];
            $originalFileName = $file['name'];
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $uniqueName = uniqid('video_', true) . '.' . $fileExtension;
            //
            $uploadDir = 'upload/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // 
            $destinationPath = $uploadDir . $uniqueName;
            // 
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                return [
                    'name' => $uniqueName,
                    'path' => "/api/" . $uploadDir,
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
        // 
        if (isset($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK && isset($file['tmp_name']) &&  isset($file['name'])) {
            // 
            $fileTmpPath = $file['tmp_name'];
            $originalFileName = $file['name'];
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $uniqueName = uniqid('IMAGE_', true) . '.' . $fileExtension;
            // 
            $uploadDir = 'upload/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // 
            $destinationPath = $uploadDir . $uniqueName;
            // 
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                return [
                    'name' => $uniqueName,
                    'path' =>  "/api/" . $uploadDir,
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
