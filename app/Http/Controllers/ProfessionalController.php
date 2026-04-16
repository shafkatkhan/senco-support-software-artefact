<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class ProfessionalController extends Controller
{
    public function index()
    {
        Gate::authorize('view-professionals');

        $professionals = Professional::all();
        $title = __('Professionals');
        return view('professionals', compact('professionals', 'title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-professionals');

        $professional = Professional::create($request->validate([
            'title' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]));

        return back()->with('success', __(':item ":name" added successfully!', ['item' => __('Professional'), 'name' => $professional->first_name.' '.$professional->last_name]));
    }

    public function update(Request $request, Professional $professional)
    {
        Gate::authorize('edit-professionals');

        $professional->update($request->validate([
            'title' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]));

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('Professional'), 'name' => $professional->first_name.' '.$professional->last_name]));
    }

    public function destroy(Professional $professional)
    {
        Gate::authorize('delete-professionals');
        
        try {
            $professional->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('Professional'), 'name' => $professional->first_name.' '.$professional->last_name]));
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', __('Cannot delete this professional as they are linked to existing records.'));
            }
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
