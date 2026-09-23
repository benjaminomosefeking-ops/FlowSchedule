<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Board;
use App\Models\Team;
use App\Events\BoardUpdated;

class BoardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // For personal use, only show boards belonging to the current user
        $boards = Board::where('user_id', $user->id)
            ->with('user')  // Keep user relationship for consistency
            ->latest()
            ->get();

        return view('boards.index', compact('boards'));
    }

    public function create()
    {
        // For personal use, no team selection needed
        return view('boards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $board = Board::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'content' => [
                'type' => 'board',
                'version' => 2,
                'strokes' => [],
                'tables' => [],
                'viewport' => [
                    'scale' => 1,
                    'offsetX' => 0,
                    'offsetY' => 0,
                ],
            ],
        ]);

        return redirect()->route('boards.show', $board->id);
    }

    public function show(Board $board)
    {
        $board->load('user');  // Keep user relationship for consistency

        Gate::authorize('view', $board);

        return view('boards.show', compact('board'));
    }

    public function content(Board $board)
    {
        Gate::authorize('view', $board);

        return response()->json([
            'content' => $board->content ?? [],
            'updated_at' => $board->updated_at?->toISOString(),
        ]);
    }

    public function update(Request $request, Board $board)
    {
        $request->validate([
            'content' => ['required', 'json'],
        ]);

        $content = json_decode($request->input('content'), true);
        Gate::authorize('update', [$board, $content]);

        $board->update([
            'content' => $content,
        ]);

        // Broadcast the board update to all connected clients
        event(new BoardUpdated($board));

        return response()->json(['success' => true, 'message' => 'Pizarra guardada']);
    }

    public function destroy(Board $board)
    {
        Gate::authorize('delete', $board);

        $board->delete();

        return redirect()->route('boards.index')->with('success', 'Pizarra eliminada exitosamente');
    }
}