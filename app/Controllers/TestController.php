<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TestController extends BaseController{
    protected $testModel;
    protected $userModel;

    public function __construct()
    {
        $this->testModel = $this->loadModel('TestModel');
        $this->userModel = $this->loadModel('UserModel');
    }

    public function showUser() {
        $user = $this->testModel->getUserById(1);

        $username = $user['username'];

        $this->render('test/display-user', [
            'username' => $username
        ]);
    }

    public function renderDatableTest() {

        $this->render('test/datable-test');
    }
    
    public function paginateUsers() {
    // Check if this is a DataTables AJAX request
    if (isset($_GET['draw'])) {
        // DataTables server-side processing parameters
        $draw = $_GET['draw'];
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $searchValue = $_GET['search']['value'] ?? '';
        
        // Column ordering
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'asc';
        
        // Column names
        $columns = [
            'users.id',
            'users.first_name', // Will need logic to handle full name sorting
            'users.email',
            'roles.name',
            'users.is_active',
            'users.last_login'
        ];
        
        // Get column to order by
        $orderByColumn = $columns[$orderColumn] ?? 'users.id';
        
        // Get filtered data and total counts
        $result = $this->userModel->getDataTablesData(
            $start, $length, $searchValue, $orderByColumn, $orderDir
        );
        
        // Prepare response for DataTables
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $result['totalRecords'],
            'recordsFiltered' => $result['filteredRecords'],
            'data' => []
        ];
        
        // Format data for DataTables
        foreach ($result['data'] as $user) {
            // Format for each row
            $row = [
                $user['id'],
                htmlspecialchars($user['first_name'] . ' ' . $user['last_name']),
                htmlspecialchars($user['email']),
                '<span class="badge ' . ($user['role_name'] === 'admin' ? 'bg-danger' : 'bg-primary') . '">' . 
                    htmlspecialchars($user['role_name']) . '</span>',
                '<span class="badge ' . ($user['is_active'] ? 'bg-success' : 'bg-secondary') . '">' . 
                    ($user['is_active'] ? 'Active' : 'Inactive') . '</span>',
                $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never',
                '<div class="btn-group btn-group-sm">
                    <a href="#" class="btn btn-outline-secondary"><i class="bi bi-eye"></i></a>
                    <a href="#" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" class="btn btn-outline-danger"><i class="bi bi-trash"></i></a>
                </div>'
            ];
            
            $response['data'][] = $row;
        }
        
        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Regular page load - just render the view
    $this->render('test/paginate-test', [
        'pagination' => [
            'total' => $this->userModel->count()
        ]
    ]);
}
}