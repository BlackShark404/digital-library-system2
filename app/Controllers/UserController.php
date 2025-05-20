<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\WishlistModel;

class UserController extends BaseController{
    protected $bookModel;
    protected $wishlistModel;
    
    public function __construct() {
        parent::__construct();
        $this->bookModel = new BookModel();
        $this->wishlistModel = new WishlistModel();
    }
    
    public function renderUserDashboard() {
        $this->render('/user/dashboard');
    }

    public function renderBrowseBooks() {
        // Get all books from the database
        $books = $this->bookModel->getAllBooks();
        
        // Transform book data to match the view's expected format
        $formattedBooks = $this->formatBooksForView($books);
        
        // Get all genres from the database
        $genreObjects = $this->bookModel->getAllGenres();
        $genres = array_column($genreObjects, 'g_name');
        
        // Get filter parameters
        $search = $this->getRequestParam('search', '');
        $genre = $this->getRequestParam('genre', '');
        $sort = $this->getRequestParam('sort', 'title_asc');
        
        // Filter books based on search term
        if (!empty($search)) {
            $filtered_books = [];
            foreach ($formattedBooks as $book) {
                if (stripos($book['title'], $search) !== false || stripos($book['author'], $search) !== false) {
                    $filtered_books[] = $book;
                }
            }
            $formattedBooks = $filtered_books;
        }
        
        // Filter books based on genre
        if (!empty($genre)) {
            $filtered_books = [];
            foreach ($formattedBooks as $book) {
                if ($book['genre'] === $genre) {
                    $filtered_books[] = $book;
                }
            }
            $formattedBooks = $filtered_books;
        }
        
        // Sort books
        $this->sortBooks($formattedBooks, $sort);
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? 0;
        
        // Check if user has books in wishlist
        if ($userId > 0) {
            foreach ($formattedBooks as &$book) {
                $book['in_wishlist'] = $this->wishlistModel->isBookInWishlist($userId, $book['id']);
            }
        }
        
        $this->render('/user/browse-books', [
            'books' => $formattedBooks,
            'genres' => $genres,
            'search' => $search,
            'genre' => $genre,
            'sort' => $sort
        ]);
    }

    public function renderReadingSessions() {
        $this->render('/user/reading-sessions');
    }

    public function renderWishlist() {
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? 0;
        
        if ($userId <= 0) {
            // Redirect to login if not logged in
            $this->redirect('/login');
        }
        
        // Get wishlist items from database
        $wishlistBooks = $this->wishlistModel->getUserWishlist($userId);
        
        $this->render('/user/wishlist', [
            'wishlist_books' => $wishlistBooks
        ]);
    }

    public function renderPurchases() {
        $this->render('/user/purchases');
    }

    public function renderUserProfile() {
        $this->render('/user/user-profile');
    }
    
    /**
     * Add a book to wishlist
     */
    public function addToWishlist() {
        if (!$this->isPost()) {
            $this->redirect('/user/browse-books');
        }
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? 0;
        
        if ($userId <= 0) {
            // Return error response for AJAX or redirect for regular request
            if ($this->isAjax()) {
                $this->jsonError('You must be logged in to add items to wishlist', 401);
            } else {
                $this->redirect('/login');
            }
        }
        
        // Check if this is an AJAX request with JSON data
        if ($this->isAjax() && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            // Get data from JSON request body
            $jsonData = $this->getJsonInput();
            $bookId = isset($jsonData['book_id']) ? intval($jsonData['book_id']) : 0;
        } else {
            // Get book ID from request parameters
            $bookId = $this->getRequestParam('book_id', 0);
        }
        
        if ($bookId <= 0) {
            // Return error response for AJAX or redirect for regular request
            if ($this->isAjax()) {
                $this->jsonError('Invalid book ID', 400);
            } else {
                $this->redirect('/user/browse-books');
            }
        }
        
        // Add to wishlist
        $success = $this->wishlistModel->addToWishlist($userId, $bookId);
        
        if ($this->isAjax()) {
            if ($success) {
                $this->jsonSuccess([], 'Book added to wishlist');
            } else {
                $this->jsonError('Failed to add book to wishlist', 500);
            }
        } else {
            $this->redirect('/user/wishlist');
        }
    }
    
    /**
     * Remove a book from wishlist
     */
    public function removeFromWishlist() {
        if (!$this->isPost()) {
            $this->redirect('/user/wishlist');
        }
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? 0;
        
        if ($userId <= 0) {
            // Return error response for AJAX or redirect for regular request
            if ($this->isAjax()) {
                $this->jsonError('You must be logged in to manage your wishlist', 401);
            } else {
                $this->redirect('/login');
            }
        }
        
        // Check if this is an AJAX request with JSON data
        if ($this->isAjax() && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            // Get data from JSON request body
            $jsonData = $this->getJsonInput();
            $wishlistId = isset($jsonData['wishlist_id']) ? intval($jsonData['wishlist_id']) : 0;
        } else {
            // Get wishlist ID from request parameters
            $wishlistId = $this->getRequestParam('wishlist_id', 0);
        }
        
        if ($wishlistId <= 0) {
            // Return error response for AJAX or redirect for regular request
            if ($this->isAjax()) {
                $this->jsonError('Invalid wishlist ID', 400);
            } else {
                $this->redirect('/user/wishlist');
            }
        }
        
        // Remove from wishlist
        $success = $this->wishlistModel->removeFromWishlist($wishlistId, $userId);
        
        if ($this->isAjax()) {
            if ($success) {
                $this->jsonSuccess([], 'Book removed from wishlist');
            } else {
                $this->jsonError('Failed to remove book from wishlist', 500);
            }
        } else {
            $this->redirect('/user/wishlist');
        }
    }
    
    /**
     * Handle AJAX request to toggle wishlist status
     */
    public function toggleWishlist() {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('/user/browse-books');
        }
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? 0;
        
        if ($userId <= 0) {
            $this->jsonError('You must be logged in to manage your wishlist', 401);
        }
        
        // Get data from JSON request body
        $jsonData = $this->getJsonInput();
        
        // Get book ID and action from JSON data
        $bookId = isset($jsonData['book_id']) ? intval($jsonData['book_id']) : 0;
        $action = $jsonData['action'] ?? '';
        
        if ($bookId <= 0) {
            $this->jsonError('Invalid book ID', 400);
        }
        
        if ($action === 'add') {
            // Add to wishlist
            $success = $this->wishlistModel->addToWishlist($userId, $bookId);
            if ($success) {
                $this->jsonSuccess(['in_wishlist' => true], 'Book added to wishlist');
            } else {
                $this->jsonError('Failed to add book to wishlist', 500);
            }
        } else if ($action === 'remove') {
            // Remove from wishlist
            $success = $this->wishlistModel->removeBookFromWishlist($userId, $bookId);
            if ($success) {
                $this->jsonSuccess(['in_wishlist' => false], 'Book removed from wishlist');
            } else {
                $this->jsonError('Failed to remove book from wishlist', 500);
            }
        } else {
            $this->jsonError('Invalid action', 400);
        }
    }
    
    /**
     * Format books from database for the view
     * 
     * @param array $books Books from database
     * @return array Formatted books for view
     */
    private function formatBooksForView($books) {
        $formatted = [];
        
        foreach ($books as $book) {
            $coverPath = $book['b_cover_path'] 
                ? '/assets/images/book-cover/' . $book['b_cover_path'] 
                : '/assets/images/book-cover/default-cover.svg';
            
            $formatted[] = [
                'id' => $book['b_id'],
                'title' => $book['b_title'],
                'author' => $book['b_author'],
                'genre' => $book['genre'] ?? 'Uncategorized',
                'description' => $book['b_description'] ?? '',
                'cover_image' => $coverPath,
                'price' => (float)$book['b_price'],
                'published_date' => $book['b_publication_date'] ?? '',
                'in_wishlist' => false
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Sort books based on sort parameter
     * 
     * @param array &$books Books to sort (passed by reference)
     * @param string $sort Sort parameter
     */
    private function sortBooks(&$books, $sort) {
        switch ($sort) {
            case 'title_desc':
                usort($books, function ($a, $b) {
                    return strcmp($b['title'], $a['title']);
                });
                break;
            case 'author_asc':
                usort($books, function ($a, $b) {
                    return strcmp($a['author'], $b['author']);
                });
                break;
            case 'author_desc':
                usort($books, function ($a, $b) {
                    return strcmp($b['author'], $a['author']);
                });
                break;
            case 'published_asc':
                usort($books, function ($a, $b) {
                    return strcmp($a['published_date'], $b['published_date']);
                });
                break;
            case 'published_desc':
                usort($books, function ($a, $b) {
                    return strcmp($b['published_date'], $a['published_date']);
                });
                break;
            case 'title_asc':
            default:
                usort($books, function ($a, $b) {
                    return strcmp($a['title'], $b['title']);
                });
                break;
        }
    }
}