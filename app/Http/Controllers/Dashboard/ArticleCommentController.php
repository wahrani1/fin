<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    public function index()
    {
        $comments = ArticleComment::with(['article', 'user'])->paginate(15);
        return view('dashboard.comments.index', compact('comments'));
    }

    public function approve($id)
    {
        $comment = ArticleComment::findOrFail($id);
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved successfully');
    }

    public function destroy($id)
    {
        ArticleComment::destroy($id);
        return back()->with('success', 'Comment deleted successfully');
    }
}
?>
