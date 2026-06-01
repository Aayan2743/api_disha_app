<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Punch In / Punch Out
     */
    public function attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'latitude'  => 'required',

            'longitude' => 'required',

            'address'   => 'nullable|string',

            'type'      => 'required|in:punch_in,punch_out',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        $userId = auth()->id();

        $today = date('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Punch In
        |--------------------------------------------------------------------------
        */

        if ($request->type == 'punch_in') {

            // Already Punched In
            $already = Attendance::where('user_id', $userId)
                ->whereDate('attendance_date', $today)
                ->first();

            if ($already) {

                return response()->json([

                    'status'  => false,

                    'message' => 'Already Punched In Today',

                ], 422);
            }

            $attendance = Attendance::create([

                'user_id'            => $userId,

                'attendance_date'    => $today,

                'punch_in'           => now(),

                'punch_in_latitude'  => $request->latitude,

                'punch_in_longitude' => $request->longitude,

                'punch_in_address'   => $request->address,

            ]);

            return response()->json([

                'status'  => true,

                'message' => 'Punch In Successfully',

                'data'    => $attendance,

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Punch Out
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('attendance_date', $today)
            ->first();

        if (! $attendance) {

            return response()->json([

                'status'  => false,

                'message' => 'Please Punch In First',

            ], 422);
        }

        if ($attendance->punch_out) {

            return response()->json([

                'status'  => false,

                'message' => 'Already Punched Out',

            ], 422);
        }

        $punchIn = Carbon::parse($attendance->punch_in);

        $punchOut = now();

        $totalMinutes = $punchIn->diffInMinutes($punchOut);

        $attendance->update([

            'punch_out'           => $punchOut,

            'punch_out_latitude'  => $request->latitude,

            'punch_out_longitude' => $request->longitude,

            'punch_out_address'   => $request->address,

            'total_minutes'       => $totalMinutes,

        ]);

        return response()->json([

            'status'      => true,

            'message'     => 'Punch Out Successfully',

            'total_hours' => floor($totalMinutes / 60) . 'h ' . ($totalMinutes % 60) . 'm',

            'data'        => $attendance,

        ]);
    }

    public function attendanceDetails(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Default Current Date
    |--------------------------------------------------------------------------
    */

        $date = $request->date ?? date('Y-m-d');

        $userId = auth()->id();

        /*
    |--------------------------------------------------------------------------
    | Attendance Record
    |--------------------------------------------------------------------------
    */

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('attendance_date', $date)
            ->first();

        /*
    |--------------------------------------------------------------------------
    | Month & Year
    |--------------------------------------------------------------------------
    */

        $month = date('m', strtotime($date));

        $year = date('Y', strtotime($date));

        /*
    |--------------------------------------------------------------------------
    | Present Count
    |--------------------------------------------------------------------------
    */

        $presentCount = Attendance::where('user_id', $userId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Holiday Count (Sunday)
    |--------------------------------------------------------------------------
    */

        $holidayCount = 0;

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $daysInMonth; $day++) {

            $currentDate = $year . '-' . $month . '-' . $day;

            if (date('w', strtotime($currentDate)) == 0) {

                $holidayCount++;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Leave Count
    |--------------------------------------------------------------------------
    */

        $leaveCount = 0;

        /*
    |--------------------------------------------------------------------------
    | Absent Count
    |--------------------------------------------------------------------------
    */

        $currentDay = date('d');

        $workingDays = 0;

        for ($day = 1; $day <= $currentDay; $day++) {

            $currentDate = $year . '-' . $month . '-' . $day;

            // Skip Sundays
            if (date('w', strtotime($currentDate)) != 0) {

                $workingDays++;
            }
        }

        $absentCount = $workingDays - $presentCount - $leaveCount;

        if ($absentCount < 0) {

            $absentCount = 0;
        }

        /*
    |--------------------------------------------------------------------------
    | No Attendance
    |--------------------------------------------------------------------------
    */

        if (! $attendance) {

            return response()->json([

                'status'        => true,

                'selected_date' => date('l, d F Y', strtotime($date)),

                'summary'       => [

                    'present' => $presentCount,

                    'absent'  => $absentCount,

                    'leave'   => $leaveCount,

                    'holiday' => $holidayCount,

                ],

                'attendance'    => null,

                'message'       => 'No Attendance Record Found',

            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Total Duration
    |--------------------------------------------------------------------------
    */

        $hours = floor($attendance->total_minutes / 60);

        $minutes = $attendance->total_minutes % 60;

        $totalDuration = $hours . 'h ' . $minutes . 'm';

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'status'        => true,

            'selected_date' => date('l, d F Y', strtotime($date)),

            'summary'       => [

                'present' => $presentCount,

                'absent'  => $absentCount,

                'leave'   => $leaveCount,

                'holiday' => $holidayCount,

            ],

            'attendance'    => [

                'attendance_date'    => $attendance->attendance_date,

                'status'             => $attendance->punch_out
                    ? 'Present'
                    : 'Punch In Only',

                'punch_in_time'      => $attendance->punch_in
                    ? date('h:i:s A', strtotime($attendance->punch_in))
                    : null,

                'punch_out_time'     => $attendance->punch_out
                    ? date('h:i:s A', strtotime($attendance->punch_out))
                    : null,

                'total_duration'     => $totalDuration,

                'total_minutes'      => $attendance->total_minutes,

                'punch_in_location'  => [

                    'latitude'  => $attendance->punch_in_latitude,

                    'longitude' => $attendance->punch_in_longitude,

                    'address'   => $attendance->punch_in_address,

                ],

                'punch_out_location' => [

                    'latitude'  => $attendance->punch_out_latitude,

                    'longitude' => $attendance->punch_out_longitude,

                    'address'   => $attendance->punch_out_address,

                ],

            ],

        ]);
    }

    public function punchIn(Request $request)
    {
        $today = date('Y-m-d');

        $attendance = Attendance::where(
            'user_id',
            auth()->id()
        )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        // ALREADY PUNCHED IN
        if ($attendance && $attendance->punch_in) {

            return response()->json([

                'status'  => false,

                'message' => 'Already punched in today',
            ]);
        }

        // CREATE
        if (! $attendance) {

            $attendance = Attendance::create([

                'user_id'         => auth()->id(),

                'attendance_date' => $today,
            ]);
        }

        $attendance->update([

            'punch_in'           => now(),

            'punch_in_latitude'  => $request->latitude,

            'punch_in_longitude' => $request->longitude,

            'punch_in_address'   => $request->address,
        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Punch in successful',

            'data'    => [

                'punch_in' => Carbon::parse(
                    $attendance->punch_in
                )->format('h:i:s A'),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PUNCH OUT
    |--------------------------------------------------------------------------
    */

    public function punchOut(Request $request)
    {
        $today = date('Y-m-d');

        $attendance = Attendance::where(
            'user_id',
            auth()->id()
        )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        if (! $attendance || ! $attendance->punch_in) {

            return response()->json([

                'status'  => false,

                'message' => 'Please punch in first',
            ]);
        }

        if ($attendance->punch_out) {

            return response()->json([

                'status'  => false,

                'message' => 'Already punched out',
            ]);
        }

        $punchIn = Carbon::parse(
            $attendance->punch_in
        );

        $punchOut = now();

        $minutes = $punchIn->diffInMinutes(
            $punchOut
        );

        $attendance->update([

            'punch_out'           => $punchOut,

            'punch_out_latitude'  => $request->latitude,

            'punch_out_longitude' => $request->longitude,

            'punch_out_address'   => $request->address,

            'total_minutes'       => $minutes,
        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Punch out successful',

            'data'    => [

                'punch_out'     => Carbon::parse(
                    $attendance->punch_out
                )->format('h:i:s A'),

                'total_minutes' => $minutes,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TODAY SESSION
    |--------------------------------------------------------------------------
    */

    public function todaySession()
    {
        $today = date('Y-m-d');

        $attendance = Attendance::where(
            'user_id',
            auth()->id()
        )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        // NO ATTENDANCE
        if (! $attendance) {

            return response()->json([

                'status'  => true,

                'message' => 'No attendance found',

                'data'    => [

                    'status'       => 'OFF DUTY',

                    'punch_in'     => null,

                    'punch_out'    => null,

                    'elapsed'      => null,

                    'activity_log' => [],
                ],
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | ELAPSED TIME
    |--------------------------------------------------------------------------
    */

        $elapsed = null;

        if ($attendance->punch_in) {

            $start = Carbon::parse(
                $attendance->punch_in
            );

            // IF PUNCHED OUT
            if ($attendance->punch_out) {

                $minutes = $attendance->total_minutes;

            } else {

                // LIVE RUNNING TIME
                $minutes = $start->diffInMinutes(now());
            }

            $hrs = floor($minutes / 60);

            $mins = $minutes % 60;

            $elapsed = $hrs . 'h ' . $mins . 'm';
        }

        /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOG
    |--------------------------------------------------------------------------
    */

        $activity = [];

        // PUNCH IN
        if ($attendance->punch_in) {

            $activity[] = [

                'type' => 'In',

                'time' => Carbon::parse(
                    $attendance->punch_in
                )->format('H:i:s'),
            ];
        }

        // PUNCH OUT
        if ($attendance->punch_out) {

            $activity[] = [

                'type' => 'Out',

                'time' => Carbon::parse(
                    $attendance->punch_out
                )->format('H:i:s'),
            ];
        }

        return response()->json([

            'status'  => true,

            'message' => 'Session fetched successfully',

            'data'    => [

                'status'       => $attendance->punch_out
                    ? 'OFF DUTY'
                    : 'ON DUTY',

                'punch_in'     => $attendance->punch_in
                    ? Carbon::parse(
                    $attendance->punch_in
                )->format('H:i:s')
                    : null,

                'punch_out'    => $attendance->punch_out
                    ? Carbon::parse(
                    $attendance->punch_out
                )->format('H:i:s')
                    : null,

                'elapsed'      => $elapsed,

                'activity_log' => $activity,
            ],
        ]);
    }

    public function staffAttendance(Request $request)
    {
        // VALIDATION
        $validator = validator($request->all(), [

            'date' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        // DATE
        $date = $request->filled('date')
            ? $request->date
            : date('Y-m-d');

        // ALL STAFF
        $users = User::whereIn('role', [

            'admin',
            'receptionist',
            'telecaller',

        ])->get();

        // ATTENDANCE
        $attendance = Attendance::whereDate(
            'attendance_date',
            $date
        )
            ->get()
            ->keyBy('user_id');

        $presentCount = 0;
        $lateCount    = 0;
        $absentCount  = 0;

        $data = $users->map(function ($user) use (

            $attendance,
            &$presentCount,
            &$lateCount,
            &$absentCount

        ) {

            $att = $attendance[$user->id] ?? null;

            // ABSENT
            if (! $att) {

                $absentCount++;

                return [

                    'user_id'     => $user->id,

                    'name'        => $user->name,

                    'phone'       => $user->phone,

                    'role'        => ucfirst($user->role),

                    'status'      => 'absent',

                    'login_time'  => null,

                    'logout_time' => null,

                    'hours'       => null,
                ];
            }

            $loginTime = $att->punch_in
                ? Carbon::parse($att->punch_in)
                : null;

            $logoutTime = $att->punch_out
                ? Carbon::parse($att->punch_out)
                : null;

            // PRESENT / LATE
            if ($loginTime) {

                if ($loginTime->format('H:i') > '10:00') {

                    $lateCount++;

                    $status = 'late';

                } else {

                    $presentCount++;

                    $status = 'present';
                }

            } else {

                $status = 'absent';

                $absentCount++;
            }

            // TOTAL HOURS
            $hours = null;

            if ($loginTime) {

                // IF PUNCHED OUT
                if ($att->punch_out) {

                    $minutes = $att->total_minutes;

                } else {

                    // LIVE RUNNING TIME
                    $minutes = $loginTime->diffInMinutes(now());
                }

                $hrs = floor($minutes / 60);

                $mins = $minutes % 60;

                $hours = $hrs . 'h ' . $mins . 'm';
            }

            return [

                'user_id'     => $user->id,

                'name'        => $user->name,

                'phone'       => $user->phone,

                'role'        => ucfirst($user->role),

                'status'      => $status,

                'login_time'  => $loginTime
                    ? $loginTime->format('H:i')
                    : null,

                'logout_time' => $logoutTime
                    ? $logoutTime->format('H:i')
                    : null,

                'hours'       => $hours,
            ];
        });

        return response()->json([

            'status'  => true,

            'message' => 'Attendance fetched successfully',

            'counts'  => [

                'present' => $presentCount,

                'late'    => $lateCount,

                'absent'  => $absentCount,
            ],

            'date'    => $date,

            'data'    => $data,
        ]);
    }

    /**
     * Summary of attendanceDashboard
     *
     */
    public function attendanceDashboard()
    {
        $userId = auth()->id();
        $today  = now()->toDateString();

        $todayAttendance = Attendance::where('user_id', $userId)
            ->whereDate('attendance_date', $today)
            ->first();

        $records = Attendance::where('user_id', $userId)
            ->orderByDesc('attendance_date')
            ->get()
            ->map(function ($item) {

                $status = $item->punch_in ? 'present' : 'absent';

                return [
                    'date'     => $item->attendance_date,

                    'punchIn'  => $item->punch_in
                        ? Carbon::parse($item->punch_in)->format('h:i A')
                        : null,

                    'punchOut' => $item->punch_out
                        ? Carbon::parse($item->punch_out)->format('h:i A')
                        : null,

                    'status'   => $status,
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => 'Attendance fetched successfully',
            'data'    => [
                'currentStatus'  =>
                ($todayAttendance && ! $todayAttendance->punch_out)
                    ? 'present'
                    : null,

                'currentPunchIn' =>
                $todayAttendance?->punch_in
                    ? Carbon::parse(
                    $todayAttendance->punch_in
                )->format('h:i A')
                    : null,

                'records'        => $records,
            ],
        ]);
    }

    public function punch()
    {
        $userId = auth()->id();
        $today  = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('attendance_date', $today)
            ->first();

        // Punch In
        if (! $attendance) {

            $attendance = Attendance::create([
                'user_id'         => $userId,
                'attendance_date' => $today,
                'punch_in'        => now(),
            ]);

            return response()->json([
                'status'  => true,
                'action'  => 'punch_in',
                'message' => 'Punch In successful',
                'data'    => [
                    'punch_in' => now()->format('h:i A'),
                ],
            ]);
        }

        // Already punched out
        if ($attendance->punch_out) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance already completed for today',
            ]);
        }

        // Punch Out
        $attendance->update([
            'punch_out'     => now(),
            'total_minutes' => Carbon::parse($attendance->punch_in)
                ->diffInMinutes(now()),
        ]);

        return response()->json([
            'status'  => true,
            'action'  => 'punch_out',
            'message' => 'Punch Out successful',
            'data'    => [
                'punch_out' => now()->format('h:i A'),
            ],
        ]);
    }
}
