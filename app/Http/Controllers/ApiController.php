<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Destination;
use Laravel\Sanctum\PersonalAccessTokenResult;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Visit;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{


public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'user_id' => $user->id,
            'token' => $token]);
    }
    throw ValidationException::withMessages([
        'email' => 'Invalid credentials',
    ]);
}



public function getlist($id)
{
    $user = User::with('destination')->find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $destinations = $user->destination->map(function ($destination) {
        return [
            'name' => $destination->destName,
            'contact_number' => $destination->contactNo,
            'location' => $destination->Location,
        ];
    });

    return response()->json(['destinations' => $destinations]);
}



public function logout(Request $request)
{
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out successfully']);
}


public function store(Request $request)
{
    $input = $request->all();
    $validator = Validator::make($input, [
        'visit_id' => 'required|exists:destinations,id',
        'user_id' => 'required|exists:destinations,user_id',
        'lattitude' => 'required',
        'longitude' => 'required',
        'remarks' => 'string',
        'dest_img' => 'image|mimes:jpeg,jpg,', 
        'meter_img' => 'image|mimes:jpeg,jpg,', 
    ]);

    if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
    }
    
    $destImgPath = $request->file('dest_img')->store('public/images/destinationImages');
    $meterImgPath = $request->file('meter_img')->store('public/images/meterImages');

    $visit = Visit::create([
        'visit_id'=> request('visit_id'),
        'user_id'=> request('user_id'),
        'lattitude'=> request('lattitude'),
        'longitude'=> request('longitude'),
        'remarks'=> request('remarks'),
        'dest_img' => $destImgPath,
        'meter_img' =>$meterImgPath
    ]);
    $destination = Destination::find($visit->visit_id);
    $destination->update([
        'status' => 1,
        'visited' => now(),
    ]);
    return response()->json(['message' => 'submitted successfully']);

   
}


public function show($id)
{
    
    $visit = Visit::find($id);

    if (!$visit) {
        return response()->json(['error' => 'Visit not found'], 404);
    }

    $data = [
        'remarks' => $visit->remarks,
        'dest_img' => $visit->dest_img,
        'meter_img' => $visit->meter_img,
    ];
    return response()->json($data);
}


public function update(Request $request)
{
    $input = $request->all();

    $validator = Validator::make($input, [
        'visit_id' => 'required|exists:destinations,id',
    ]);

    if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
    }

    $visit = Visit::find($id);
    $visit->update([
        'remarks' => request('remarks')
    ]);
    
    return response()->json(['message' => 'Remarks updated successfully']);
    
}

}