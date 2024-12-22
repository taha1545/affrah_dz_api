<?php
//
class Validator {
    //
    protected $validationRules = [
        'client' => [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'wilaya' => ['required', 'string'],
            'age' => ['required', 'integer', 'min:1', 'max:3'], 
            'email' => ['required', 'email', 'unique:client,email_c'],
            'phone' => ['required', 'string', 'min:6', 'max:14','unique:client,tel_c'],
            'password' => ['required', 'string', 'min:8'],
            'etat' => ['required', 'string', 'in:attente,active,inactive'],
            'banned' => ['required', 'string', 'in:non,oui'],
            'idAdmin' => ['required', 'integer', 'exists:admin,id_a'],
            'idModerateur' => ['required', 'integer', 'exists:moderateur,id_mo'],
        ],
    
        'admin' => [
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'email' => ['required', 'email', 'unique:admin,email_a'],
            'phone' => ['required', 'string', 'min:6', 'max:30', 'unique:admin,tel_a'],
            'password' => ['required', 'string', 'min:8'],
        ],
    
        'annonce' => [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'eventType' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'creationDate' => ['required', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'mobile' => ['required', 'string', 'max:30'],
            'price' => ['nullable', 'numeric'],
            'details' => ['nullable', 'string', 'max:255'],
            'etat' => ['required', 'string', 'in:attente,active,inactive'],
            'idAdmin' => ['nullable', 'integer', 'exists:admin,id_a'],
            'idModerateur' => ['nullable', 'integer', 'exists:moderateur,id_mo'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
            'pricingNature' => ['nullable', 'string', 'max:255'],
            'visits' => ['nullable', 'integer'],
            'likes' => ['nullable', 'integer'],
        ],
    
        'boost' => [
            'duration' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'state' => ['required', 'string', 'in:attente,active,inactive'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
            'creationDate' => ['required', 'date'],
            'idModerateur' => ['required', 'integer', 'exists:moderateur,id_mo'],
        ],
    
        'contact' => [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'in:male,female'],
            'idMember' => ['nullable', 'integer', 'exists:membre,id_m'],
            'idClient' => ['nullable', 'integer', 'exists:client,id_c'],
            'idModerateur' => ['nullable', 'integer', 'exists:moderateur,id_mo'],
        ],
    
        'favorite' => [
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
            'idClient' => ['required', 'integer', 'exists:client,id_c'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
        ],
    
      'moderateur' => [
        'name' => ['required', 'string', 'min:4', 'max:255'],
        'familyname' => ['required', 'string', 'min:4', 'max:255'],
        'email' => ['required', 'email', 'unique:moderateur,email_mo'],
        'phone' => ['required', 'string', 'min:6', 'max:15','unique:moderateur,tel_mo'],
         'password' => ['required', 'string', 'min:8'],
        'idAdmin' => ['required', 'integer', 'exists:admin,id_a'],
         ],
    
        'reservation' => [
            'reservationDate' => ['required', 'date'],
            'numberOfGuests' => ['required', 'integer'],
            'state' => ['required', 'string', 'in:attente,active,inactive'],
            'idClient' => ['required', 'integer', 'exists:client,id_c'],
            'idMember' => ['required', 'integer', 'exists:membre,id_m'],
            'idAnnonce' => ['required', 'integer', 'exists:annonce,id_an'],
        ],
    
        'member' => [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'email' => ['required', 'email', 'unique:membre,email_m'],
            'wilaya' => ['required', 'string', 'min:3', 'max:100'], 
            'location' => ['required', 'string', 'min:3', 'max:255'], 
            'phone' => ['required', 'string', 'min:6', 'max:14','unique:membre,tel_m'], 
            'mobail' => ['required', 'string', 'min:6', 'max:14','unique:membre,mobil_m'],
            'password' => ['required', 'string', 'min:8'],
            'etat' => ['required', 'string', 'in:attente,active,inactive'],
            'idAdmin' => ['required', 'integer', 'exists:admin,id_a'],
            'banned' => ['required', 'string', 'in:non,oui'], 
            'idModerateur' => ['required', 'integer', 'exists:moderateur,id_mo'], 
        ],

        'updateclient' => [
            'name' => [ 'string', 'min:4', 'max:100'],
            'wilaya' => [ 'string'],
            'age' => ['integer', 'min:1', 'max:3'], 
            'email' => [ 'email', 'unique:client,email_c'],
            'phone' => ['string', 'min:6', 'max:14','unique:client,tel_c'],
            'password' => [ 'string', 'min:8'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'banned' => [ 'string', 'in:non,oui'],
            'idAdmin' => [ 'integer', 'exists:admin,id_a'],
            'idModerateur' => [ 'integer', 'exists:moderateur,id_mo'],
        ],
    
        'updateadmin' => [
            'name' => [ 'string', 'min:4', 'max:255'],
            'email' => [ 'email', 'unique:admin,email_a'],
            'phone' => [ 'string', 'min:6', 'max:30', 'unique:admin,tel_a'],
            'password' => [ 'string', 'min:8'],
        ],

        'updatemoderator' => [
        'name' => [ 'string', 'min:4', 'max:255'],
        'familyname' => ['string', 'min:4', 'max:255'],
        'email' => [ 'email', 'unique:moderateur,email_mo'],
        'phone' => ['required', 'string', 'min:6', 'max:15','unique:moderateur,tel_mo'],
        'password' => [ 'string', 'min:8'],
        'idAdmin' => ['integer', 'exists:admin,id_a'],
        ],
    
         'updatemember' => [
            'name' => ['string', 'min:4', 'max:100'],
            'email' => ['email', 'unique:membre,email_m'],
            'wilaya' => ['string', 'min:3', 'max:100'], 
            'location' => [ 'string', 'min:3', 'max:255'], 
            'phone' => [ 'string', 'min:6', 'max:14','unique:membre,tel_m'], 
            'mobail' => ['string', 'min:6', 'max:14','unique:membre,mobil_m'],
            'password' => [ 'string', 'min:8'],
            'etat' => ['string', 'in:attente,active,inactive'],
            'idAdmin' => [ 'integer', 'exists:admin,id_a'],
            'banned' => [ 'string', 'in:non,oui'], 
            'idModerateur' => [ 'integer', 'exists:moderateur,id_mo'], 
        ],
    ];
    
    //
    protected $data = [];
     //
    public function validateData($data, $table) {
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
                if (isset($data[$field])){
                    $value = $data[$field];
                }else{
                    $value=[];
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
                          $model= new Models($uniqueTable);
                          try{
                              $result=$model->find($value,$uniqueColumn); 
                              $errors[$field][] = "The {$field} must be unique in {$uniqueTable}.";
                          }catch(Exception){
                            http_response_code(200);
                          }
                      
                        break;
    
                      case (strpos($rule, 'exists:') === 0):
                        [$existsTable, $existsColumn] = explode(',', substr($rule, 7));
                
                        $model = new Models($existsTable);
                        try{
                        $result = $model->find($value, $existsColumn);
                        }catch(Exception){
                            $errors[$field][] = "The {$field} must exist in {$existsTable}.";
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

    public static function ValideImage($image) {
        if (isset($image)) {
            if ($image['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $image['tmp_name'];
                $fileSize = $image['size'];
                $fileType = mime_content_type($fileTmpPath);
                $allowedMimeTypes = ['image/jpeg', 'image/png'];
    
                if (in_array($fileType, $allowedMimeTypes)) {
                    if ($fileSize <= 3 * 1024 * 1024) {
                       return $fileTmpPath;
                    } else {
                        throw new Exception('The image exceeds the maximum allowed size of 3MB.');
                    }
                } else {
                    throw new Exception('Invalid image type. Only JPG, PNG are allowed.');
                }
            } else {
                throw new Exception('File upload error: ' . $image['error']);
            }
        }
    }
    
}
