<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestForm;

class TestFormController extends Controller
{
    public function index()
    {
        $test_rows = TestForm::orderBy('id', 'desc')->get();
        return view('test_form', compact('test_rows'));
    }
}
