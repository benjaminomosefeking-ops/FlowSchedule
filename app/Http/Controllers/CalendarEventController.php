<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CalendarEventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = CalendarEvent::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->whereNotNull('title')
                    ->where('title', '!=', '')
                    ->orWhereNotNull('note')
                    ->where('note', '!=', '')
                    ->orWhereNotNull('color')
                    ->orWhereNotNull('drawing');
            })
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function (CalendarEvent $event) {
                return [$event->date->format('Y-m-d') => [
                    'title' => $event->title,
                    'note' => $event->note,
                    'color' => $event->color,
                    'drawing' => $event->drawing,
                ]];
            });

        return response()->json($events);
    }

    public function store(Request $request, string $date): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:60',
            'note' => 'nullable|string|max:200',
            'color' => ['nullable', 'string', 'max:20', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'drawing' => 'nullable|string',
        ]);

        if (collect($validated)->filter(fn ($value) => filled($value))->isEmpty()) {
            CalendarEvent::where('user_id', Auth::id())->whereDate('date', $date)->delete();

            return response()->json(['date' => $date, 'deleted' => true]);
        }

        $event = CalendarEvent::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $date],
            $validated
        );

        return response()->json(['date' => $event->date->format('Y-m-d')]);
    }

    public function destroy(Request $request, string $date): JsonResponse|RedirectResponse
    {
        CalendarEvent::where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->delete();

        if (!$request->expectsJson()) {
            return redirect()->route('dashboard')->with('success', 'Calendario eliminado.');
        }

        return response()->json(['success' => true]);
    }
}
