<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Era;
use App\Models\Governorate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(): Factory|View|Application
    {
        $articles = Article::with(['era', 'governorate'])->paginate(15);
        return view('dashboard.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): Factory|View|Application
    {
        $eras = Era::all();
        $governorates = Governorate::all();
        return view('dashboard.articles.create', compact('eras', 'governorates'));
    }

    /**
     * Store a newly created article in storage.
     */

    public function store(Request $request): RedirectResponse
    {
        $request->validate(Article::rules());

        // Debug: Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create articles');
        }

        // Debug: Get user ID and check if it's valid
        $userId = Auth::id();
        if (!$userId || $userId == 0) {
            return redirect()->back()->with('error', 'Unable to get user ID. Please try logging in again.');
        }

        $data = $request->except('images');

        // Set user_id to current authenticated user
        $data['user_id'] = $userId;

        $article = Article::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('articles', 'public');
                ArticleImage::create([
                    'article_id' => $article->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('articles.index')->with('success', 'Article created successfully');
    }


    /**
     * Show the form for editing the specified article.
     */
    public function edit($id): Factory|View|Application
    {
        $article = Article::with('images')->findOrFail($id);
        $eras = Era::all();
        $governorates = Governorate::all();
        return view('dashboard.articles.edit', compact('article', 'eras', 'governorates'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate(Article::rules());

        $article = Article::findOrFail($id);

        // Prepare data for update, ensuring all fillable fields are included
        $data = $request->only(['name', 'description', 'category', 'era_id', 'governorate_id']);

        // Update the article with validated data
        $article->update($data);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('articles', 'public');
                ArticleImage::create([
                    'article_id' => $article->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()
            ->route('articles.index')
            ->with('success', 'Article updated successfully');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $article = Article::findOrFail($id);
        foreach ($article->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article deleted successfully');
    }

    /**
     * Delete a specific image associated with an article.
     */
    public function deleteImage($imageId): RedirectResponse
    {
        $image = ArticleImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Image deleted successfully');
    }
}

