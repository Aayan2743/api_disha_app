<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public $guarded = [];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

}
