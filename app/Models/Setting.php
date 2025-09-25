<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public function getParsedValueAttribute()
    {
        if ($this->type === 'json') {
            $decoded = json_decode($this->value, true);

            return $decoded ?? $this->value;
        }

        return $this->value;
    }
}
