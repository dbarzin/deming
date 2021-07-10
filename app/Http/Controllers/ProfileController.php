<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('auth.profile');
    }

    public function avatar(int $id)
    {    
        $user = User::findOrFail($id);
        $image = Storage::download($user->profile_image);
        return $image;
    }

    public function updateProfile(Request $request)
    {
        // Form validation
        $request->validate(
            [           
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]
        );

        // Get current user
        $user = User::findOrFail(auth()->user()->id);
        // Set user name
        // $user->name = $request->input('name');

        // Check if a profile image has been uploaded
        if($request->hasFile('profile_image')){
            $filename = $request->profile_image->getClientOriginalName();
            $request->profile_image->storeAs('avatar',auth()->user()->id,'public');
            // Auth()->user()->update(['profile_image'=>1]);
            $user->profile_image = 1;
            $user->update();
        }

        // Return user back and show a flash message
        return redirect()->back()->with(['status' => 'Profile updated successfully.']);
    }
}

?>
