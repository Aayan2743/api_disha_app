<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    //

    public $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}