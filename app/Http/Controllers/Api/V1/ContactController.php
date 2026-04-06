<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $contacts = Contact::where('is_active', true)
            ->orderBy('order')
            ->get();
            
        return $this->success($contacts);
    }
}
