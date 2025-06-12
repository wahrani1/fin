<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ArticleRating;
use Illuminate\Http\Request;

class ArticleRatingController extends Controller
{
    public function index()
    {
        $ratings = ArticleRating::with(['article', 'user'])->paginate(15);
        return view('dashboard.ratings.index', compact('ratings'));
    }

    public function approve($id)
    {
        $rating = ArticleRating::findOrFail($id);
        $rating->update(['is_approved' => true]);
        return back()->with('success', 'Rating approved successfully');
    }

    public function destroy($id)
    {
        ArticleRating::destroy($id);
        return back()->with('success', 'Rating deleted successfully');
    }
}
?>
