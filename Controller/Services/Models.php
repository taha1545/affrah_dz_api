<?php

class Models {
    protected $tablename;
    protected $hidden_column;
    protected $hidden_columns=[
        'client'=>['photo_c'],
        'admin'=>['photo_a'],
        'membre'=>['photo_m'],
        'moderateur'=>['photo_mo'],
        'boost'=>['recu_b']
    ];
    protected $conn;
    protected $Columns = [
        // CLIENT
        'client' => [
            'id_c','nom_c', 'ville_c', 'age_c', 'email_c', 'tel_c', 'mdp_c', 'etat_c', 'signale', 'id_a', 'id_mo'
        ],
        // ADMIN
        'admin' => [
            'id_a','nom_a', 'email_a', 'tel_a', 'mdp_a'
        ],
        // annonce
        'annonce' => [
            'id_an', 'nom_an', 'categorie_an', 'type_fete', 'ville_an', 'adresse_an', 'date_cr', 'tel_an', 'mobile_an',
            'tarif_an', 'detail_an', 'etat_an', 'id_a', 'id_mo', 'id_m', 'nature_tarif', 'visites', 'jaime','file_path'
        ],
        // boost
        'boost' => [
             'id_b','duree_b', 'tarif_b', 'etat_b', 'id_m', 'id_an', 'date_cr_b', 'id_mo'
        ],
        // conatct
        'contact' => [
            'id', 'nom', 'email', 'msg', 'tel', 'sujet', 'genre', 'id_m', 'id_c', 'id_mo'
        ],
        // favoris
        'favorite' => [
            'id_fav', 'id_c', 'id_m','id_an'
        ],
        // moderateur
        'moderateur' => ['id_mo', 'nom_mo', 'prenom_mo', 'email_mo', 'tel_mo', 'mdp_mo', 'id_a'],

         //resarvation
        'reservation' =>['id_r', 'date_r_debut', 'date_r_fin', 'nom_c_r', 'email_c_r', 'tel_c_r', 'type_fete', 'etat_r', 'date_cr', 'id_an', 'id_m', 'id_c'],
        
        //membre
        'membre' => [
            'id_m', 'nom_m', 'email_m', 'ville_m', 'adresse_m', 'tel_m', 'mobil_m', 'mdp_m', 
            'etat_m', 'id_a', 'signale','id_mo'
        ],
        //images
        'images'=>[
            'id_img','nom_img','taille_img','type_img','chemin_img','date_cr','id_an '
        ]
    ];
    

    public function __construct($tablename) {
         // table and hidden column
        $this->tablename = $tablename;
        $this->hidden_column=$this->hidden_columns[$tablename] ?? [];
        // connection
        try 
        {
            $this->conn = new mysqli("db", "root", "rootpassword", "affrah");
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }

    public function all() {
        // 
        if (isset($this->Columns[$this->tablename])) {
            $columns = implode(', ', $this->Columns[$this->tablename]);
        } else {
            $columns = '*';
        }
        // Select the columns
        $sql = "SELECT $columns FROM {$this->tablename}";
        $result = $this->conn->query($sql);
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }
    

    public function find($id, $key) {
        // 
        if (isset($this->Columns[$this->tablename])) {
            $columns = implode(', ', $this->Columns[$this->tablename]);
        } else {
            $columns = '*';
        }
        // Select the columns
        $sql = "SELECT $columns FROM {$this->tablename} WHERE {$key} = ? LIMIT 1";
        //execute
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result= $result->fetch_assoc();
         //
        if ($result){
            return $result;
        }else{
            throw new Exception('no resource found');
        }
    }
    

    public function where($conditions = [], $options = []) {
        try {
            $columns = implode(', ', $this->Columns[$this->tablename]) ?? '*';
            $placeholders = [];
            $values = [];
            $types = "";
    
            foreach ($conditions as $condition) {
                [$column, $operator, $value] = $condition;
    
                if (is_array($value) && $operator === 'IN') {
                    $inPlaceholders = implode(',', array_fill(0, count($value), '?'));
                    $placeholders[] = "$column IN ($inPlaceholders)";
                    $values = array_merge($values, $value);
                    $types .= str_repeat('s', count($value));
                } else {
                    $placeholders[] = "$column $operator ?";
                    $values[] = $operator === 'LIKE' ? "%{$value}%" : $value;
                    $types .= is_int($value) ? "i" : "s";
                }
            }
    
            $sql = "SELECT $columns FROM {$this->tablename}";
            if ($placeholders) {
                $sql .= " WHERE " . implode(" AND ", $placeholders);
            }
    
            // Ordering
            if (!empty($options['orderBy'])) {
                $direction = strtoupper($options['orderDirection'] ?? 'ASC');
                $sql .= " ORDER BY " . $this->conn->real_escape_string($options['orderBy']) . " $direction";
            }
    
            // Pagination optimization
            if (isset($options['page'], $options['perPage'])) {
                $offset = ((int)$options['page'] - 1) * (int)$options['perPage'];
                $sql .= " LIMIT ? OFFSET ?";
                $values[] = (int)$options['perPage'];
                $values[] = $offset;
                $types .= "ii";
            }
    
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
    
            if ($values) {
                $stmt->bind_param($types, ...$values);
            }
    
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = $result->fetch_all(MYSQLI_ASSOC);

    
            return $data;
    
        } catch (Exception $e) {
            throw new Exception("Error in where query: " . $e->getMessage());
        }
    }
    
    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $types = "";
        $values = array_values($data);
        foreach ($values as $value) {
            $types .= is_int($value) ? "i" : "s";
        }
        $sql = "INSERT INTO {$this->tablename} ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        $stmt->bind_param($types, ...$values);
        //
        if (!$stmt->execute()) {
            throw new Exception("Execution Error: " . $stmt->error);
        }
         //
        return $stmt->affected_rows > 0;
    }
    
    
    public function update($id, $data, $key) {
        $placeholders = [];
        $values = [];
        $types = "";
    
        foreach ($data as $column => $value) {
            $placeholders[] = "$column = ?";
            $values[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }
    
        $types .= "i";  
        $values[] = $id;
    
        $sql = "UPDATE {$this->tablename} SET " . implode(", ", $placeholders) . " WHERE {$key} = ?";
        $stmt = $this->conn->prepare($sql);
    
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
    
        $stmt->bind_param($types, ...$values);
    
        if (!$stmt->execute()) {
            throw new Exception("Execution Error: " . $stmt->error);
        }
    
        return $stmt->affected_rows > 0;
    }
    

    public function delete($id, $key) {
        
        $sql = "DELETE FROM {$this->tablename} WHERE {$key} = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
    
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
    
        if (!$stmt->execute()) {
            throw new Exception("Execution Error: " . $stmt->error);
        }
        if($stmt->affected_rows <= 0){
            THROW NEW Exception('NO DATA FOUND TO DELETE');
        }
    
        return $stmt->affected_rows > 0; 
    }
    

    public function rawQuery($query, $params = [], $types = "") {
        //
        $stmt = $this->conn->prepare($query);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        //
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findImage($id, $key) {
        //
        if (isset($this->hidden_column[$this->tablename])) {
            $columns = $this->hidden_column[$this->tablename];
        } else {
            $columns = '*';
        }
        //
        $sql = "SELECT $columns FROM {$this->tablename} WHERE {$key} = ? ";
        $stmt = $this->conn->prepare($sql);
        //
        $stmt->bind_param("i", $id); 
        //
        $stmt->execute();
        $result = $stmt->get_result();
        //
        $result = $result->fetch_assoc();
        //
        if ($result) {
            return $result;
        } 
    }
    

    public function __destruct() {
        $this->conn->close();
    }
}

// example ....
// $model = new Models('users', ['name', 'email']);
// $users = $model->all();
// $user = $model->find(1);
// $model->create(['name' => 'taha masn', 'email' => 'taha@example.com']);
// $model->update(1, ['name' => 'taha mans']);
// $model->delete(1);
