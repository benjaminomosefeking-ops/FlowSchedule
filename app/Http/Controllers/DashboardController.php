<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Todo;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // For personal use, we skip onboarding check since the field was removed
        // Get user's shifts for this week
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Get upcoming shifts for the user
        $upcomingShifts = Shift::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->orderBy('start_time', 'asc')
            ->get();

        // Get calendar events for the user
        $calendarEventQuery = CalendarEvent::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNotNull('title')
                    ->where('title', '!=', '')
                    ->orWhereNotNull('note')
                    ->where('note', '!=', '')
                    ->orWhereNotNull('color')
                    ->orWhereNotNull('drawing');
            });
        $calendarEventCount = (clone $calendarEventQuery)->count();
        $calendarEvents = $calendarEventQuery
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $shiftsThisWeek = $upcomingShifts->count();

        // Calculate total hours worked this week
        $totalHours = $upcomingShifts->sum(function ($shift) {
            return $shift->start_time->diffInHours($shift->end_time);
        });

        // Get pending todos for the user
        $todos = \App\Models\Todo::where('user_id', $user->id)
            ->where('completed', false)
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();

        $pendingTodoCount = $todos->count();

        return view('dashboard', compact(
            'upcomingShifts',
            'calendarEvents',
            'calendarEventCount',
            'shiftsThisWeek',
            'totalHours',
            'todos',
            'pendingTodoCount'
        ));
    }
}
