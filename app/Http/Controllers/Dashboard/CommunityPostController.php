<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $posts = CommunityPost::with('user')->paginate(15);
        return view('dashboard.community_posts.index', compact('posts'));
    }

    public function destroy($id)
    {
        $post = CommunityPost::findOrFail($id);
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $post->delete();
        return back()->with('success', 'Post deleted successfully');
    }
}
?>
