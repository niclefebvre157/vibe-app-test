<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $activities = $user->loginActivities()
            ->latest('logged_in_at')
            ->take(5)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }
}
