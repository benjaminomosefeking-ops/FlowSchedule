<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date|after:today',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|between:1,3',
        ]);

        $todo = Todo::create([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 1,
            'user_id' => Auth::id(),
            'completed' => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea creada exitosamente',
                'todo' => $todo
            ], 201);
        }

        return back()->with('success', 'Tarea creada exitosamente');
    }

    public function toggle(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta tarea');
        }

        $todo->completed = !$todo->completed;
        $todo->save();

        return response()->json([
            'success' => true,
            'completed' => $todo->completed
        ]);
    }
    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) abort(403);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date|after:today',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|between:1,3',
        ]);
        
        $todo->update($validated);
        return response()->json(['success' => true]);
    }

    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) abort(403);
        $todo->delete();
        return response()->json(['success' => true]);
    }

    public function export(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) abort(403);
        
        $date = $todo->due_date ? $todo->due_date->format('Ymd') : now()->format('Ymd');
        
        $content = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//FlowScheduler//EN\n";
        $content .= "BEGIN:VEVENT\n";
        $content .= "SUMMARY:" . str_replace(["\r", "\n"], ' ', $todo->title) . "\n";
        $content .= "DESCRIPTION:" . str_replace(["\r", "\n"], ' ', $todo->description ?? '') . "\n";
        $content .= "DTSTART;VALUE=DATE:" . $date . "\n";
        $content .= "DTEND;VALUE=DATE:" . $date . "\n";
        $content .= "END:VEVENT\nEND:VCALENDAR";

        return response($content)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="tarea-' . $todo->id . '.ics"');
    }
}

