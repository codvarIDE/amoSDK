<?php
/**
 * Database SDK using PDO
 * @author Claude
 */

class amoSDK {
    private $pdo;
    private $table;
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Constructor - Initialize database connection
     */
    public function __construct($host, $dbname, $username, $password, $charset = 'utf8mb4') {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        
        try {
            $this->pdo = new PDO($dsn, $username, $password, $this->options);
        } catch (\PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Set the current working table
     */
    public function table($tableName) {
        $this->table = $tableName;
        return $this;
    }

    /**
     * Create new record
     */
    public function create(array $data) {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($values)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Create failed: " . $e->getMessage());
        }
    }

    /**
     * Read records
     */
    public function read($conditions = [], $fields = '*', $orderBy = '', $limit = null) {
        $sql = "SELECT $fields FROM {$this->table}";
        $values = [];

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            foreach ($conditions as $key => $value) {
                $sql .= "$key = ? AND ";
                $values[] = $value;
            }
            $sql = rtrim($sql, ' AND ');
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception("Read failed: " . $e->getMessage());
        }
    }

    /**
     * Update records
     */
    public function update(array $data, array $conditions) {
        $sql = "UPDATE {$this->table} SET ";
        $values = [];

        foreach ($data as $key => $value) {
            $sql .= "$key = ?, ";
            $values[] = $value;
        }
        $sql = rtrim($sql, ', ');

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            foreach ($conditions as $key => $value) {
                $sql .= "$key = ? AND ";
                $values[] = $value;
            }
            $sql = rtrim($sql, ' AND ');
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (\PDOException $e) {
            throw new \Exception("Update failed: " . $e->getMessage());
        }
    }

    /**
     * Delete records
     */
    public function delete(array $conditions) {
        $sql = "DELETE FROM {$this->table}";
        $values = [];

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            foreach ($conditions as $key => $value) {
                $sql .= "$key = ? AND ";
                $values[] = $value;
            }
            $sql = rtrim($sql, ' AND ');
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (\PDOException $e) {
            throw new \Exception("Delete failed: " . $e->getMessage());
        }
    }

    /**
     * Execute raw SQL query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Get count of records
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $values = [];

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            foreach ($conditions as $key => $value) {
                $sql .= "$key = ? AND ";
                $values[] = $value;
            }
            $sql = rtrim($sql, ' AND ');
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetch()['count'];
        } catch (\PDOException $e) {
            throw new \Exception("Count failed: " . $e->getMessage());
        }
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }
}
