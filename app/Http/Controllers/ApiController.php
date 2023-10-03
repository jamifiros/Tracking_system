<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Destination;
use Laravel\Sanctum\PersonalAccessTokenResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{

public function login(Request $request)
{
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Invalid login'
        ], 401);
    }

    $user = User::where('email', $request['email'])->firstOrFail();

    $token = $user->createToken('auth_token')->plainTextToken;
    
    if ($user->profile_image !== null) {
        $imagePath = asset('storage/' . $user->profile_image);
    } else {
        $imagePath = null;
    }

    return response()->json([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'contact_no' => $user->contact_no,
        'image_url' =>$imagePath,
        'access_token' => $token,
        
    ]);
}

public function getlist($id, Request $request)
{
    $user = User::with('destination')->find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Get the scheduled date from the request, or default to the current date
    $scheduledDate = $request->input('scheduled_date');
  

    if (!$scheduledDate) {
        // No scheduled date provided, use the current date
        $scheduledDate = Carbon::now()->format('Y-m-d');
    }

    // Filter destinations based on the scheduled date
    $filteredDestinations = $user->destination->filter(function ($destination) use ($scheduledDate) {
        return $destination->scheduled_date == $scheduledDate;
    });

    $destinations = $filteredDestinations->map(function ($destination) {
        return [
            'destination_id' => $destination->id,
            'name' => $destination->destName,
            'contact_number' => $destination->contactNo,
            'location' => $destination->Location,
            'scheduled_time' => $destination->scheduled_time,
            'status'=> $destination->status,
            'visited_date'=>$destination->visited_date
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
    $id = $request->input('destination_id');
    $destination = Visit::where('destination_id', $id)->first();
  
    if (is_null($destination)) {
        $destImgPath = $request->file('dest_img')->store('destinationImages', 'public');
        $meterImgPath = $request->file('meter_img')->store('meterImages', 'public');
        
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
            'visited_date' => now(),
        ]);

        return response()->json(['message' => 'submitted successfully']); 
    }
else{
    return response()->json(['message' => 'visit already exists..!']); 
}  
}


public function show($id)
{
     $destination = Destination::find($id);
     if (!$destination) {
         return response()->json(['message' => 'destination not found'], 404);
     }
    
     $visit =  $destination->visit;
    if (!$visit) {
        return response()->json(['error' => 'Visit not found'], 404);
    }

    $data = [
        'visit_id' => $visit->id,
        'remarks' => $visit->remarks,
        'dest_img'  => asset('/'.'storage/' . $visit->dest_img),
        'meter_img' => asset('/'.'storage/' . $visit->meter_img)
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


// public function visitlist($id) {
   
//     $user = User::find($id);
//     if (!$user) {
//         return response()->json(['message' => 'User not found'], 404);
//     }

//     $Destinations = $user->destination()->where('status', 1)->get();
//     return response()->json($Destinations);
// }


public function addlist(Request $request,$id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validatedData = $request->validate([
        'destName' => 'required|string',
        'contactNo' => 'required|string',
        'Location' => 'required|string',
        'scheduled_date' =>'required|date',
        'scheduled_time' => 'required|date_format:H:i',
    ]);

Destination::create([
    'user_id' => $user->id,
    'destName' => request('destName'),
    'contactNo' => request('contactNo'),
    'Location' => request('Location'),
    'scheduled_date' => request('scheduled_date'),
    'scheduled_time' => request('scheduled_time')
]);
    return response()->json(['message' => 'Destination created successfully'], 201);
}


public function password(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validatedData = $request->validate([
        'password' => 'required|string',
        'new_password' => 'required|string', 
    ]);

    // Verify the current password
    if (!Hash::check($validatedData['password'], $user->password)) {
        return response()->json(['message' => 'Current password is incorrect'], 400);
    }

    try {
        // Update the user's password with the new password
        $user->password = Hash::make($validatedData['new_password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Update failed'], 500); 
}

}


public function changeprofile(Request $request, $id)
    {
         // Find the user by ID
         $user = User::find($id);

         if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
          }
        if ($request->hasFile('profile_image')) {
            // Validate the request
             $request->validate([
                'profile_image' => 'image|mimes:jpeg,jpg,png', // Adjust allowed image formats
             ]);

              try {
                // Delete the old profile image (if it exists)
                if ($user->profile_image) {
                    Storage::delete('public/' . $user->profile_image);
                }
             $profileImgPath = $request->file('profile_image')->store('profileImages', 'public');

            $user->update([
               'profile_image' => $profileImgPath   
             ]);
             return response()->json(['message' => 'Profile image updated successfully'],200);
            }
             catch (\Exception $e) {
                return response()->json(['message' => 'Profile image update failed'], 500);
             }
            } else {
                return response()->json(['message' => 'No image uploaded'], 400);
            }
    }

public function showprofile($id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->profile_image !== null) {
            $imagePath = asset('storage/' . $user->profile_image);
        } else {
            $imagePath = null;
        }
        return response()->json([
            'image_url' =>$imagePath,
        ]);
    }
}



    


