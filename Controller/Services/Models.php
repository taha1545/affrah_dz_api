<?php

// query sql to get data from db

class Models
{
    protected $tablename;


    protected $hidden_column;

    //db photos or  longblob 
    protected $hidden_columns = [
        'client' => ['photo_c'],
        'admin' => ['photo_a'],
        'membre' => ['photo_m'],
        'moderateur' => ['photo_mo'],
        'boost' => ['recu_b']
    ];

    protected $conn;

    // db tables with colums to fetch
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
            'id_mo',
            'fcm_token'
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
            'id_mo',
            'fcm_token',
            'code',
            'code_use'
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
        //
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


        return $stmt->affected_rows > 0;
    }

    public function delete($id, $key)
    {
        $sql = "DELETE FROM {$this->tablename} WHERE {$key} = ?  LIMIT 1";
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

    // 
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
    // 
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

    // 
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

    public function findallannonce($id)
    {
        // Select the columns
        try {
            $sql = "SELECT a.*, lb.type_b
            FROM annonce a
            LEFT JOIN (
            SELECT b.id_an, b.type_b, b.date_cr_b 
            FROM (
            SELECT id_an, type_b, date_cr_b,
               ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
            FROM boost
             ) b
             WHERE b.rn = 1
             ) lb ON  a.id_an = lb.id_an
             WHERE a.id_m = $id
             ORDER BY 
            CASE 
            WHEN lb.type_b = 'gold' THEN 1
            WHEN lb.type_b = 'silver' THEN 2 
            ELSE 3 END,
            lb.date_cr_b DESC,
            a.jaime DESC;";
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

    public function allvip($page = 1, $perPage = 30, $wilaya = null)
    {
        $columns = implode(', ', $this->Columns['annonce']);
        $offset = ($page - 1) * $perPage;

        try {
            // Count total records
            $countSql = "SELECT COUNT(*) as total FROM annonce a 
                         JOIN boost b ON a.id_an = b.id_an
                         WHERE b.type_b = 'gold' AND b.etat_b = 'valide' AND DATE_ADD(b.date_cr_b, INTERVAL b.duree_b DAY) >= NOW()  
                         AND a.etat_an = 'valide'";
            $countResult = $this->conn->query($countSql);
            $totalRows = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $perPage);

            // Add priority sorting by wilaya (ville)
            $wilayaOrder = $wilaya ? "CASE WHEN a.ville_an = '{$this->conn->real_escape_string($wilaya)}' THEN 0 ELSE 1 END," : "";

            $sql = "SELECT a.*, latest_boost.type_b
                    FROM annonce a 
                    JOIN (
                        SELECT 
                            b.*,
                            ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                        FROM boost b
                        WHERE b.type_b = 'gold' AND b.etat_b = 'valide' AND DATE_ADD(b.date_cr_b, INTERVAL b.duree_b DAY) >= NOW() 
                    ) latest_boost ON a.id_an = latest_boost.id_an AND latest_boost.rn = 1
                    WHERE a.etat_an = 'valide'
                    ORDER BY $wilayaOrder a.date_cr DESC, a.jaime DESC
                    LIMIT $perPage OFFSET $offset";

            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }

        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        return [
            'paginate' => [
                'current_page' => (int) $page,
                'per_page' => $perPage,
                'total_rows' => $totalRows,
                'total_pages' => $totalPages,
            ],
            'data' => $data
        ];
    }



    public function allboost($page = 1, $perPage = 30)
    {
        $columns = implode(', ', $this->Columns['annonce']);
        $offset = ($page - 1) * $perPage;
        //
        try {
            // Count total records for pagination
            $countSql = "SELECT COUNT(*) as total FROM annonce a 
                     JOIN boost b ON a.id_an = b.id_an
                     WHERE b.type_b = 'silver' AND b.etat_b = 'valide' AND DATE_ADD(b.date_cr_b, INTERVAL b.duree_b DAY) >= NOW() AND a.etat_an = 'valide'";
            //         
            $countResult = $this->conn->query($countSql);
            $totalRows = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $perPage);
            // Fetch paginated results
            $sql = "SELECT a.*, latest_boost.type_b
                FROM annonce a 
                JOIN (
                    SELECT 
                        b.*,
                        ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                    FROM boost b
                    WHERE b.type_b = 'silver' AND b.etat_b = 'valide' AND DATE_ADD(b.date_cr_b, INTERVAL b.duree_b DAY) >= NOW() 
                ) latest_boost ON a.id_an = latest_boost.id_an AND latest_boost.rn = 1 
                WHERE a.etat_an = 'valide'
                ORDER BY a.date_cr DESC, a.jaime DESC
                LIMIT $perPage OFFSET $offset";
            //
            $result = $this->conn->query($sql);
        } catch (Exception) {
            throw new Exception("error fetching");
        }
        // Fetch data
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        // Add pagination metadata to $result
        return [
            'paginate' => [
                'current_page' => (int) $page,
                'per_page' => $perPage,
                'total_rows' => $totalRows,
                'total_pages' => $totalPages,
            ],
            'data' => $data
        ];
    }

    public function allcategorie()
    {
        // Select the columns
        try {
            $sql = "SELECT categorie_an, COUNT(categorie_an) AS count
            FROM annonce
            GROUP BY categorie_an
             ORDER BY count DESC ;";
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

    public function allannonce($page = 1, $perPage = 30)
    {
        $offset = ($page - 1) * $perPage;
        try {
            // Count total records for pagination
            $countSql = "SELECT COUNT(*) as total FROM annonce a 
                         LEFT JOIN boost b ON a.id_an = b.id_an
                         WHERE a.etat_an = 'valide'";
            $countResult = $this->conn->query($countSql);
            $totalRows = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $perPage);

            // Fetch paginated results with proper ordering
            $sql = "SELECT a.*, lb.type_b
                    FROM annonce a
                    LEFT JOIN (
                        SELECT id_an, type_b, date_cr_b
                        FROM (
                            SELECT id_an, type_b, date_cr_b,
                                   ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                            FROM boost
                        ) b
                        WHERE b.rn = 1
                    ) lb ON a.id_an = lb.id_an
                    WHERE a.etat_an = 'valide'
                    ORDER BY 
                        CASE 
                            WHEN lb.type_b = 'gold' THEN 1
                            WHEN lb.type_b = 'silver' THEN 2 
                            ELSE 3 
                        END,
                        lb.date_cr_b DESC,
                        a.jaime DESC
                    LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $perPage, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            return  $data;
        } catch (Exception $e) {
            throw new Exception("Error fetching data: " . $e->getMessage());
        }
    }


    public function whereannonce($conditions = [], $options = ['page' => 1, 'perPage' => 20])
    {
        try {
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

            // Base SQL query
            $sql = "SELECT a.*, lb.type_b
                    FROM annonce a
                    LEFT JOIN (
                        SELECT id_an, type_b, date_cr_b
                        FROM (
                            SELECT id_an, type_b, date_cr_b,
                                   ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                            FROM boost
                        ) b
                        WHERE b.rn = 1
                    ) lb ON a.id_an = lb.id_an
                    WHERE a.etat_an = 'valide'";

            if ($placeholders) {
                $sql .= " AND " . implode(" AND ", $placeholders);
            }

            // Ordering
            $orderBy = "ORDER BY 
                            CASE 
                                WHEN lb.type_b = 'gold' THEN 1
                                WHEN lb.type_b = 'silver' THEN 2 
                                ELSE 3 
                            END,
                            lb.date_cr_b DESC,
                            a.jaime DESC";

            if (!empty($options['orderBy'])) {
                $direction = strtoupper($options['orderDirection'] ?? 'ASC');
                $sql .= " ORDER BY " . $this->conn->real_escape_string($options['orderBy']) . " $direction, " . $orderBy;
            } else {
                $sql .= " " . $orderBy;
            }

            // Pagination
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

    // to create alot of images with one annonce 
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
    // get favoris annonce 
    public function allannoncefavoris($id, $key)
    {
        // Select the columns
        try {
            $sql = "SELECT a.*, b.type_b
                  FROM annonce a
                  JOIN favoris f ON a.id_an = f.id_an
                  LEFT JOIN boost b ON a.id_an = b.id_an
                   WHERE f.$key = $id
                   ORDER BY a.date_cr DESC
                   ;";

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

    public function Searchannonce($word, $page = 1, $perPage = 30)
    {
        //
        $searchableColumns = ['nom_an', 'categorie_an', 'type_fete', 'ville_an', 'adresse_an'];
        $offset = ($page - 1) * $perPage;
        //
        $whereClause = [];
        foreach ($searchableColumns as $column) {
            $whereClause[] = "$column LIKE ?";
        }
        $whereClause = implode(' OR ', $whereClause);
        //
        $sql = "SELECT a.*, b.type_b
        FROM annonce a
        LEFT JOIN (
            SELECT id_an, type_b
            FROM (
                SELECT id_an, type_b, 
                       ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                FROM boost 
            ) b_latest
            WHERE rn = 1
        ) b ON a.id_an = b.id_an
        WHERE ($whereClause) AND a.etat_an = 'valide'
        ORDER BY 
                CASE 
                WHEN b.type_b = 'gold' THEN 1 
                WHEN b.type_b = 'silver' THEN 2 
                ELSE 3 
            END,
            a.date_cr DESC, 
            a.jaime DESC
             LIMIT $perPage OFFSET $offset;
        ";


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
        //
        $countSql = "SELECT COUNT(*) as total FROM annonce a 
        WHERE ($whereClause) AND a.etat_an = 'valide'";

        $countStmt = $this->conn->prepare($countSql);
        if (!$countStmt) {
            throw new Exception("SQL Error: " . $this->conn->error);
        }

        $countStmt->bind_param($types, ...array_fill(0, count($searchableColumns), $searchWord));

        if (!$countStmt->execute()) {
            throw new Exception("Execution Error: " . $countStmt->error);
        }

        $countResult = $countStmt->get_result();
        $totalRows = $countResult->fetch_assoc()['total'] ?? 0;
        $totalPages = ceil($totalRows / $perPage);
        // 
        return [
            'paginate' => [
                'current_page' => (int)$page,
                'per_page' => $perPage,
                'total_rows' => $totalRows,
                'total_pages' => $totalPages,
            ],
            'data' => $data
        ];
    }

    public function whereVip($conditions = [], $page = 1, $perPage = 30)
    {
        try {
            $columns = implode(', ', $this->Columns['annonce']);
            $offset = ($page - 1) * $perPage;
            $placeholders = [];
            $values = [];
            $types = "";

            // Construct WHERE clause based on conditions
            foreach ($conditions as $condition) {
                [$column, $operator, $value] = $condition;

                if (is_array($value) && $operator === 'IN') {
                    $inPlaceholders = implode(',', array_fill(0, count($value), '?'));
                    $placeholders[] = "$column IN ($inPlaceholders)";
                    $values = array_merge($values, $value);
                    $types .= str_repeat('s', count($value));
                } else {
                    $placeholders[] = "$column $operator ?";
                    $values[] = ($operator === 'LIKE') ? "%{$value}%" : $value;
                    $types .= is_int($value) ? "i" : "s";
                }
            }

            // Base query with VIP filtering
            $baseQuery = "FROM annonce a 
            JOIN (
                SELECT 
                    b.*, ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                FROM boost b
                WHERE b.type_b = 'gold' AND b.etat_b = 'valide' AND DATE_ADD(b.date_cr_b, INTERVAL b.duree_b DAY) >= NOW() 
            ) latest_boost ON a.id_an = latest_boost.id_an AND latest_boost.rn = 1
            WHERE a.etat_an = 'valide'";

            // Append WHERE conditions if any
            if (!empty($placeholders)) {
                $baseQuery .= " AND " . implode(" AND ", $placeholders);
            }

            // Count total records for pagination
            $countSql = "SELECT COUNT(*) as total $baseQuery";
            $stmt = $this->conn->prepare($countSql);
            if ($values) {
                $stmt->bind_param($types, ...$values);
            }
            $stmt->execute();
            $countResult = $stmt->get_result();
            $totalRows = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $perPage);

            // Fetch paginated results
            $sql = "SELECT a.*, latest_boost.type_b $baseQuery
            ORDER BY a.date_cr DESC, a.jaime DESC
            LIMIT ? OFFSET ?";

            // Bind pagination values
            $values[] = $perPage;
            $values[] = $offset;
            $types .= "ii";

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

            // Return data with pagination info
            return [
                'paginate' => [
                    'current_page' => (int) $page,
                    'per_page' => $perPage,
                    'total_rows' => $totalRows,
                    'total_pages' => $totalPages,
                ],
                'data' => $data
            ];
            //
        } catch (Exception $e) {
            throw new Exception("Error in gold query: " . $e->getMessage());
        }
    }

    public function whereGold($conditions = [], $page = 1, $perPage = 30)
    {
        try {
            $columns = implode(', ', $this->Columns['annonce']);
            $offset = ($page - 1) * $perPage;
            $placeholders = [];
            $values = [];
            $types = "";

            // Construct WHERE clause based on conditions
            foreach ($conditions as $condition) {
                [$column, $operator, $value] = $condition;

                if (is_array($value) && $operator === 'IN') {
                    $inPlaceholders = implode(',', array_fill(0, count($value), '?'));
                    $placeholders[] = "$column IN ($inPlaceholders)";
                    $values = array_merge($values, $value);
                    $types .= str_repeat('s', count($value));
                } else {
                    $placeholders[] = "$column $operator ?";
                    $values[] = ($operator === 'LIKE') ? "%{$value}%" : $value;
                    $types .= is_int($value) ? "i" : "s";
                }
            }

            // Base query with hh filtering
            $baseQuery = "FROM annonce a 
                JOIN (
                    SELECT 
                        b.*, ROW_NUMBER() OVER (PARTITION BY id_an ORDER BY date_cr_b DESC) AS rn
                    FROM boost b
                    WHERE b.type_b = 'silver' AND b.etat_b = 'valide' AND DATE_ADD(b.date_cr_b, INTERVAL b.duree_b DAY) >= NOW() 
                ) latest_boost ON a.id_an = latest_boost.id_an AND latest_boost.rn = 1
                WHERE a.etat_an = 'valide'";

            // Append WHERE conditions if any
            if (!empty($placeholders)) {
                $baseQuery .= " AND " . implode(" AND ", $placeholders);
            }

            // Count total records for pagination
            $countSql = "SELECT COUNT(*) as total $baseQuery";
            $stmt = $this->conn->prepare($countSql);
            if ($values) {
                $stmt->bind_param($types, ...$values);
            }
            $stmt->execute();
            $countResult = $stmt->get_result();
            $totalRows = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $perPage);

            // Fetch paginated results
            $sql = "SELECT a.*, latest_boost.type_b $baseQuery
                ORDER BY a.date_cr DESC, a.jaime DESC
                LIMIT ? OFFSET ?";

            // Bind pagination values
            $values[] = $perPage;
            $values[] = $offset;
            $types .= "ii";

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

            // Return data with pagination info
            return [
                'paginate' => [
                    'current_page' => (int)$page,
                    'per_page' => $perPage,
                    'total_rows' => $totalRows,
                    'total_pages' => $totalPages,
                ],
                'data' => $data
            ];
            //
        } catch (Exception $e) {
            throw new Exception("Error in wheregold query: " . $e->getMessage());
        }
    }
    // get resarvation for client so he know his planning (annonce,resarvation,membre)
    public function ReservationsByDateClient($startDate, $endDate, $id)
    {
        try {
            // Get the columns for 'reservation' and 'membre' from the Columns array
            $reservationColumns = ['id_r ', 'date_r_debut', 'date_r_fin', 'etat_r', 'date_cr'];
            $membre = ['id_m', 'nom_m', 'ville_m', 'tel_m', 'email_m'];
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
    // get resarvation for membre so he know his planning (annonce,resarvation,client)
    public function ReservationsByDateMembre($startDate, $endDate, $id)
    {
        try {
            // Get the columns for 'reservation' and 'membre' from the Columns array
            $reservationColumns = ['id_r ', 'date_r_debut', 'date_r_fin', 'etat_r', 'date_cr'];
            $client = ['id_c', 'nom_c', 'ville_c', 'tel_c', 'email_c'];
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
    // to select annonce with images and boost informations
    public function findannonce($id)
    {
        // Define all columns for annonce selection
        $columns = "a.id_an, a.nom_an, a.categorie_an, a.type_fete, a.ville_an, 
                    a.adresse_an, a.date_cr, a.tel_an, a.mobile_an, a.tarif_an, 
                    a.detail_an, a.etat_an, a.id_a, a.id_mo, a.id_m, a.nature_tarif, 
                    a.visites, a.jaime, a.file_path, a.file_path_video, 
                    a.file_name, a.file_name_video";

        try {
            // Prepare SQL query with joins
            $sql = "SELECT 
                        $columns,
                        b.id_b, b.duree_b, b.etat_b, b.date_cr_b, b.type_b,
                        i.id_img, i.nom_img, i.chemin_img, i.id_an AS image_id_an
                    FROM annonce a
                    LEFT JOIN boost b ON b.id_an = a.id_an 
                        AND b.date_cr_b = (SELECT MAX(b2.date_cr_b) FROM boost b2 WHERE b2.id_an = a.id_an)
                    LEFT JOIN images i ON i.id_an = a.id_an
                    WHERE a.id_an = ?";

            $stmt = $this->conn->prepare($sql);
            // Bind parameters and execute
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            //
            if ($result->num_rows === 0) {
                http_response_code(404);
                throw new Exception('No resource found');
            }
            // Process results to structure images separately
            $annonce = null;
            $images = [];
            //
            while ($row = $result->fetch_assoc()) {
                if (!$annonce) {
                    // Store annonce and boost details only once
                    $annonce = [
                        'id_an' => $row['id_an'],
                        'nom_an' => $row['nom_an'],
                        'categorie_an' => $row['categorie_an'],
                        'type_fete' => $row['type_fete'],
                        'ville_an' => $row['ville_an'],
                        'adresse_an' => $row['adresse_an'],
                        'date_cr' => $row['date_cr'],
                        'tel_an' => $row['tel_an'],
                        'mobile_an' => $row['mobile_an'],
                        'tarif_an' => $row['tarif_an'],
                        'detail_an' => $row['detail_an'],
                        'etat_an' => $row['etat_an'],
                        'id_a' => $row['id_a'],
                        'id_mo' => $row['id_mo'],
                        'id_m' => $row['id_m'],
                        'nature_tarif' => $row['nature_tarif'],
                        'visites' => $row['visites'],
                        'jaime' => $row['jaime'],
                        'file_path' => $row['file_path'],
                        'file_path_video' => $row['file_path_video'],
                        'file_name' => $row['file_name'],
                        'file_name_video' => $row['file_name_video'],
                        'boost' => [
                            'id_b' => $row['id_b'],
                            'duree_b' => $row['duree_b'],
                            'etat_b' => $row['etat_b'],
                            'date_cr_b' => $row['date_cr_b'],
                            'type_b' => $row['type_b'],
                            'id_m' => $row['id_m'],
                            'id_an' => $row['id_an']
                        ],
                        'images' => []
                    ];
                }
                // Add images if available
                if ($row['id_img']) {
                    $images[] = [
                        'id_img' => $row['id_img'],
                        'nom_img' => $row['nom_img'],
                        'chemin_img' => $row['chemin_img'],
                        'id_an' => $row['id_an']
                    ];
                }
            }
            // Attach images to the response
            if ($annonce['boost']['id_b'] == null) {
                $annonce['boost'] = [];
            }
            $annonce['images'] = $images;
            return $annonce;
        } catch (Exception $e) {
            http_response_code(500);
            throw new Exception("Error fetching data: " . $e->getMessage());
        }
    }
    // to update visites of annonce 
    public function updateVisite($id)
    {
        try {
            // Prepare SQL statement to increment visites
            $sql = "UPDATE annonce SET visites = visites + 1 WHERE id_an = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $id);
            $stmt->execute();

            // Check if the update was successful
            if ($stmt->affected_rows === 0) {
                http_response_code(404);
                throw new Exception("Annonce not found or visites not updated.");
            }
        } catch (Exception $e) {
            http_response_code(500);
            throw new Exception(" visites not updated.");
        }
    }

    public function findfavoris($id, $key)
    {
        try {
            $sql = "SELECT id_an FROM favoris WHERE $key = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function exists_like($userId, $column, $id_an)
    {
        try {
            //
            $sql = "SELECT 1 FROM favoris WHERE $column = ? AND id_an = ?";
            //
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $userId, $id_an);
            $stmt->execute();
            //
            return (bool) $stmt->fetch();
            //
        } catch (Exception) {
            return false;
        }
    }


    public function updateLikeCount($id_an)
    {
        try {
            //
            $sql = "UPDATE annonce 
        SET jaime = (SELECT COUNT(*) FROM favoris WHERE id_an = ?) 
        WHERE id_an = ?";
            //
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $id_an, $id_an);
            $stmt->execute();
            //
            return $stmt->affected_rows > 0;
            //
        } catch (Exception) {
            throw new Exception('error occurese while trying update likes');
        }
    }

    public function delete_like($id, $key, $id_an)
    {
        $sql = "DELETE FROM {$this->tablename} WHERE {$key} = ? AND id_an = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            throw new Exception("Failed to prepare the SQL statement for deletion.");
        }

        $stmt->bind_param("ii", $id, $id_an);

        if (!$stmt->execute()) {
            http_response_code(500);
            throw new Exception("Failed to execute the delete statement.");
        }

        if ($stmt->affected_rows <= 0) {
            http_response_code(404);
            throw new Exception("No matching record found to delete.");
        }

        return true;
    }

    public function StoreFCM($title, $message, $id, $role)
    {
        // Validate role input
        if (!in_array($role, ['membre', 'client'])) {
            throw new Exception("Invalid role provided. Must be 'membre' or 'client'.");
        }
        // Use correct column name
        $columnid = ($role == 'client') ? 'id_c' : 'id_m';
        // Single query to fetch fcm_token and insert into notify
        $sql = "
            INSERT INTO notify (title, message, fcm_token)
            SELECT ?, ?, fcm_token FROM $role WHERE $columnid = ?
        ";
        //
        $stmt = $this->conn->prepare($sql);
        //
        if (!$stmt) {
            http_response_code(500);
            throw new Exception("Failed to prepare the SQL statement: " . $this->conn->error);
        }
        // Bind only the first two parameters (title, message)
        $stmt->bind_param("ssi", $title, $message, $id);
        //
        if (!$stmt->execute()) {
            http_response_code(500);
            throw new Exception("Failed to execute the SQL statement: " . $stmt->error);
        }
        //
        $notifyId = $stmt->insert_id;
        $stmt->close();
        //
        return $notifyId;
    }
    public function GetNotify()
    {
        try {
            $sql = "SELECT * FROM notify WHERE status IS NULL ";
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

    public function DeleteNotify($id)
    {
        $sql = "DELETE FROM notify WHERE id = ?  LIMIT 1";
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

    public function UpdateStatus($id, $status)
    {
        $sql = "UPDATE notify SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            throw new Exception("Failed to prepare the SQL statement for update.");
        }

        $stmt->bind_param("si", $status, $id);

        if (!$stmt->execute()) {
            http_response_code(500);
            throw new Exception("Failed to execute the update statement.");
        }

        if ($stmt->affected_rows <= 0) {
            http_response_code(404);
            throw new Exception("No matching record found to update.");
        }

        return true;
    }

    public function AddPointCoins($code)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE membre SET code_use = code_use + 1 WHERE code = ?");
            $stmt->execute([$code]);
            // 
            return  1;
            //
        } catch (Exception $e) {
            return 0;
        }
    }

    public function hasUsedCodeMoreThanTen($id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT code_use FROM membre WHERE id_m = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && $result['code_use'] > 10;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createBoostForMembre($id, $id_an)
    {
        try {
            // Start transaction
            $this->conn->autocommit(false);

            // 1. Fetch member
            $stmt = $this->conn->prepare("SELECT code_use FROM membre WHERE id_m = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $membre = $result->fetch_assoc();
            $stmt->close();

            if (!$membre || $membre['code_use'] < 10) {
                $this->conn->rollback();
                throw new Exception('ur point are not enough');
            }

            // 2. Deduct 10 points
            $stmt = $this->conn->prepare("UPDATE membre SET code_use = code_use - 10 WHERE id_m = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // 3. Create boost
            $duree_b = 7;
            $tarif_b = 0.0;
            $recu_b = null;
            $etat_b = 'valide';
            $id_mo = 1;
            $type_b = 'silver';

            $stmt = $this->conn->prepare("INSERT INTO boost (
                duree_b, tarif_b, recu_b, etat_b, id_m, id_an, date_cr_b, id_mo, type_b
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)");

            $stmt->bind_param("idsssiss", $duree_b, $tarif_b, $recu_b, $etat_b, $id, $id_an, $id_mo, $type_b);
            $stmt->execute();
            $stmt->close();

            // Commit transaction
            $this->conn->commit();
            return true;
            //
        } catch (Exception $e) {
            $this->conn->rollback();
            throw new Exception('cant create boost: ' . $e->getMessage());
        }
    }
}
