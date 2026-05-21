<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    public $guarded = [];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    protected static function booted()
    {
        static::creating(function ($appointment) {

            if ($appointment->payment_method == 'online_payment') {

                $appointment->payment_status = 'completed';

            } else {

                $appointment->payment_status = 'pending';
            }
        });
    }
}
