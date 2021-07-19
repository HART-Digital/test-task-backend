<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTest extends Model
{
    use HasFactory;

    protected $table = 'test_plan';

    protected $fillable = ['plan_id', 'path'];
}
