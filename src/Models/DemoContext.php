<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DemoContext extends Model
{
    use HasUuids;

    protected $table = 'demo_contexts';

    protected $fillable = [
        'name',
    ];
}
