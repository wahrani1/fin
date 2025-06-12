<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CommunityPostComment;
use Illuminate\Http\Request;

class CommunityPostCommentController extends Controller
{
    public function index()
    {
        $comments = CommunityPostComment::with(['post', 'user'])->paginate(15);
        return view('dashboard.community_comments.index', compact('comments'));
    }

    public function destroy($id)
    {
        CommunityPostComment::destroy($id);
        return back()->with('success', 'Comment deleted successfully');
    }
}
?>
