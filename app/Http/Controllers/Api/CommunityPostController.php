<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    public function index()
    {
        return response()->json(
            CommunityPost::with(['user', 'images', 'comments.user'])->paginate(15)
        );
    }

    public function store(Request $request)
    {
        $request->validate(CommunityPost::rules());

        $post = CommunityPost::create([
            'user_id' => Auth::id(),
            $request->input('title'),
            'content' => $request->input('content')
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('community_posts', 'public');
                CommunityPostImage::create([
                    'community_post_id' => $post->id,
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post->load(['images', 'user'])
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            CommunityPost::with(['user', 'images', 'comments.user'])->findOrFail($id)
        );
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'min:3']
        ]);

        CommunityPostComment::create([
            'community_post_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->input('content')
        ]);

        return response()->json(['message' => 'Comment added successfully'], 201);
    }
}

?>
