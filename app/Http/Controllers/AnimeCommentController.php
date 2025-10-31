<?php

namespace App\Http\Controllers;

use App\Models\AnimeComment;
use App\Models\AnimeCommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnimeCommentController extends Controller
{
    public function store(Request $request)
{
    // Validar los datos del formulario
    $data = $request->validate([
        'anime_id'   => 'required|integer',
        'user_name'  => 'nullable|string|max:100',
        'content'    => 'required|string|min:2',
        'image'      => 'nullable|image|max:2048', // máx. 2MB
        'is_spoiler' => 'nullable|boolean',
    ]);

    // Crear instancia manualmente para evitar errores de mass-assignment
    $comment = new \App\Models\AnimeComment();
    $comment->anime_id = $data['anime_id'];
    $comment->content = $data['content'];
    $comment->is_spoiler = $request->filled('is_spoiler');

    // Guardar imagen si se adjunta
    if ($request->hasFile('image')) {
        $comment->image = $request->file('image')->store('comments', 'public');
    }

    // Asignar usuario (autenticado o anónimo)
    if (auth()->check()) {
        $comment->user_id = auth()->id();                
        $comment->user_name = auth()->user()->name;
    } else {
        $comment->user_name = $data['user_name'] ?? 'Anónimo';
    }

    // Guardar en base de datos
    $comment->save();

    // Redirigir con mensaje de éxito
    return redirect()->back()->with('success', 'Comentario agregado con éxito.');
}


    // Toggle like: intenta crear like, si ya existe lo borra (unlike).
    public function toggleLike(Request $request, $commentId)
    {
        // Asegurarse de que el usuario esté autenticado
        if (!auth()->check()) {
            // Si es una petición AJAX, devolvemos JSON
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autenticado'], 403);
            }

            // Si es una petición normal, redirigimos con error
            return redirect()->back()->with('error', 'Debes iniciar sesión para dar like.');
        }

        $user = auth()->user();
        $comment = AnimeComment::findOrFail($commentId);

        DB::beginTransaction();
        try {
            // Buscar si el usuario ya dio like
            $existing = AnimeCommentLike::where('anime_comment_id', $comment->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                // Si ya dio like → lo quitamos
                $existing->delete();
                $comment->decrement('likes_count');
                $liked = false;
            } else {
                // Si no → creamos el like
                AnimeCommentLike::create([
                    'anime_comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ]);
                $comment->increment('likes_count');
                $liked = true;
            }

            DB::commit();

            // Si la petición viene de fetch() → devolvemos JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'likes_count' => $comment->likes_count,
                    'liked' => $liked,
                ]);
            }

            // Si no es AJAX, redirigir normalmente
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
