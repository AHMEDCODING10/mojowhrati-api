<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Material;

class MaterialController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index()
    {
        $materials = Material::all();
        return $this->success($materials);
    }
}
