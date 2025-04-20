<?php

namespace App\Controllers;

class DataTableController extends BaseController
{
/**
 * Process DataTables server-side request
 * 
 * @param string $modelName The model to use for data retrieval
 * @return void
 */
public function process($modelName)
{
    // Check if it's an AJAX request
    if (!$this->isAjax()) {
        $this->jsonError('Invalid request', 400);
    }
    
    try {
        // Load the appropriate model
        $model = $this->loadModel($modelName);
        
        // Get raw request data
        $requestData = $this->request();
        
        // Get DataTables parameters
        $draw = isset($requestData['draw']) ? intval($requestData['draw']) : 1;
        $start = isset($requestData['start']) ? intval($requestData['start']) : 0;
        $length = isset($requestData['length']) ? intval($requestData['length']) : 10;
        
        // Get search value
        $search = '';
        if (isset($requestData['search']) && isset($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
        }
        
        // Get order column and direction
        $orderColumn = 'id'; // Default column name
        $orderDir = 'asc';   // Default direction
        
        if (isset($requestData['order']) && isset($requestData['order'][0])) {
            $orderColumnIdx = isset($requestData['order'][0]['column']) ? intval($requestData['order'][0]['column']) : 0;
            $orderDir = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'asc';
            
            // Get the column name from the columns data or name
            if (isset($requestData['columns']) && isset($requestData['columns'][$orderColumnIdx])) {
                // Try to get data attribute first, then name if data is empty
                if (!empty($requestData['columns'][$orderColumnIdx]['data'])) {
                    $orderColumn = $requestData['columns'][$orderColumnIdx]['data'];
                } elseif (!empty($requestData['columns'][$orderColumnIdx]['name'])) {
                    $orderColumn = $requestData['columns'][$orderColumnIdx]['name'];
                }
            }
        }
        
        // Get total records count
        $totalRecords = $model->countData();
        
        // Get filtered records count
        $filteredRecords = $model->countFilteredData($search);
        
        // Get data with filtering, ordering and pagination
        $data = $model->getDataTableData($start, $length, $search, $orderColumn, $orderDir);
        
        // Format response in DataTables expected format
        $response = [
            'draw' => $draw,
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'data' => $data
        ];
        
        // Debug: Log the response being sent
        // error_log('DataTables Response: ' . json_encode($response));
        
        $this->json($response);
        
    } catch (\Exception $e) {
        // Log the error
        error_log('DataTables Error: ' . $e->getMessage());
        $this->jsonError('Error processing request: ' . $e->getMessage(), 500);
    }
}
    
    /**
     * Handle record creation
     */
    public function create($modelName) 
    {
        if (!$this->isPost()) {
            $this->jsonError('Invalid request method', 405);
        }
        
        try {
            $model = $this->loadModel($modelName);
            $data = $this->isAjax() ? $this->getJsonInput() : $this->request();
            
            $result = $model->createData($data);
            
            if ($result) {
                $this->jsonSuccess(['id' => $result], 'Record created successfully');
            } else {
                $this->jsonError('Failed to create record');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Handle record update
     */
    public function update($modelName) 
    {
        if (!$this->isPost()) {
            $this->jsonError('Invalid request method', 405);
        }
        
        try {
            $model = $this->loadModel($modelName);
            $data = $this->isAjax() ? $this->getJsonInput() : $this->request();
            
            if (empty($data['id'])) {
                $this->jsonError('ID is required', 400);
            }
            
            $result = $model->updateData($data['id'], $data);
            
            if ($result) {
                $this->jsonSuccess([], 'Record updated successfully');
            } else {
                $this->jsonError('Failed to update record');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Handle record deletion
     */
    public function delete($modelName) 
    {
        if (!$this->isPost()) {
            $this->jsonError('Invalid request method', 405);
        }
        
        try {
            $model = $this->loadModel($modelName);
            $id = $this->request('id');
            
            if (empty($id)) {
                $this->jsonError('ID is required', 400);
            }
            
            $result = $model->deleteData($id);
            
            if ($result) {
                $this->jsonSuccess([], 'Record deleted successfully');
            } else {
                $this->jsonError('Failed to delete record');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
}