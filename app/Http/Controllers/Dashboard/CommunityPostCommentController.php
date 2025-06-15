<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CommunityPostComment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommunityPostCommentController extends Controller
{
    public function index(): Factory|View|Application
    {
        $comments = CommunityPostComment::with(['post', 'user'])->paginate(15);
        return view('dashboard.community_comments.index', compact('comments'));
    }

    public function destroy($id): RedirectResponse
    {
        CommunityPostComment::destroy($id);
        return back()->with('success', 'Comment deleted successfully');
    }
}
