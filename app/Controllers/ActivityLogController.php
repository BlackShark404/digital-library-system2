<?php

namespace App\Controllers;

class ActivityLogController extends BaseController
{
    /**
     * Display the activity log page
     */
    public function index()
    {
        // Check if user has admin permission
        if (!$this->checkPermission('admin')) {
            $this->redirect('/error/403');
        }
        
        // Load the activity log model
        $model = $this->loadModel('ActivityLogModel');
        
        // Get unique action types for the filter dropdown
        $unique_actions = $model->getUniqueActionTypes();
        
        // Get the action filter from request if any
        $action_filter = $this->getRequestParam('action', '');
        
        // Prepare view data
        $data = [
            'unique_actions' => $unique_actions,
            'action_filter' => $action_filter
        ];
        
        // Render the view
        $this->render('admin/activity-log', $data);
    }
    
    /**
     * Get activity logs data for DataTables
     */
    public function getActivityLogs()
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Get action filter if any
        $actionFilter = $this->getRequestParam('action', '');
        
        // Create a custom data processor for the model to handle our filters
        $dataProcessor = function($model, $requestData) use ($actionFilter) {
            // Standard DataTables parameters
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
                
                // Get the column name from the columns data
                if (isset($requestData['columns']) && isset($requestData['columns'][$orderColumnIdx])) {
                    if (!empty($requestData['columns'][$orderColumnIdx]['data'])) {
                        $orderColumn = $requestData['columns'][$orderColumnIdx]['data'];
                    } elseif (!empty($requestData['columns'][$orderColumnIdx]['name'])) {
                        $orderColumn = $requestData['columns'][$orderColumnIdx]['name'];
                    }
                }
            }
            
            // Override action filter if present in request
            $action = isset($requestData['action']) ? $requestData['action'] : $actionFilter;
            
            // Get total records count
            $totalRecords = $model->countData();
            
            // Get filtered records count
            $filteredRecords = $model->countFilteredData($search, $action);
            
            // Get data with filtering, ordering and pagination
            $data = $model->getDataTableData($start, $length, $search, $orderColumn, $orderDir, $action);
            
            return [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];
        };
        
        // Process the DataTables request using our custom processor
        $model = $this->loadModel('ActivityLogModel');
        $requestData = $this->request();
        
        $response = $dataProcessor($model, $requestData);
        $this->json($response);
    }
    
    /**
     * View details of a specific activity log
     * 
     * @param int $id Activity log ID
     */
    public function viewLog($id)
    {
        // Check if user has admin permission
        if (!$this->checkPermission('admin')) {
            $this->jsonError('Permission denied', 403);
        }
        
        // Load the activity log model
        $model = $this->loadModel('ActivityLogModel');
        
        // Get log details
        $log = $model->getLogById($id);
        
        if (!$log) {
            $this->jsonError('Log record not found', 404);
        }
        
        // Return log details
        $this->jsonSuccess($log);
    }
    
    /**
     * Get activity statistics for dashboard
     */
    public function getActivityStats()
    {
        // Check if user has admin permission
        if (!$this->checkPermission('admin')) {
            $this->jsonError('Permission denied', 403);
        }
        
        // Load the activity log model
        $model = $this->loadModel('ActivityLogModel');
        
        // Get activity statistics
        $stats = $model->getActivityStats();
        
        // Return statistics
        $this->jsonSuccess($stats);
    }
    
    /**
     * Create a new activity log
     * This can be called programmatically from other controllers
     * 
     * @param int|null $userId User ID or null for system actions
     * @param string $actionCode Activity type code
     * @param string $description Description of the activity
     * @return bool Success or failure
     */
    public static function logActivity($userId, $actionCode, $description)
    {
        try {
            // Create a temporary instance of the controller
            $controller = new self();
            
            // Load the model
            $model = $controller->loadModel('ActivityLogModel');
            
            // Get the activity type ID from the code
            $db = $controller->getDb();
            $stmt = $db->prepare("SELECT at_id FROM activity_type WHERE at_code = ?");
            $stmt->execute([$actionCode]);
            $actionTypeId = $stmt->fetchColumn();
            
            if (!$actionTypeId) {
                throw new \Exception("Invalid activity type code: $actionCode");
            }
            
            // Create the log entry
            $logData = [
                'user_id' => $userId,
                'action_type_id' => $actionTypeId,
                'description' => $description
            ];
            
            return $model->createData($logData);
        } catch (\Exception $e) {
            error_log("Error logging activity: " . $e->getMessage());
            return false;
        }
    }
} 