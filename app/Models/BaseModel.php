<?php

namespace App\Models;

use Config\Database;

class BaseModel
{
    // Database connection and table properties
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    // Query building properties
    protected $select = '*';
    protected $joins = [];
    protected $wheres = [];
    protected $groupBy = '';
    protected $orderBy = '';
    protected $limit;
    protected $offset;
    protected $params = [];
    
    // Model configuration properties
    protected $fillable = [];
    protected $useSoftDeletes = false;
    protected $timestamps = false;
    protected $createdAtColumn = 'created_at';
    protected $updatedAtColumn = 'updated_at';
    protected $deletedAtColumn = 'deleted_at';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // -----------------------------------------
    // Basic CRUD Operations
    // -----------------------------------------
    
    /**
     * Get all records
     */
    public function all()
    {
        $this->whereSoftDeleted();
        return $this->get();
    }

    /**
     * Find a record by primary key
     */
    public function find($id)
    {
        $this->where("{$this->table}.{$this->primaryKey} = :id")
             ->whereSoftDeleted();
        $this->bind(['id' => $id]);

        $result = $this->limit(1)->get();
        return $result[0] ?? null;
    }

    /**
     * Get the first record from the query
     */
    public function first()
    {
        $result = $this->limit(1)->get();
        return $result[0] ?? null;
    }

    /**
     * Insert a new record
     */
    public function insert(array $data)
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $data[$this->createdAtColumn] = $now;
            $data[$this->updatedAtColumn] = $now;
        }

        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        return $this->execute($sql, $data);
    }

    /**
     * Update records matching the where condition
     */
    public function update(array $data, $where, array $whereParams = [])
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        if ($this->timestamps) {
            $data[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $set WHERE $where";
        return $this->execute($sql, array_merge($data, $whereParams));
    }

    /**
     * Delete records matching the where condition
     */
    public function delete($where, array $params = [])
    {
        if ($this->useSoftDeletes) {
            $sql = "UPDATE {$this->table} SET {$this->deletedAtColumn} = NOW() WHERE $where";
        } else {
            $sql = "DELETE FROM {$this->table} WHERE $where";
        }
        return $this->execute($sql, $params);
    }

    /**
     * Restore soft-deleted records
     */
    public function restore($where, array $params = [])
    {
        if (!$this->useSoftDeletes) return false;
        
        $sql = "UPDATE {$this->table} SET {$this->deletedAtColumn} = NULL WHERE $where";
        return $this->execute($sql, $params);
    }

    /**
     * Truncate the table
     */
    public function truncate()
    {
        return $this->db->exec("TRUNCATE TABLE {$this->table}");
    }

    // -----------------------------------------
    // Query Building Methods
    // -----------------------------------------
    
    /**
     * Select specific columns
     */
    public function select($columns)
    {
        $this->select = $columns;
        return $this;
    }
    
    /**
     * Add a join clause
     */
    public function join($table, $firstKey, $secondKey, $type = 'INNER')
    {
        $this->joins[] = "$type JOIN $table ON $firstKey = $secondKey";
        return $this;
    }

    /**
     * Add a where condition
     */
    public function where($condition)
    {
        $this->wheres[] = $condition;
        return $this;
    }

    /**
     * Add where condition for soft deleted records
     */
    public function whereSoftDeleted($alias = null)
    {
        if ($this->useSoftDeletes) {
            $col = $alias ? "$alias.{$this->deletedAtColumn}" : "{$this->table}.{$this->deletedAtColumn}";
            $this->wheres[] = "$col IS NULL";
        }
        return $this;
    }

    /**
     * Include soft-deleted records
     */
    public function withDeleted()
    {
        $this->useSoftDeletes = false;
        return $this;
    }

    /**
     * Add parameters to bind
     */
    public function bind(array $params)
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Add a group by clause
     */
    public function groupBy($columns)
    {
        $this->groupBy = "GROUP BY $columns";
        return $this;
    }

    /**
     * Add an order by clause
     */
    public function orderBy($columns)
    {
        $this->orderBy = "ORDER BY $columns";
        return $this;
    }

    /**
     * Set the query limit
     */
    public function limit($number)
    {
        $this->limit = (int) $number;
        return $this;
    }

    /**
     * Set the query offset
     */
    public function offset($number)
    {
        $this->offset = (int) $number;
        return $this;
    }

    // -----------------------------------------
    // Aggregation Methods
    // -----------------------------------------
    
    /**
     * Count records
     */
    public function count($column = '*')
    {
        return $this->aggregate('COUNT', $column);
    }

    /**
     * Calculate the sum of a column
     * 
     * @param string $column The column to calculate sum
     * @return int|float The sum result
     */
    public function sum($column)
    {
        return $this->aggregate('SUM', $column);
    }

    /**
     * Calculate the average of a column
     * 
     * @param string $column The column to calculate average
     * @return int|float The average result
     */
    public function avg($column)
    {
        return $this->aggregate('AVG', $column);
    }

    /**
     * Get the minimum value of a column
     * 
     * @param string $column The column to get minimum value
     * @return mixed The minimum value
     */
    public function min($column)
    {
        return $this->aggregate('MIN', $column);
    }

    /**
     * Get the maximum value of a column
     */
    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    /**
     * Perform an aggregate function
     */
    public function aggregate($function, $column, $alias = null)
    {
        $column = $column === '*' ? '*' : "`$column`";
        $alias = $alias ?: strtolower($function);
        
        $originalSelect = $this->select;
        $this->select = "$function($column) as $alias";
        
        $result = $this->get();
        $this->select = $originalSelect;
        
        return isset($result[0][$alias]) ? $result[0][$alias] : 0;
    }

    /**
     * Perform multiple aggregate functions
     */
    public function aggregates(array $aggregates)
    {
        $selects = [];
        
        foreach ($aggregates as $agg) {
            if (count($agg) < 2) {
                continue;
            }
            
            $function = $agg[0];
            $column = $agg[1] === '*' ? '*' : "`{$agg[1]}`";
            $alias = $agg[2] ?? strtolower($function . '_' . $agg[1]);
            
            $selects[] = "$function($column) as $alias";
        }
        
        if (empty($selects)) {
            return [];
        }
        
        $originalSelect = $this->select;
        $this->select = implode(', ', $selects);
        
        $result = $this->get();
        $this->select = $originalSelect;
        
        return $result[0] ?? [];
    }

    // -----------------------------------------
    // Query Execution Methods
    // -----------------------------------------
    
    /**
     * Execute the query and get results
     */
    public function get(array $params = [])
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if ($this->groupBy) {
            $sql .= ' ' . $this->groupBy;
        }

        if ($this->orderBy) {
            $sql .= ' ' . $this->orderBy;
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $this->rawQuery($sql, array_merge($this->params, $params));
    }

    /**
     * Check if records exist
     */
    public function exists($where, array $params = [])
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE $where LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Paginate the results
     *
     * @param int $page The page number (1-based)
     * @param int $perPage Number of items per page
     * @return array Array with 'data', 'pagination' information
     */
    public function paginate($page = 1, $perPage = 15)
    {
        // Save the current query state
        $originalSelect = $this->select;
        $originalJoins = $this->joins;
        $originalWheres = $this->wheres;
        $originalParams = $this->params;
        $originalGroupBy = $this->groupBy;
        $originalOrderBy = $this->orderBy;
        $originalLimit = $this->limit;
        $originalOffset = $this->offset;
        
        // For the count query, we only need a simple COUNT
        $this->select = "COUNT(*) as total";
        
        // If there are joins, we need to be more specific to avoid duplicate counting
        if (!empty($this->joins)) {
            $this->select = "COUNT(DISTINCT {$this->table}.{$this->primaryKey}) as total";
        }
        
        // Temporarily remove ORDER BY as it's not needed for counting and can cause issues
        $this->orderBy = '';
        
        // Get total count
        $countResult = $this->get();
        $total = isset($countResult[0]['total']) ? (int)$countResult[0]['total'] : 0;
        
        // Restore original query state
        $this->select = $originalSelect;
        $this->joins = $originalJoins;
        $this->wheres = $originalWheres;
        $this->params = $originalParams;
        $this->groupBy = $originalGroupBy;
        $this->orderBy = $originalOrderBy;
        
        // Calculate pagination values
        $page = max(1, (int)$page); // Ensure page is at least 1
        $perPage = max(1, (int)$perPage); // Ensure items per page is at least 1
        $lastPage = ceil($total / $perPage);
        $lastPage = max(1, $lastPage); // Ensure last page is at least 1
        
        // Apply pagination limits
        $this->limit($perPage);
        $this->offset(($page - 1) * $perPage);
        
        // Get the paginated data
        $data = $this->get();
        
        // Restore original limit and offset
        $this->limit = $originalLimit;
        $this->offset = $originalOffset;
        
        // Create pagination information
        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'first_page_url' => 1,
            'last_page_url' => $lastPage,
            'next_page_url' => $page < $lastPage ? $page + 1 : null,
            'prev_page_url' => $page > 1 ? $page - 1 : null,
            'from' => ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
        ];
        
        return [
            'data' => $data,
            'pagination' => $pagination
        ];
    }

    // -----------------------------------------
    // Internal Helper Methods
    // -----------------------------------------
    
    /**
     * Execute a raw query and fetch results
     */
    protected function rawQuery($query, $params = [])
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        $this->reset(); // Reset after fetching
        return $result;
    }

    /**
     * Execute a query without fetching results
     */
    protected function execute($query, $params = [])
    {
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute($params);
        $this->reset(); // Reset after execution
        return $result;
    }

    /**
     * Reset query builder state
     */
    protected function reset()
    {
        $this->select = '*';
        $this->joins = [];
        $this->wheres = [];
        $this->groupBy = '';
        $this->orderBy = '';
        $this->limit = null;
        $this->offset = null;
        $this->params = [];
        $this->useSoftDeletes = true;
    }
}