<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'type', 'subject', 'message', 'is_answered', 'reply', 'replied_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['is_answered' => 'boolean', 'replied_at' => 'datetime'];
    }
}
