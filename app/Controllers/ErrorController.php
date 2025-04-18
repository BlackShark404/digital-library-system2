<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ErrorController extends BaseController
{
    public function error404()
    {
        $this->renderError("The page you're looking for doesn't exist or has been moved.", 404);
    }

    public function error403()
    {
        $this->renderError("You don't have permission to access this resource.", 403);
    }

    public function error500()
    {
        $this->renderError("Something went wrong on our end. We're working to fix it.");
    }
}
