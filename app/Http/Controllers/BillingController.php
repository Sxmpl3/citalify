<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tz = $user->timezone ?? 'Europe/Madrid';
        $now = Carbon::now($tz);
        
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'), $tz)->startOfDay()
            : $now->copy()->startOfMonth();
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'), $tz)->endOfDay()
            : $now->copy()->endOfMonth();

        $todayRevenue = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereDate('starts_at', $now->toDateString())
            ->sum('price');
            
        $weekRevenue = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereBetween('starts_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->sum('price');
            
        $monthRevenue = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereMonth('starts_at', $now->month)
            ->whereYear('starts_at', $now->year)
            ->sum('price');
            
        $rangeBookings = Booking::with('service')
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereBetween('starts_at', [$startDate->copy()->utc(), $endDate->copy()->utc()])
            ->orderBy('starts_at', 'desc')
            ->get();
            
        $rangeRevenue = $rangeBookings->sum('price');
        
        return view('billing.index', compact(
            'todayRevenue', 'weekRevenue', 'monthRevenue', 
            'rangeBookings', 'rangeRevenue', 'startDate', 'endDate'
        ));
    }
}
