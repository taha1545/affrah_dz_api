<?php

class Models {
    protected $tablename;
    protected $required_columns;
    protected $hidden_column;
    protected $conn;

    public function __construct($tablename, $required_columns = [],$hidden_column=[]) {
        // get table and column that should be included and colums that should not be included
        $this->tablename = $tablename;
        $this->required_columns = $required_columns;
        $this->hidden_column=$hidden_column;
        // create connection
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
        //select all
        $sql = "SELECT * FROM {$this->tablename}";
        $result = $this->conn->query($sql);
        // fetch 
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            // unset columns that should be hidden
            if(isset($this->hidden_column)){
                foreach ($data as &$row) {
                    foreach ($this->hidden_column as $hidden) {
                        unset($row[$hidden]);
                    }
                }
                // return new data
                return $data;
            }else{
                return $result;
            }
        }
        return [];
    }

    public function find($id, $key) {
        // key => value ..... 
        $sql = "SELECT * FROM {$this->tablename} WHERE {$key} = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result= $result->fetch_assoc();
        foreach ($this->hidden_column as $hidden) {
            unset($result[$hidden]);
        }
        if (isset($result)){
            return $result;
        }else{
            http_response_code(404);
           return ['error'=>' resource not found'];
        }
    }
    

    public function where($conditions = []) {
        //
        $placeholders = [];
        $values = [];
        $types = "";
        //
        foreach ($conditions as $column => $value) {
            $placeholders[] = "$column = ?";
            $values[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }
        //
        $sql = "SELECT * FROM {$this->tablename} WHERE " . implode(" AND ", $placeholders);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();
        //
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $types = "";
        $values = array_values($data);
        //
        foreach ($values as $value) {
            $types .= is_int($value) ? "i" : "s";
        }
        // 
        $sql = "INSERT INTO {$this->tablename} ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        // 
        if (!$stmt) {
            die("SQL Error: " . $this->conn->error);
        }
        // 
        $stmt->bind_param($types, ...$values);
        // errors
        if (!$stmt->execute()) {
            die("Execution Error: " . $stmt->error);
        }
        return true;
    }
    
    public function update($id, $data,$key) {
        $placeholders = [];
        $values = [];
        $types = "";
        //
        foreach ($data as $column => $value) {
            $placeholders[] = "$column = ?";
            $values[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }
        //
        $types .= "i";
        $values[] = $id;
        //
        $sql = "UPDATE {$this->tablename} SET " . implode(", ", $placeholders) . " WHERE {$key} = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
         //
        return $stmt->execute();
    }

    public function delete($id,$key) {
        $sql = "DELETE FROM {$this->tablename} WHERE {$key} = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        //
        return $stmt->execute();
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
