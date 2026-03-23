<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class ProgressionSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'progression_update_date' => Setting::get('progression_update_date'),
            'progression_min_year_group' => Setting::get('progression_min_year_group'),
            'progression_max_year_group' => Setting::get('progression_max_year_group'),
        ];

        $currentDate = $settings['progression_update_date'] ?? '';
        $currentMonth = $currentDate ? explode('-', $currentDate)[0] : '';
        $currentDay = $currentDate ? explode('-', $currentDate)[1] : '';

        $title = 'Progression Settings';
        return view('progression_settings', compact('settings', 'title', 'currentMonth', 'currentDay'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'progression_update_month' => 'required|string|size:2',
            'progression_update_day' => [
                'required',
                'string',
                'size:2',
                function ($attribute, $value, $fail) use ($request) {
                    if (!checkdate((int) $request->progression_update_month, (int) $value, 2026)) {
                        $fail('The selected date is invalid.');
                    }
                },
            ],
            'progression_min_year_group' => 'required|integer|min:1',
            'progression_max_year_group' => 'required|integer|gte:progression_min_year_group',
        ]);

        $date = $validated['progression_update_month'] . '-' . $validated['progression_update_day'];
        
        Setting::set('progression_update_date', $date);
        Setting::set('progression_min_year_group', $validated['progression_min_year_group']);
        Setting::set('progression_max_year_group', $validated['progression_max_year_group']);

        return redirect()->back()->with('success', 'Progression settings updated successfully!');
    }
}
