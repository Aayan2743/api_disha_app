<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WebpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name'   => 'required|string|max:255',

            'email'  => 'required|email|unique:users,email,' . auth()->id(),

            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        $user = User::find(auth()->id());

        /*
        |--------------------------------------------------------------------------
        | Upload Avatar
        |--------------------------------------------------------------------------
        */

        $avatar = $user->avatar;

        if ($request->hasFile('avatar')) {

            // Delete Old Image
            if ($user->avatar && file_exists(public_path($user->avatar))) {

                unlink(public_path($user->avatar));
            }

            $file = $request->file('avatar');

            $fileName = time() . '.webp';

            $destinationPath = public_path('uploads/profile');

            // Create Folder
            if (! file_exists($destinationPath)) {

                mkdir($destinationPath, 0755, true);
            }

            // Temp Upload
            $tempPath = $file->getRealPath();

            $webpPath = $destinationPath . '/' . $fileName;

            // Convert to WebP
            WebpService::convert(

                $tempPath,
                $webpPath,
                60,
                300,
                300

            );

            $avatar = 'uploads/profile/' . $fileName;
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $user->update([

            'name'   => $request->name,

            'email'  => $request->email,

            'avatar' => $avatar,

        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Profile Updated Successfully',

            'data'    => [

                'id'     => $user->id,

                'name'   => $user->name,

                'email'  => $user->email,

                'avatar' => $user->avatar
                    ? asset($user->avatar)
                    : null,

            ],

        ]);
    }
    /**
     * Change Password
     */

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'password' => 'required|min:6|confirmed',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        $user = User::find(auth()->id());

        $user->update([

            'password' => bcrypt($request->password),

        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Password Updated Successfully',

        ]);
    }
}