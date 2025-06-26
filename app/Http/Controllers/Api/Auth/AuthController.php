<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use App\Traits\SiteTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    use SiteTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthRequest $request)
    {
        try {
            $data =  $request->validated();
            User::create($data);
            return $this->JsonResponse('User created successfully', 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
