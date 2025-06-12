<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index()
    {
        return response()->json(
            Article::with([
                'era',
                'governorate',
                'images',
                'comments' => fn($q) => $q->where('is_approved', true)->with('user'),
                'ratings' => fn($q) => $q->where('is_approved', true),
                'user' // Include user who created the article
            ])->paginate(15)
        );
    }

    public function show($id)
    {
        return response()->json(
            Article::with([
                'era',
                'governorate',
                'images',
                'comments' => fn($q) => $q->where('is_approved', true)->with('user'),
                'ratings' => fn($q) => $q->where('is_approved', true)
            ])->findOrFail($id)
        );
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'min:3']
        ]);

        ArticleComment::create([
            'article_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->input('content')
        ]);

        return response()->json(['message' => 'Comment submitted for approval'], 201);
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5']
        ]);

        ArticleRating::updateOrCreate(
            ['article_id' => $id, 'user_id' => Auth::id()],
            ['rating' => $request->rating]
        );

        return response()->json(['message' => 'Rating submitted for approval'], 201);
    }
}
?>
