<?php

namespace App\Models;

class BookModel extends BaseModel
{
    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'author', 'genre', 'publication_date', 'price', 'pages', 'status', 'cover', 'description'];
    protected $searchableFields = ['title', 'author', 'genre', 'description'];
    
    /**
     * Get books with pagination and ordering
     */
    public function getBooks($orderBy = 'id ASC', $start = 0, $length = 10)
    {
        if (!empty($orderBy)) {
            $this->orderBy($orderBy);
        }
        
        return $this->limit($length)
                   ->offset($start)
                   ->get();
    }
    
    /**
     * Search books by term
     */
    public function search($term, $orderBy = 'id ASC', $start = 0, $length = 10)
    {
        $conditions = [];
        $params = [];
        
        foreach ($this->searchableFields as $index => $field) {
            $paramName = "search{$index}";
            $conditions[] = "$field LIKE :$paramName";
            $params[$paramName] = $term;
        }
        
        $whereClause = '(' . implode(' OR ', $conditions) . ')';
        
        if (!empty($orderBy)) {
            $this->orderBy($orderBy);
        }
        
        return $this->where($whereClause, $params)
                   ->limit($length)
                   ->offset($start)
                   ->get();
    }
    
    /**
     * Count search results
     */
    public function countSearchResults($term)
    {
        $conditions = [];
        $params = [];
        
        foreach ($this->searchableFields as $index => $field) {
            $paramName = "search{$index}";
            $conditions[] = "$field LIKE :$paramName";
            $params[$paramName] = $term;
        }
        
        $whereClause = '(' . implode(' OR ', $conditions) . ')';
        
        return $this->where($whereClause, $params)->count();
    }
}