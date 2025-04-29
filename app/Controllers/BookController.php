<?php

namespace App\Controllers;

class BookController extends BaseController
{
    public function index()
    {
        $this->render('book-management', [
            'title' => 'Book Management'
        ]);
    }
    
    public function getBooks()
    {
        // Check if this is an AJAX request
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        $bookModel = $this->loadModel('BookModel');
        
        // Get DataTables parameters
        $draw = $this->request('draw', 0);
        $start = $this->request('start', 0);
        $length = $this->request('length', 10);
        $search = $this->request('search', ['value' => '']);
        $order = $this->request('order', []);
        
        // Column ordering
        $columns = [
            'id', 'cover', 'title', 'author', 'genre', 'price', 'status', null // 'actions' column doesn't exist in DB
        ];
        
        // Set up sorting
        $orderBy = '';
        if (!empty($order)) {
            $columnIdx = $order[0]['column'];
            $direction = $order[0]['dir'];
            
            if (isset($columns[$columnIdx]) && $columns[$columnIdx] !== null) {
                $orderBy = $columns[$columnIdx] . ' ' . $direction;
            }
        }
        
        // Get total count (before filtering)
        $totalRecords = $bookModel->count();
        
        // Apply search if provided
        $filteredRecords = $totalRecords;
        $books = [];
        
        if (!empty($search['value'])) {
            $searchTerm = '%' . $search['value'] . '%';
            $books = $bookModel->search($searchTerm, $orderBy, $start, $length);
            $filteredRecords = $bookModel->countSearchResults($searchTerm);
        } else {
            $books = $bookModel->getBooks($orderBy, $start, $length);
        }
        
        // Format data for DataTables
        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book['id'],
                'cover' => '<img src="' . $book['cover'] . '" alt="' . htmlspecialchars($book['title']) . '" class="img-thumbnail" width="50">',
                'title' => htmlspecialchars($book['title']) . '<div class="small text-muted">' . $book['pages'] . ' pages</div>',
                'author' => htmlspecialchars($book['author']),
                'genre' => htmlspecialchars($book['genre']),
                'price' => '$' . number_format($book['price'], 2),
                'status' => '<span class="badge ' . (($book['status'] === 'Published') ? 'bg-success' : 'bg-warning text-dark') . '">' . $book['status'] . '</span>',
                'actions' => '' // We'll use a renderer for this
            ];
        }
        
        // Response in the format expected by DataTables
        $this->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
}