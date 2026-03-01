<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProfessionalController extends Controller
{
    public function index()
    {
        $professionals = Professional::all();
        $title = "Professionals";
        return view('professionals', compact('professionals', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        Professional::create($request->all());

        return back()->with('success', 'Professional Added Successfully!');
    }

    public function update(Request $request, Professional $professional)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $professional->update($request->all());

        return back()->with('success', 'Professional Updated Successfully!');
    }

    public function destroy(Professional $professional)
    {
        try {
            $professional->delete();
            return back()->with('success', 'Professional Deleted Successfully!');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Cannot delete this professional as they are linked to existing records.');
            }
            return back()->with('error', 'Something went wrong.');
        }
    }
}
