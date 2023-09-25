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
            'name' => $user->name,
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
        'destination_id' => 'required|exists:destinations,id',
        'user_id' => 'required|exists:users,id',
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
        'destination_id'=> request('destination_id'),
        'user_id'=> request('user_id'),
        'lattitude'=> request('lattitude'),
        'longitude'=> request('longitude'),
        'remarks'=> request('remarks'),
        'dest_img' => $destImgPath,
        'meter_img' =>$meterImgPath
    ]);
    $destination = Destination::find($visit->destination_id);
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



public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'remarks' => 'required|string',
    ]);

    try {
       
        $visit = Visit::find($id);
        $visit->remarks = $validatedData['remarks'];
        $visit->save();
 
        return response()->json(['message' => 'updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'update failed'], 404);
    }
}


public function visitlist($id) {
   
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $Destinations = $user->destination()->where('status', 1)->get();
    return response()->json($Destinations);
}


// public function addlist(Request $request, $id)
// {
//     // Validate the incoming request data
//     $validatedData = $request->validate([
//         'destName' => 'required|string',
//         'contactNo' => 'required|string',
//         'Location' => 'required|string',
//     ]);

//     // Check if the user exists
//     $user = User::find($id);

//     if (!$user) {
//         return response()->json(['message' => 'User not found'], 404);
//     }
//     $destination->user_id = $id; 
//     $destination = new Destination([
//         'destName' => $validatedData['destName'],
//         'contactNo' => $validatedData['contactNo'],
//         'Location' => $validatedData['Location'],
//     ]);

//     // Associate the destination with the user
    
//     $destination->save();

//     return response()->json(['message' => 'Destination added successfully'], 201);
// }

}

