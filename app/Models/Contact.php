<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'subject', 'message', 'is_answered'];

    protected function casts(): array
    {
        return ['is_answered' => 'boolean'];
    }
}
