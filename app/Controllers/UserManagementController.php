<?php

namespace App\Controllers;

class UserManagementController extends BaseController
{
    protected $userModel;

    public function __construct() 
    {
        $this->userModel = $this->loadModel('UserModel');
    }
    
    /**
     * Process DataTables server-side request for users data
     * 
     * @return void
     */
    public function getData()
    {
        // Delegate to DataTableController
        $dataTableController = new DataTableController();
        $dataTableController->process('UserModel');
    }
    
    /**
     * Create a new user
     * 
     * @return void
     */
    public function create()
    {
        // Delegate to DataTableController
        $dataTableController = new DataTableController();
        $dataTableController->create('UserModel');
    }
    
    /**
     * Update a user
     * 
     * @return void
     */
    public function update()
    {
        // Delegate to DataTableController
        $dataTableController = new DataTableController();
        $dataTableController->update('UserModel');
    }
    
    /**
     * Delete a user
     * 
     * @return void
     */
    public function delete()
    {
        // Delegate to DataTableController
        $dataTableController = new DataTableController();
        $dataTableController->delete('UserModel');
    }
    
}