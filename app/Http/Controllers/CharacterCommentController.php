<?php

namespace App\Http\Controllers;

use App\Models\CharacterComment;
use App\Models\CharacterCommentLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharacterCommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'character_id' => 'required|integer',
            'user_name' => 'nullable|string|max:100',
            'content' => 'required|string|min:2',
            'image' => 'nullable|image|max:2048',
            'is_spoiler' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('comments', 'public');
        }

        $data['is_spoiler'] = $request->filled('is_spoiler');

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
            $data['user_name'] = auth()->user()->name;
        }

        CharacterComment::create($data);

        return redirect()->back()->with('success', 'Comentario agregado con éxito.');
    }

    public function toggleLike(Request $request, $commentId)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autenticado'], 403);
            }
            return redirect()->back()->with('error', 'Debes iniciar sesión para dar like.');
        }

        $user = auth()->user();
        $comment = CharacterComment::findOrFail($commentId);

        DB::beginTransaction();
        try {
            $existing = CharacterCommentLike::where('character_comment_id', $comment->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                $existing->delete();
                $comment->decrement('likes_count');
                $liked = false;
            } else {
                CharacterCommentLike::create([
                    'character_comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ]);
                $comment->increment('likes_count');
                $liked = true;
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'likes_count' => $comment->likes_count,
                    'liked' => $liked,
                ]);
            }

            return redirect()->back()->with(
                'success',
                $liked ? 'Has dado me gusta.' : 'Has quitado tu me gusta.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al procesar el like.'], 500);
            }
            return redirect()->back()->with('error', 'Error al procesar el like.');
        }
    }
}
