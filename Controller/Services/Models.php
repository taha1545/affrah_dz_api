<?php
class Models
{
    protected $tablename;
    protected $hidden_column;
    protected $hidden_columns = [
        'client' => ['photo_c'],
        'admin' => ['photo_a'],
        'membre' => ['photo_m'],
        'moderateur' => ['photo_mo'],
        'boost' => ['recu_b']
    ];

    protected $conn;

    protected $Columns = [
        // CLIENT
        'client' => [
            'id_c',
            'nom_c',
            'ville_c',
            'age_c',
            'email_c',
            'tel_c',
            'mdp_c',
            'etat_c',
            'signale',
            'id_a',
            'id_mo'
        ],
        // ADMIN
        'admin' => [
            'id_a',
            'nom_a',
            'email_a',
            'tel_a',
            'mdp_a'
        ],
        // annonce
        'annonce' => [
            'id_an',
            'nom_an',
            'categorie_an',
            'type_fete',
            'ville_an',
            'adresse_an',
            'date_cr',
            'tel_an',
            'mobile_an',
            'tarif_an',
            'detail_an',
            'etat_an',
            'id_a',
            'id_mo',
            'id_m',
            'nature_tarif',
            'visites',
            'jaime',
            'file_path',
            'file_path_video',
            'file_name',
            'file_name_video'
        ],
        // boost
        'boost' => [
            'id_b',
            'duree_b',
            'tarif_b',
            'etat_b',
            'id_m',
            'id_an',
            'date_cr_b',
            'id_mo',
            'type_b'
        ],
        // conatct
        'contact' => [
            'id',
            'nom',
            'email',
            'msg',
            'tel',
            'sujet',
            'genre',
            'id_m',
            'id_c',
            'id_mo'
        ],
        // favoris
        'favorite' => [
            'id_fav',
            'id_c',
            'id_m',
            'id_an'
        ],
        // moderateur
        'moderateur' => ['id_mo', 'nom_mo', 'prenom_mo', 'email_mo', 'tel_mo', 'mdp_mo', 'id_a'],

        //resarvation
        'reservation' => ['id_r', 'date_r_debut', 'date_r_fin', 'nom_c_r', 'email_c_r', 'tel_c_r', 'type_fete', 'etat_r', 'date_cr', 'id_an', 'id_m', 'id_c'],

        //membre
        'membre' => [
            'id_m',
            'nom_m',
            'email_m',
            'ville_m',
            'adresse_m',
            'tel_m',
            'mobil_m',
            'mdp_m',
            'etat_m',
            'id_a',
            'signale',
            'id_mo'
        ],
        //images
        'images' => [
            'id_img',
            'nom_img',
            'taille_img',
            'type_img',
            'chemin_img',
            'date_cr',
            'id_an '
        ]
    ];

    public function __construct($tablename, $conn)
    {
        $this->tablename = $tablename;
        $this->conn = $conn;
    }


    public function all()
    {
        // 
        if (isset($this->Columns[$this->tablename])) {
            $columns = implode(', ', $this->Columns[$this->tablename]);
        } else {
            $columns = '*';
        }
        // Select the columns
        try {
            $sql = "SELECT $columns FROM {$this->tablename}";
            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }

    public function find($id, $key)
    {
        // Determine columns to select
        $columns = isset($this->Columns[$this->tablename])
            ? implode(', ', $this->Columns[$this->tablename])
            : '*';
        try {
            // Prepare SQL query
            $sql = "SELECT $columns FROM {$this->tablename} WHERE {$key} = ?";
            $stmt = $this->conn->prepare($sql);
            // Bind parameters and execute
            $stmt->bind_param("s", $id);
            $stmt->execute();
            // Fetch the result
            $result = $stmt->get_result()->fetch_assoc();
            if (!$result) {
                http_response_code(404);
                throw new Exception('No resource found');
            }
            return $result;
        } catch (Exception $e) {
            // Log or handle error details (e.g., $e->getMessage())
            http_response_code(500);
            throw new Exception("Error fetching data");
        }
    }


    public function create(array $data): int
    {
        if (empty($data)) {
            http_response_code(400);
            throw new InvalidArgumentException("The data array provided is empty.");
        }
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $values = array_values($data);
        $types = array_reduce($values, function ($carry, $value) {
            return $carry . (is_int($value) ? "i" : "s");
        }, "");
        $sql = "INSERT INTO {$this->tablename} ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            throw new Exception("Failed to prepare the SQL statement.");
        }
        if (!$stmt->bind_param($types, ...$values)) {
            http_response_code(500);
            throw new Exception("Failed to bind parameters to the SQL statement.");
        }
        if (!$stmt->execute()) {
            http_response_code(500);
            throw new Exception("Failed to execute the SQL statement.");
        }
        $id = $this->conn->insert_id;
        $stmt->close();
        return $id;
    }

    public function update($id, $data, $key)
    {
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
            http_response_code(500);
            throw new Exception("Failed to prepare the SQL statement for update.");
        }

        $stmt->bind_param($types, ...$values);

        if (!$stmt->execute()) {
            http_response_code(500);
            throw new Exception("Failed to execute the update statement.");
        }
        if ($stmt->affected_rows === 0) {
            http_response_code(404);
            throw new Exception("No matching record found to update.");
        }

        return $stmt->affected_rows > 0;
    }

    public function delete($id, $key)
    {
        $sql = "DELETE FROM {$this->tablename} WHERE {$key} = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if (!$stmt) {
            http_response_code(500);
            throw new Exception("Failed to prepare the SQL statement for deletion.");
        }

        if (!$stmt->execute()) {
            http_response_code(500);
            throw new Exception("Failed to execute the delete statement.");
        }
        if ($stmt->affected_rows <= 0) {
            http_response_code(404);
            throw new Exception("No matching record found to delete.");
        }

        return $stmt->affected_rows > 0;
    }

    public function rawQuery($query, $params = [], $types = "")
    {
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

    public function findImage($id, $key)
    {
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

    //not yet
    public function Search($key = [])
    {
        if (isset($this->hidden_column[$this->tablename])) {
            $columns = $this->hidden_column[$this->tablename];
        } else {
            $columns = '*';
        }
        $sql = "SELECT $columns FROM {$this->tablename}";
        $result = $this->conn->query($sql);

        //search 
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $data = [];
        }
        if (!$data) {
            throw new Exception('no data found to search');
        } else {
        }
    }


    public function where($conditions = [], $options = [])
    {
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
                http_response_code(500);
                throw new Exception("NO DATA FOUND");
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

    public function updatepass($key, $keyname, $data)
    {
        // Ensure input data is not empty
        if (empty($key) || empty($keyname) || empty($data)) {
            throw new Exception("Invalid input parameters for updating record");
        }

        // Build placeholders and types for prepared statement
        $placeholders = [];
        $values = [];
        $types = "";

        foreach ($data as $column => $value) {
            $placeholders[] = "$column = ?";
            $values[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }

        // Append the key for the WHERE clause
        $types .= "s";
        $values[] = $key;

        // Prepare the SQL statement
        $sql = "UPDATE {$this->tablename} SET " . implode(", ", $placeholders) . " WHERE {$keyname} = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        // Bind the parameters dynamically
        $stmt->bind_param($types, ...$values);

        // Execute the query and handle errors
        if (!$stmt->execute()) {
            throw new Exception("Execution Error: " . $stmt->error);
        }

        // Return true if the update was successful
        return $this->conn->insert_id;
    }


    public function findall($id, $key)
    {
        //
        $columns = isset($this->Columns[$this->tablename])
            ? implode(', ', $this->Columns[$this->tablename])
            : '*';
        // Prepare SQL query
        $sql = "SELECT $columns FROM {$this->tablename} WHERE {$key} = ?";
        $stmt = $this->conn->prepare($sql);
        // Bind parameters and execute
        $stmt->bind_param("s", $id);
        $stmt->execute();
        // Fetch the result
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
        //
        if (empty($data)) {
            throw new Exception('No resources found');
        }
        return $data;
    }

    public function allvip()
    {
        //
        $columns = implode(', ', $this->Columns['annonce']);
        // Select the columns
        try {
            $sql = "SELECT annonce.*, boost.type_b 
            FROM annonce 
            LEFT JOIN boost 
            ON annonce.id_an = boost.id_an WHERE boost.type_b = 'vip';";
            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }

    public function allboost()
    {
        //
        $columns = implode(', ', $this->Columns['annonce']);
        // Select the columns
        try {
            $sql = "SELECT annonce.*, boost.type_b 
            FROM annonce 
            LEFT JOIN boost 
            ON annonce.id_an = boost.id_an WHERE boost.type_b = 'gold';";
            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }

    public function allcategorie()
    {
        // Select the columns
        try {
            $sql = "SELECT categorie_an, COUNT(categorie_an) AS count
            FROM annonce
            GROUP BY categorie_an;";
            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }

    public function allannonce()
    {
        // 
        $columns = implode(', ', $this->Columns['annonce']);
        // Select the columns
        try {
            $sql = "SELECT annonce.*, boost.type_b 
            FROM annonce 
            LEFT JOIN boost 
            ON annonce.id_an = boost.id_an;";
            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }

    public function whereannonce($conditions = [], $options = [])
    {
        try {
            $columns = implode(', ', $this->Columns['annonce']);
            $placeholders = [];
            $values = [];
            $types = "";
            //
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
            //
            $sql = "SELECT annonce.*, boost.type_b FROM annonce LEFT JOIN boost   ON annonce.id_an = boost.id_an ";
            //
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

    public function bulkcreate(array $data)
    {
        // Ensure data is not empty
        if (empty($data)) {
            throw new InvalidArgumentException("Data array cannot be empty.");
        }
        // Extract columns from the first row
        $columns = implode(", ", array_keys($data[0]));
        // Create placeholders and values array
        $placeholders = [];
        $values = [];
        $types = '';
        foreach ($data as $row) {
            // Create placeholders for each row
            $placeholders[] = "(" . implode(", ", array_fill(0, count($row), "?")) . ")";

            // Collect values and determine parameter types
            foreach ($row as $value) {
                $values[] = $value;
                $types .= is_int($value) ? "i" : "s"; // 'i' for integer, 's' for string
            }
        }
        // Prepare SQL statement
        $placeholders = implode(", ", $placeholders);
        $sql = "INSERT INTO {$this->tablename} ($columns) VALUES $placeholders";
        // Prepare and execute the statement
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("SQL Preparation Error: " . $this->conn->error);
        }
        // Bind parameters dynamically
        if (!$stmt->bind_param($types, ...$values)) {
            throw new Exception("Parameter Binding Error: " . $stmt->error);
        }
        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execution Error: " . $stmt->error);
        }
        //
        $stmt->close();
    }

    public function allannoncefavoris($id, $key)
    {
        // Select the columns
        try {
            $sql = "SELECT a.*, b.type_b
                  FROM annonce a
                  JOIN favoris f ON a.id_an = f.id_an
                  LEFT JOIN boost b ON a.id_an = b.id_an
                   WHERE f.$key = $id;";

            $result = $this->conn->query($sql);
        } catch (Exception $e) {
            throw new Exception("error fetching");
        }
        // Fetch and return the data
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return [];
    }

    public function Searchannonce($word)
    {
        if (isset($this->hidden_column[$this->tablename])) {
            $columns = $this->hidden_column[$this->tablename];
        } else {
            $columns = '*';
        }
        $searchableColumns = ['nom_an', 'categorie_an', 'type_fete', 'ville_an', 'adresse_an', 'tel_an', 'detail_an'];
        // 
        $whereClause = [];
        foreach ($searchableColumns as $column) {
            $whereClause[] = "$column LIKE ?";
        }
        $whereClause = implode(' OR ', $whereClause);
        //
        $sql = "SELECT annonce.*, boost.type_b 
                FROM annonce 
                LEFT JOIN boost 
                ON annonce.id_an = boost.id_an 
                WHERE $whereClause";
        $stmt = $this->conn->prepare($sql);
        //
        if (!$stmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }
        // Bind 
        $searchWord = "%$word%";
        $types = str_repeat('s', count($searchableColumns));
        $stmt->bind_param($types, ...array_fill(0, count($searchableColumns), $searchWord));
        // Execute the query
        if (!$stmt->execute()) {
            throw new Exception("Execution Error: " . $stmt->error);
        }
        // Fetch the results
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        // Check if data was found
        if (empty($data)) {
            throw new Exception('No data found for the search term: ' . $word);
        }
        return $data;
    }

    public function whereVip($conditions = [], $options = [])
    {
        try {
            $columns = implode(', ', $this->Columns['annonce']);
            $placeholders = [];
            $values = [];
            $types = "";
            //
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
            //
            $sql = "SELECT annonce.*, boost.type_b FROM annonce LEFT JOIN boost ON annonce.id_an = boost.id_an";

            if ($placeholders) {
                $sql .= " WHERE " . implode(" AND ", $placeholders) . " AND boost.type_b = 'vip'";
            } else {
                $sql .= " WHERE boost.type_b = 'vip'";
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

    public function whereGold($conditions = [], $options = [])
    {
        try {
            $columns = implode(', ', $this->Columns['annonce']);
            $placeholders = [];
            $values = [];
            $types = "";
            //
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
            //
            $sql = "SELECT annonce.*, boost.type_b FROM annonce LEFT JOIN boost ON annonce.id_an = boost.id_an";

            if ($placeholders) {
                $sql .= " WHERE " . implode(" AND ", $placeholders) . " AND boost.type_b = 'gold'";
            } else {
                $sql .= " WHERE boost.type_b = 'gold'";
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

    public function ReservationsByDateClient($startDate, $endDate, $id)
    {
        try {
            // Get the columns for 'reservation' and 'membre' from the Columns array
            $reservationColumns = ['id_r ', 'date_r_debut', 'date_r_fin', 'etat_r', 'date_cr'];
            $membre = ['id_m', 'nom_m', 'ville_m'];
            $annoncecolumn = ['id_an', 'nom_an', 'ville_an', 'adresse_an', 'file_path', 'file_name', 'tarif_an'];
            // Construct the SELECT clause for 'reservation' dynamically
            $selectReservationColumns = implode(', ', array_map(function ($column) {
                return 'r.' . $column;
            }, $reservationColumns));
            // Construct the SELECT clause for 'membre' dynamically
            $membres = implode(', ', array_map(function ($column) {
                return 'm.' . $column;
            }, $membre));
            //
            $annoncecolumns = implode(', ', array_map(function ($column) {
                return 'a.' . $column;
            }, $annoncecolumn));
            // Construct the SQL query
            $sql = "SELECT $selectReservationColumns, $membres, $annoncecolumns
                        FROM reservation r
                        JOIN annonce a ON r.id_an = a.id_an
                        JOIN membre m ON r.id_m = m.id_m
                        WHERE r.date_r_debut BETWEEN ? AND ?
                        AND r.id_c = ? ORDER BY r.date_r_debut DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $startDate, $endDate, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            //
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching reservations: " . $e->getMessage());
        }
    }

    public function ReservationsByDateMembre($startDate, $endDate, $id)
    {
        try {
            // Get the columns for 'reservation' and 'membre' from the Columns array
            $reservationColumns = ['id_r ', 'date_r_debut', 'date_r_fin', 'etat_r', 'date_cr'];
            $client = ['id_c', 'nom_c', 'ville_c'];
            $annoncecolumn = ['id_an', 'nom_an', 'ville_an', 'adresse_an', 'file_path', 'file_name', 'tarif_an'];
            // Construct the SELECT clause for 'reservation' dynamically
            $selectReservationColumns = implode(', ', array_map(function ($column) {
                return 'r.' . $column;
            }, $reservationColumns));
            // Construct the SELECT clause for 'membre' dynamically
            $clients = implode(', ', array_map(function ($column) {
                return 'c.' . $column;
            }, $client));
            //
            $annoncecolumns = implode(', ', array_map(function ($column) {
                return 'a.' . $column;
            }, $annoncecolumn));
            // Construct the SQL query
            $sql = "SELECT $selectReservationColumns, $clients, $annoncecolumns
                    FROM reservation r
                    JOIN annonce a ON r.id_an = a.id_an
                    JOIN client c ON r.id_c = c.id_c
                    WHERE r.date_r_debut BETWEEN ? AND ?
                    AND r.id_m = ? ORDER BY r.date_r_debut DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $startDate, $endDate, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            //
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error fetching reservations: " . $e->getMessage());
        }
    }
    
}
