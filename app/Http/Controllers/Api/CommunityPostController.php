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
    /**
     * Get all community posts with comments in descending order
     */
    public function index(Request $request)
    {
        $perPage = min($request->get('per_page', 15), 50);

        $posts = CommunityPost::with([
            'user:id,name,email,avatar',
            'images',
            'comments' => function ($query) {
                $query->with('user:id,name,email')
                    ->orderBy('created_at', 'desc'); // Comments in descending order
            }
        ])
            ->withCount('comments')
            ->orderBy('created_at', 'desc') // Posts in descending order
            ->paginate($perPage);

        return response()->json([
            'posts' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ],
            'total_posts' => $posts->total()
        ]);
    }

    /**
     * Get a specific post with comments in descending order
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        $post = CommunityPost::with([
            'user:id,name,email,avatar',
            'images'
        ])
            ->withCount('comments')
            ->findOrFail($id);

        // Get comments separately with explicit ordering
        $comments = CommunityPostComment::where('community_post_id', $id)
            ->with('user:id,name,email,avatar')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add comments to post
        $post->comments = $comments;

        return response()->json([
            'post' => $post
        ]);
    }

    /**
     * Store a new community post
     */
    public function store(Request $request)
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
            'post' => $post->load(['images', 'user:id,name,email,avatar'])
        ], 201);
    }

    /**
     * Update/Edit Post
     */
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
            'post' => $post->load(['images', 'user:id,name,email'])
        ]);
    }

    /**
     * Delete Post
     */
    public function destroy($id)
    {
        $post = CommunityPost::findOrFail($id);

        // Check if user owns the post
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

    /**
     * Add comment to a post
     */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'min:3']
        ]);

        $comment = CommunityPostComment::create([
            'community_post_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->input('content')
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user:id,name,email')
        ], 201);
    }

    /**
     * Update/Edit Comment
     */
    public function updateComment(Request $request, $commentId)
    {
        $comment = CommunityPostComment::findOrFail($commentId);

        // Check if user owns the comment
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
            'comment' => $comment->load('user:id,name,email')
        ]);
    }

    /**
     * Delete Comment
     */
    public function destroyComment($commentId)
    {
        $comment = CommunityPostComment::findOrFail($commentId);

        // Check if user owns the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}

?>
