<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBusinessRequest;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function store(CreateBusinessRequest $request)
    {
        $business = Business::create($request->validated());
        
        // Associate the current user with the business
        Auth::user()->update(['business_id' => $business->id]);

        return response()->json([
            'message' => 'Business created successfully',
            'data' => $business
        ], 201);
    }
}