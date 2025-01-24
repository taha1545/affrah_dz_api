<?php
//
require_once __DIR__ . '/../Controller.php';
class Validator extends Controller
{
    protected $validationRules = [
        'client' => [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'wilaya' => ['required', 'string'],
            'age' => ['required', 'integer', 'min:1', 'max:3'],
            'email' => ['required', 'email', 'unique:client,email_c'],
            'phone' => ['required', 'string', 'min:6', 'max:14', 'unique:client,tel_c'],
            'password' => ['required', 'string', 'min:8'],
            'image' => ['required'],
        ],
        'annonce' => [
            'name' => ['required', 'string', 'max:255', 'unique:annonce,nom_an'],
            'category' => ['required', 'string', 'max:255'],
            'eventType' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'creationDate' => ['date'],
            'phone' => ['string', 'max:30'],
            'mobile' => ['required', 'string', 'max:30'],
            'price' => ['nullable', 'numeric'],
            'details' => ['nullable', 'string', 'max:255'],
            'pricingNature' => ['nullable', 'string', 'max:255'],
            'image' => ['required'],
            'video' => ['nullable'],
            'images' => ['required'],
        ],

        'boost' => [
            'duration' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
            'creationDate' => ['date'],
            'idModerateur' => ['integer', 'exists:moderateur,id_mo'],
            'image' => ['required'],
        ],

        'contact' => [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string'],
        ],

        'favorite' => [
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
            'idClient' => ['required', 'integer', 'exists:client,id_c'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
        ],

        'reservation' => [
            'reservationDate' => ['required', 'date'],
            'finalreservationDate' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'type' => ['required', 'string', 'max:255'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
        ],

        'member' => [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'email' => ['required', 'email', 'unique:membre,email_m'],
            'wilaya' => ['required', 'string', 'min:3', 'max:100'],
            'location' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'string', 'min:6', 'max:14', 'unique:membre,tel_m'],
            'mobail' => ['required', 'string', 'min:6', 'max:14', 'unique:membre,mobil_m'],
            'password' => ['required', 'string', 'min:8'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idAdmin' => ['integer', 'exists:admin,id_a'],
            'banned' => ['string', 'in:non,oui'],
            'idModerateur' => ['integer', 'exists:moderateur,id_mo'],
            'image' => ['required'],
        ],

        'updateclient' => [
            'name' => ['string', 'min:4', 'max:100'],
            'wilaya' => ['string'],
            'age' => ['integer', 'min:1', 'max:3'],
            'email' => ['email', 'unique:client,email_c'],
            'phone' => ['string', 'min:6', 'max:14', 'unique:client,tel_c'],
            'password' => ['string', 'min:8'],
            'banned' => ['string', 'in:non,oui'],
        ],
        'updatemember' => [
            'name' => ['string', 'min:4', 'max:100'],
            'email' => ['email', 'unique:membre,email_m'],
            'wilaya' => ['string', 'min:3', 'max:100'],
            'location' => ['string', 'min:3', 'max:255'],
            'phone' => ['string', 'min:6', 'max:14', 'unique:membre,tel_m'],
            'mobail' => ['string', 'min:6', 'max:14', 'unique:membre,mobil_m'],
            'password' => ['string', 'min:8'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idAdmin' => ['integer', 'exists:admin,id_a'],
            'banned' => ['string', 'in:non,oui'],
            'idModerateur' => ['integer', 'exists:moderateur,id_mo'],
        ],
        'updatecontact' => [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255'],
            'message' => ['string', 'max:255'],
            'phone' => ['string', 'max:30'],
            'subject' => ['string', 'max:255'],
            'genre' => ['string', 'in:male,female'],
            'idMember' => ['integer', 'exists:membre,id_m'],
            'idClient' => ['integer', 'exists:client,id_c'],
            'idModerateur' => ['integer', 'exists:moderateur,id_mo'],
        ],
        'updateannonce' => [
            'name' => ['string', 'max:255', 'unique:annonce,nom_an'],
            'category' => ['string', 'max:255'],
            'eventType' => ['string', 'max:255'],
            'city' => ['string', 'max:255'],
            'address' => ['string', 'max:255'],
            'creationDate' => ['date'],
            'phone' => ['string', 'max:30'],
            'mobile' => ['string', 'max:30'],
            'price' => ['numeric'],
            'details' => ['string', 'max:255'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idAdmin' => ['integer', 'exists:admin,id_a'],
            'idModerateur' => ['integer', 'exists:moderateur,id_mo'],
            'idMember' => ['integer', 'exists:membre,id_m'],
            'pricingNature' => ['string', 'max:255'],
            'visits' => ['integer'],
            'likes' => ['integer'],
        ],
        'updateboost' => [
            'duration' => ['integer'],
            'price' => ['numeric'],
            'state' => ['string', 'in:attente,active,inactive'],
            'idMember' => ['integer', 'exists:membre,id_m'],
            'idAnnonce' => ['integer', 'exists:annonce,id_an'],
            'creationDate' => ['date'],
            'idModerateur' => ['integer', 'exists:moderateur,id_mo'],
        ],
        'updatefavorite' => [
            'idAnnonce' => ['integer', 'exists:annonce,id_an'],
            'idClient' => ['integer', 'exists:client,id_c'],
            'idMember' => ['integer', 'exists:membre,id_m'],
        ],
        'updatereservation' => [
            'reservationDate' => ['date'],
            'finalreservationDate' => ['date', 'after_or_equal:date_r_debut'],
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255'],
            'phone' => ['string', 'max:30'],
            'type' => ['string', 'max:255'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idClient' => ['integer', 'exists:client,id_c'],
            'idMember' => ['integer', 'exists:membre,id_m'],
            'idAnnonce' => ['integer', 'exists:annonce,id_an'],
        ],
        'images' => [
            'image' => ['required'],
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
        ],
        'updateimages' => [
            'image' => ['required'],
            'idAnnonce' => ['integer', 'exists:annonce,id_an'],
        ]
    ];
    protected $data = [];

    public function validateData($data, $table)
    {
        //
        if (!isset($this->validationRules[$table])) {
            return $data;
        }
        //
        $rules = $this->validationRules[$table];
        $errors = [];
        $this->data = $data;
        //
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                // 
                if ($rule === 'required' && (empty($data[$field]) || !isset($data[$field]))) {
                    $errors[$field][] = "The {$field} field is required";
                    continue;
                }
                //
                if (!isset($data[$field]) && strpos($rule, 'nullable') === false) {
                    continue;
                }
                //
                if (isset($data[$field])) {
                    $value = $data[$field];
                } else {
                    $value = [];
                }
                // 
                if (is_string($rule) && strpos($rule, 'max:') === 0) {
                    $max = (int)substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "The {$field} must not bigger then {$max} characters";
                    }
                }
                //
                if (is_string($rule) && strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters";
                    }
                }
                // 
                if (is_string($rule) && strpos($rule, 'in:') === 0) {
                    $allowedValues = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowedValues)) {
                        $errors[$field][] = "The {$field} must be one of the following: " . implode(', ', $allowedValues);
                    }
                }
                //
                switch ($rule) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "The {$field} must be a valid email";
                        }
                        break;

                    case 'integer':
                        if (!is_numeric($value) || floor($value) != $value) {
                            $errors[$field][] = "The {$field} must be an integer";
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field][] = "The {$field} must be numeric";
                        }
                        break;

                    case 'image':
                        if (!in_array(mime_content_type($value), ['image/jpeg', 'image/png', 'image/gif'])) {
                            $errors[$field][] = "The {$field} must be a valid image file";
                        }
                        break;

                    case 'date':
                        $formats = ["Y-m-d", "Y-m-d H:i:s"];
                        $valid = false;

                        foreach ($formats as $format) {
                            $d = DateTime::createFromFormat($format, $value);
                            if ($d && $d->format($format) === $value) {
                                $valid = true;
                                break;
                            }
                        }

                        if (!$valid) {
                            $errors[$field][] = "The {$field} must match one of the formats: " . implode(", ", $formats);
                        }
                        break;

                    case (strpos($rule, 'unique:') === 0):
                        [$uniqueTable, $uniqueColumn] = explode(',', substr($rule, 7));

                        $model = new Models($uniqueTable, $this->conn);
                        try {
                            $result = $model->find($value, $uniqueColumn);
                            $errors[$field][] = "The {$field} already existe";
                        } catch (Exception) {
                            http_response_code(200);
                        }

                        break;

                    case (strpos($rule, 'exists:') === 0):
                        [$existsTable, $existsColumn] = explode(',', substr($rule, 7));

                        $model = new Models($existsTable, $this->conn);
                        try {
                            $result = $model->find($value, $existsColumn);
                        } catch (Exception) {
                            $errors[$field][] = "The {$field} must exist before";
                        }
                        break;
                }
            }
        }
        //
        if (!empty($errors)) {
            throw new Exception(json_encode($errors));
        }
        //
        return $data;
    }

    public static function ValideImage($image)
    {
        if (isset($image) && isset($image['error'])  && isset($image['tmp_name'])  && isset($image['size'])) {
            if ($image['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $image['tmp_name'];
                $fileSize = $image['size'];
                $fileType = mime_content_type($fileTmpPath);
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                if (in_array($fileType, $allowedMimeTypes)) {
                    if ($fileSize <= 5 * 1024 * 1024) {
                        $status = true;
                    } else {
                        throw new Exception('The image exceeds the maximum allowed size of 5MB.');
                    }
                } else {
                    throw new Exception('Invalid image type. Only JPG, PNG are allowed.');
                }
            } else {
                throw new Exception('File upload error: ' . $image['error']);
            }
        } else {
            throw new Exception('no image found');
        }
    }

    public static function ValideVideo($video)
    {
        if (isset($video) && isset($video['error']) && isset($video['tmp_name']) && isset($video['size'])) {
            if ($video['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $video['tmp_name'];
                $fileSize = $video['size'];
                $fileType = mime_content_type($fileTmpPath);
                $allowedMimeTypes = ['video/mp4', 'video/x-matroska', 'video/avi', 'video/mpeg', 'image/jpeg', 'image/png'];

                if (in_array($fileType, $allowedMimeTypes)) {
                    if ($fileSize <= 20 * 1024 * 1024) {
                        return true;
                    } else {
                        throw new Exception('The video exceeds the maximum allowed size of 20MB ');
                    }
                } else {
                    throw new Exception('Invalid video type. Only MP4, MKV, AVI, and MPEG formats are allowed.');
                }
            } else {
                throw new Exception('File upload error: ' . $video['error']);
            }
        } else {
            throw new Exception('No video file found.');
        }
    }
}
