<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
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
}