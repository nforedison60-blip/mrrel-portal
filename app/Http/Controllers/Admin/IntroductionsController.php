<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyMatch;
use App\Models\User;

// Admin view of landlord introductions: who decided what, what is paid, and both contacts to coordinate the viewing
class IntroductionsController extends Controller
{
    public function index()
    {
        $matches = PropertyMatch::whereNotNull('landlord_status')
            ->with(['property', 'need', 'user'])
            ->latest('landlord_decided_at')
            ->get();

        $landlords = User::whereIn('id', $matches->map(fn ($m) => optional($m->property)->user_id)->filter()->unique())->get()->keyBy('id');
        $paid = $matches->filter(fn ($m) => $m->introPaid());

        return view('admin.introductions', [
            'matches' => $matches,
            'landlords' => $landlords,
            'collected' => $paid->sum('intro_amount'),
            'paidCount' => $paid->count(),
        ]);
    }
}
