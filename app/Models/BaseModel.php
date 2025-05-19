<?php

namespace App\Models;

use Config\Database;
use PDO;
use PDOException;
use Exception;

class Model
{
    protected $pdo;
    protected $table;
    
    public function __construct()
    {
        // Get the PDO connection from the Database singleton
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * Execute a query with support for PostgreSQL commands and formatting with newlines
     * 
     * @param string $sql The SQL query with optional newlines for better formatting
     * @param array $params Parameters to bind to the query
     * @param bool $fetchAll Whether to fetch all results or just one
     * @return mixed Query results
     */
    public function query($sql, $params = [], $fetchAll = true)
    {
        try {
            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                // Skip array values - they should be processed separately before calling query
                if (is_array($value)) {
                    continue;
                }
                
                $paramType = PDO::PARAM_STR;
                
                if (is_int($value)) {
                    $paramType = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $paramType = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $paramType = PDO::PARAM_NULL;
                }
                
                // Check if the key is a named parameter or positional
                if (is_string($key) && strpos($key, ':') !== 0) {
                    $key = ':' . $key;
                }
                
                $stmt->bindValue($key, $value, $paramType);
            }
            
            // Execute the query
            $stmt->execute();
            
            // Return the results
            if ($fetchAll) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            // Log the error or handle it as needed
            error_log("Database query error: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get a single record
     * 
     * @param string $sql The SQL query with optional newlines for formatting
     * @param array $params Parameters to bind to the query
     * @return array|null Single result row or null if not found
     */
    public function queryOne($sql, $params = [])
    {
        $result = $this->query($sql, $params, false);
        return $result === false ? null : $result;
    }
    
    /**
     * Get a single scalar value from the first column of the first row
     * 
     * @param string $sql The SQL query with optional newlines for formatting
     * @param array $params Parameters to bind to the query
     * @param mixed $default Default value if no result is returned
     * @return mixed Scalar value or default value if no result
     */
    public function queryScalar($sql, $params = [], $default = null)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                // Skip array values - they should be processed separately before calling queryScalar
                if (is_array($value)) {
                    continue;
                }
                
                $paramType = PDO::PARAM_STR;
                
                if (is_int($value)) {
                    $paramType = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $paramType = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $paramType = PDO::PARAM_NULL;
                }
                
                if (is_string($key) && strpos($key, ':') !== 0) {
                    $key = ':' . $key;
                }
                
                $stmt->bindValue($key, $value, $paramType);
            }
            
            $stmt->execute();
            
            // Fetch only the first column of the first row
            $result = $stmt->fetchColumn();
            
            // Return the scalar value or default if null
            return ($result !== false) ? $result : $default;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get the value of a specific column from the first row
     * 
     * @param string $sql The SQL query
     * @param array $params Parameters to bind to the query
     * @param string $column The column name to retrieve
     * @param mixed $default Default value if not found
     * @return mixed The column value or default
     */
    public function queryValue($sql, $params = [], $column = null, $default = null)
    {
        $row = $this->queryOne($sql, $params);
        
        if (!$row) {
            return $default;
        }
        
        if ($column === null) {
            // If no column specified, return the first column
            return reset($row);
        }
        
        return isset($row[$column]) ? $row[$column] : $default;
    }
    
    /**
     * Execute a query with no return value (INSERT, UPDATE, DELETE)
     * 
     * @param string $sql The SQL query with optional newlines for formatting
     * @param array $params Parameters to bind to the query
     * @return int Number of affected rows
     */
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                // Skip array values - they should be processed separately before calling execute
                if (is_array($value)) {
                    continue;
                }
                
                $paramType = PDO::PARAM_STR;
                
                if (is_int($value)) {
                    $paramType = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $paramType = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $paramType = PDO::PARAM_NULL;
                }
                
                if (is_string($key) && strpos($key, ':') !== 0) {
                    $key = ':' . $key;
                }
                
                $stmt->bindValue($key, $value, $paramType);
            }
            
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Database execution error: " . $e->getMessage() . " - SQL: " . $sql);
            throw new Exception("Database execution failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get the PDO parameter type for a value
     * 
     * @param mixed $value The value to determine type for
     * @return int PDO parameter type
     */
    private function getParamType($value)
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }
    
    /**
     * Get the last inserted ID
     * 
     * @param string|null $sequenceName Name of the sequence (for PostgreSQL)
     * @return string The last inserted ID
     */
    public function lastInsertId($sequenceName = null)
    {
        return $this->pdo->lastInsertId($sequenceName);
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit()
    {
        // Check if there's an active transaction before committing
        if ($this->pdo->inTransaction()) {
            return $this->pdo->commit();
        }
        return true; // Return true if there's no active transaction
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback()
    {
        // Check if there's an active transaction before rolling back
        if ($this->pdo->inTransaction()) {
            return $this->pdo->rollBack();
        }
        return true; // Return true if there's no active transaction
    }

    /**
     * Format columns and placeholders for INSERT statements
     * 
     * @param array $data Associative array of column => value pairs
     * @param array $exclude Array of column names to exclude
     * @param array $expressions Associative array of column => sql expression pairs
     * @return array Associative array with 'columns' and 'placeholders' keys
     */
    protected function formatInsertData(array $data, array $exclude = [], array $expressions = [])
    {
        // Filter out excluded columns
        $filteredData = array_diff_key($data, array_flip($exclude));
        
        // Filter out array values which can't be inserted directly
        foreach ($filteredData as $key => $value) {
            if (is_array($value)) {
                unset($filteredData[$key]);
            }
        }
        
        $columns = [];
        $placeholders = [];
        
        // Process regular column/value pairs
        foreach ($filteredData as $column => $value) {
            $columns[] = $column;
            $placeholders[] = ":$column";
        }
        
        // Add custom SQL expressions
        foreach ($expressions as $column => $expression) {
            $columns[] = $column;
            $placeholders[] = $expression;
        }
        
        return [
            'columns' => implode(', ', $columns),
            'placeholders' => implode(', ', $placeholders),
            'filteredData' => $filteredData
        ];
    }

    /**
     * Format SET clause for UPDATE statements
     * 
     * @param array $data Associative array of column => value pairs
     * @param array $exclude Array of column names to exclude
     * @param array $expressions Associative array of column => sql expression pairs
     * @return array Associative array with 'updateClause' and 'filteredData' keys
     */
    protected function formatUpdateData(array $data, array $exclude = [], array $expressions = [])
    {
        // Filter out excluded columns
        $filteredData = array_diff_key($data, array_flip($exclude));
        
        // Filter out array values which can't be updated directly
        foreach ($filteredData as $key => $value) {
            if (is_array($value)) {
                unset($filteredData[$key]);
            }
        }
        
        $updateParts = [];
        
        // Process regular column/value pairs
        foreach ($filteredData as $column => $value) {
            $updateParts[] = "$column = :$column";
        }
        
        // Add custom SQL expressions
        foreach ($expressions as $column => $expression) {
            $updateParts[] = "$column = $expression";
        }
        
        return [
            'updateClause' => implode(', ', $updateParts),
            'filteredData' => $filteredData
        ];
    }
}