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
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            CommunityPost::with(['user', 'images', 'comments.user'])->paginate(15)
        );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'min:10'],
            'images.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        $post = CommunityPost::create([
            'user_id' => Auth::id(),
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

    public function show($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            CommunityPost::with(['user', 'images', 'comments.user'])->findOrFail($id)
        );
    }
    // Update/Edit Post
    public function update(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => ['required', 'string', 'min:10'],
            'images.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        $post->update([
            'content' => $request->input('content')
        ]);

        // Handle new images if uploaded
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
            'message' => 'Post updated successfully',
            'post' => $post->load(['images', 'user'])
        ]);
    }


    public function comment(Request $request, $id): \Illuminate\Http\JsonResponse
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

    // Update Comment
    public function updateComment(Request $request, $commentId)
    {
        $comment = CommunityPostComment::findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => ['required', 'string', 'min:3']
        ]);

        $comment->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment->load('user')
        ]);
    }

// Delete Comment
    public function destroyComment($commentId): \Illuminate\Http\JsonResponse
    {
        $comment = CommunityPostComment::findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

// Delete Post
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete associated images from storage
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }


}
