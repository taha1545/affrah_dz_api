<?php
//
class Validator {
    //
    protected $validationRules = [
        //
  'client' => [
    'nom_c' => ['required', 'string', 'min:4', 'max:100'],
    'ville_c' => ['required', 'string'],
    'age_c' => ['required', 'integer', 'min:1', 'max:2'],
    'email_c' => ['required', 'email', 'unique:client,email_c'],
    'tel_c' => ['required', 'string', 'min:6', 'max:14'],
    'mdp_c' => ['required', 'string', 'min:8'],
    'etat_c' => ['required', 'string', 'in:attente,active,inactive'],
    'signale' => ['required', 'string', 'in:non,oui'],
    'id_a' => ['required', 'integer', 'exists:admin,id_a'],
    'id_mo' => ['required', 'integer', 'exists:moderateur,id_mo'],
  ],
  //
  'admin' => [
    'nom_a' => ['required', 'string', 'min:4', 'max:255'],
    'email_a' => ['required', 'email', 'unique:admin,email_a'],
    'tel_c' => ['required', 'string', 'min:6', 'max:30'],
    'mdp_a' => ['required', 'string', 'min:8'],
  ],
   //
  'annonce' => [
    'nom_an' => ['required', 'string', 'max:255'],
    'categorie_an' => ['required', 'string', 'max:255'],
    'type_fete' => ['required', 'string', 'max:255'],
    'ville_an' => ['required', 'string', 'max:255'],
    'adresse_an' => ['required', 'string', 'max:255'],
    'date_cr' => ['required', 'date'],
    'tel_an' => ['nullable', 'string', 'max:30'],
    'mobile_an' => ['required', 'string', 'max:30'],
    'tarif_an' => ['nullable', 'numeric'],
    'detail_an' => ['nullable', 'string', 'max:255'],
    'etat_an' => ['required', 'string', 'in:attente,active,inactive'],
    'id_a' => ['nullable', 'integer', 'exists:admin,id_a'],
    'id_mo' => ['nullable', 'integer', 'exists:moderateur,id_mo'],
    'id_m' => ['required', 'integer', 'exists:membre,id_m'],
    'nature_tarif' => ['nullable', 'string', 'max:255'],
    'visites' => ['nullable', 'integer'],
    'jaime' => ['nullable', 'integer'],
   ],
    //
   'boost' => [
    'duree_b' => ['required', 'integer'],
    'tarif_b' => ['required', 'numeric'],
    'etat_b' => ['required', 'string', 'in:attente,active,inactive'],
    'id_m' => ['required', 'integer', 'exists:membre,id_m'],
    'id_an' => ['required', 'integer', 'exists:annonce,id_an'],
    'date_cr_b' => ['required', 'date'],
    'id_mo' => ['required', 'integer', 'exists:moderateur,id_mo'],
   ],
    //
   'contact' => [
    'nom' => ['nullable', 'string', 'max:255'],
    'email' => ['nullable', 'email', 'max:255'],
    'msg' => ['required', 'string', 'max:255'],
    'tel' => ['nullable', 'string', 'max:30'],
    'sujet' => ['required', 'string', 'max:255'],
    'genre' => ['required', 'string', 'max:30'],
    'id_m' => ['required', 'integer', 'exists:membre,id_m'],
    'id_c' => ['required', 'integer', 'exists:client,id_c'],
    'id_mo' => ['nullable', 'integer', 'exists:moderateur,id_mo'],
   ],

 'favorite' => [
    'id_an' => ['required', 'integer', 'exists:annonce,id_an'],
    'id_c' => ['nullable', 'integer', 'exists:client,id_c'],
    'id_m' => ['nullable', 'integer', 'exists:membre,id_m'],
 ],

 'membre' => [
    'nom_m' => ['required', 'string', 'max:255'],
    'email_m' => ['required', 'email', 'unique:membre,email_m'],
    'ville_m' => ['required', 'string', 'max:255'],
    'adresse_m' => ['nullable', 'string', 'max:255'],
    'tel_m' => ['nullable', 'string', 'max:30'],
    'mobil_m' => ['required', 'string', 'max:30'],
    'mdp_m' => ['required', 'string', 'min:8'],
    'etat_m' => ['required', 'string', 'in:attente,active,inactive'],
    'signale' => ['required', 'string', 'in:non,oui'],
    'id_a' => ['required', 'integer', 'exists:admin,id_a'],
    'id_mo' => ['nullable', 'integer', 'exists:moderateur,id_mo'],
 ],

 'moderateur' => [
    'nom_mo' => ['required', 'string', 'max:255'],
    'prenom_mo' => ['required', 'string', 'max:255'],
    'email_mo' => ['required', 'email', 'unique:moderateur,email_mo'],
    'tel_mo' => ['required', 'string', 'max:30'],
    'mdp_mo' => ['required', 'string', 'min:8'],
    'id_a' => ['required', 'integer', 'exists:admin,id_a'],
  ],

  'reservation' => [
    'date_r_debut' => ['required', 'date'],
    'date_r_fin' => ['required', 'date'],
    'nom_c_r' => ['required', 'string', 'max:255'],
    'email_c_r' => ['required', 'email'],
    'tel_c_r' => ['required', 'string', 'max:30'],
    'type_fete' => ['required', 'string', 'max:255'],
    'etat_r' => ['required', 'string', 'in:attente,active,inactive'],
    'date_cr' => ['required', 'date'],
    'id_an' => ['required', 'integer', 'exists:annonce,id_an'],
    'id_m' => ['required', 'integer', 'exists:membre,id_m'],
    'id_c' => ['nullable', 'integer', 'exists:client,id_c'],
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
                              $result=$model->find($value,$uniqueColumn); 
                              if (!isset($result['error'])){
                                $errors[$field][] = "The {$field} must be unique in {$uniqueTable}.";
                              }else{
                                http_response_code(200);
                              }
                      
                        break;
    
                    case (strpos($rule, 'exists:') === 0):
                        [$existsTable, $existsColumn] = explode(',', substr($rule, 7));
                
                        $model = new Models($existsTable);
                        $result = $model->find($value, $existsColumn);
                
                        if (isset($result['error'])) { 
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
}
