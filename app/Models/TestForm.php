<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestForm extends Model
{

    protected $table = 'test_form';
    protected $guarded = ['id'];
    public $timestamps = false;
}
