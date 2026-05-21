<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class staffcontroller extends Controller
{
    /**
     * Add Staff API
     */
    public function addStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6',
            'role'     => 'required|in:telecaller,receptionist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            // 'password' => Hash::make($request->password),
            'password' => $request->password,
            'added_by' => auth()->user()->id, // Set the added_by field to the ID of the authenticated admin
        ]);

        // Generate JWT Token

        return response()->json([
            'status'  => true,
            'message' => 'Staff Registered Successfully',

            'data'    => $user,
        ], 201);
    }

    /**
     * List Staff API
     */
    public function staffList(Request $request)
    {
        $query = User::query();

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('role', 'LIKE', "%{$search}%");

            });
        }

        /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('status')) {

            if ($request->status == 'active') {

                $query->where('status', 1);

            } elseif ($request->status == 'inactive') {

                $query->where('status', 0);

            }
        }

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $perPage = $request->per_page ?? 10;

        $staffs = $query->latest()->paginate($perPage);

        /*
    |--------------------------------------------------------------------------
    | Counts
    |--------------------------------------------------------------------------
    */

        $counts = [

            'total'    => User::count(),

            'active'   => User::where('status', 1)->count(),

            'inactive' => User::where('status', 0)->count(),

            'roles'    => [

                [
                    'role'  => 'admin',
                    'count' => User::where('role', 'admin')->count(),
                ],

                [
                    'role'  => 'drafting_team',
                    'count' => User::where('role', 'drafting_team')->count(),
                ],

                [
                    'role'  => 'pa',
                    'count' => User::where('role', 'pa')->count(),
                ],

                [
                    'role'  => 'receptionist',
                    'count' => User::where('role', 'receptionist')->count(),
                ],

                [
                    'role'  => 'telecaller',
                    'count' => User::where('role', 'telecaller')->count(),
                ],

            ],

        ];

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'status'     => true,

            'message'    => 'Staff List Fetch Successfully',

            'counts'     => $counts,

            'pagination' => [

                'current_page' => $staffs->currentPage(),

                'last_page'    => $staffs->lastPage(),

                'per_page'     => $staffs->perPage(),

                'total'        => $staffs->total(),

                'from'         => $staffs->firstItem(),

                'to'           => $staffs->lastItem(),

            ],

            'data'       => collect($staffs->items())->map(function ($staff) {

                return [

                    'id'          => $staff->id,

                    'role'        => $staff->role,

                    'name'        => $staff->name,

                    'username'    => $staff->phone,

                    'email'       => $staff->email,

                    'avatar'      => $staff->avatar,

                    'status'      => $staff->status,

                    'status_text' => $staff->status == 1 ? 'active' : 'inactive',

                    'is_me'       => auth()->id() == $staff->id ? true : false,

                    'added_by'    => $staff->added_by,

                    'created_at'  => date('d M Y', strtotime($staff->created_at)),

                ];

            }),

        ]);
    }

    /**
     * Update Staff API
     */

    public function updateStaff(Request $request, $id)
    {
        $staff = User::find($id);

        if (! $staff) {

            return response()->json([
                'status'  => false,
                'message' => 'Staff Not Found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [

            'role'     => 'required',
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,' . $id,
            'phone'    => 'required|digits:10|unique:users,phone,' . $id,
            'password' => 'nullable|min:6',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $updateData = [

            'role'  => $request->role,

            'name'  => $request->name,

            'email' => $request->email,

            'phone' => $request->phone,

        ];

        // UPDATE PASSWORD ONLY IF SENT
        if ($request->filled('password')) {

            // $updateData['password'] = bcrypt($request->password);
            $updateData['password'] = $request->password;
        }

        $staff->update($updateData);

        return response()->json([

            'status'  => true,

            'message' => 'Staff Updated Successfully',

            'data'    => $staff,

        ]);
    }

    /**
     * Delete Staff API
     */

    public function deleteStaff($id)
    {
        $staff = User::find($id);

        if (! $staff) {

            return response()->json([
                'status'  => false,
                'message' => 'Staff Not Found',
            ], 404);
        }

        $staff->delete();

        return response()->json([

            'status'  => true,

            'message' => 'Staff Deleted Successfully',

        ]);
    }

    /**
     * Staff Details By Id API
     */

    public function staffDetails($id)
    {
        $staff = User::find($id);

        if (! $staff) {

            return response()->json([
                'status'  => false,
                'message' => 'Staff Not Found',
            ], 404);
        }

        return response()->json([

            'status'  => true,

            'message' => 'Staff Details Fetch Successfully',

            'data'    => [

                'id'          => $staff->id,

                'role'        => $staff->role,

                'name'        => $staff->name,

                'email'       => $staff->email,

                'phone'       => $staff->phone,

                'avatar'      => $staff->avatar,

                'status'      => $staff->status,

                'status_text' => $staff->status == 1
                    ? 'active'
                    : 'inactive',

                'added_by'    => $staff->added_by,

                'created_at'  => date('d M Y h:i A', strtotime($staff->created_at)),

            ],

        ]);
    }

    /**
     * Toggle Staff Status API
     */
    public function toggleStatus($id)
    {
        $staff = User::find($id);

        if (! $staff) {

            return response()->json([

                'status'  => false,

                'message' => 'Staff Not Found',

            ], 404);
        }

        // Toggle Status
        $staff->status = $staff->status == 1 ? 0 : 1;

        $staff->save();

        return response()->json([

            'status'  => true,

            'message' => $staff->status == 1
                ? 'Staff Activated Successfully'
                : 'Staff Deactivated Successfully',

            'data'    => [

                'id'             => $staff->id,

                'current_status' => $staff->status,

                'status_text'    => $staff->status == 1
                    ? 'active'
                    : 'inactive',

            ],

        ]);
    }
}
