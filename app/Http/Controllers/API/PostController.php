<?php

namespace App\Http\Controllers\API;

use finfo;
use App\Models\Post;
use App\Models\User;
use App\Models\PostAsset;
use Illuminate\Http\Request;
use App\Http\Traits\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\PostComment;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reportedPostIds = Report::where('reported_by', Auth::user()->id)
            ->pluck('post_id');

        $post = Post::select('id', 'user_id', 'bio')
            ->with('assets:id,post_id,type,asset_url', 'user:id,first_name,last_name,profile_image_url')
            ->with(['comments' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'comment_id', 'comment')->with('user:id,first_name,last_name,profile_image_url', 'replies:id,post_id,user_id,comment_id,comment');
            }])
            ->withCount('likes')
            ->withCount('comments')
            ->where('user_id', Auth::user()->id)
            ->whereNotIn('id', $reportedPostIds)
            ->get();

        return ok(__('strings.success', ['name' => 'Posts list get']), [
            'post'     =>  $post
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        // $request->validate([
        //     'post_type' => 'required|in:I,V,G',
        //     'post_file' => [
        //         'required',
        //         'array',
        //         function ($attribute, $value, $fail) use ($request) {
        //             $post_type = $request->post_type;
        //             foreach ($value as $file) {
        //                 if ($post_type === 'I' && !in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/jpg'])) {
        //                     $fail('The ' . $attribute . ' must be a valid PNG, JPG, or JPEG image.');
        //                 } elseif ($post_type === 'V' && !in_array($file->getMimeType(), ['video/mp4', 'video/mpeg'])) {
        //                     $fail('The ' . $attribute . ' must be a valid MP4 or MPEG video.');
        //                 } elseif ($post_type === 'G' && $file->getMimeType() !== 'image/gif') {
        //                     $fail('The ' . $attribute . ' must be a valid GIF image.');
        //                 }
        //             }
        //         },
        //     ],
        //     'bio'       => 'required|string',
        // ]);

        $post_type = $request->post_type === 'I' ? 'images' : ($request->post_type === 'V' ? 'videos' : (($request->post_type === 'G') ? 'gif' : null));

        $post = Post::create([
            'user_id'   => Auth::user()->id,
            'bio'       => $request->bio,
        ]);

        if ($request->hasfile('post_file')) {
            $files = $request->file('post_file');
            foreach ($files as $file) {

                $filename = $this->createFilename($file);
                $image    = $this->storeFile($file, $filename, auth()->id(), $post_type);

                PostAsset::create([
                    'post_id'   => $post->id,
                    'type'      => $request->post_type,
                    'asset_url' => $image,
                ]);
            }
        }

        return ok(__('strings.success', ['name' => 'Post create']), ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id = null)
    {
        $post = Post::select("id", "user_id", "bio")->findOrFail($id);
        $post->assets;

        return ok(__('strings.success', ['name' => 'Post Details get']), [
            'post'     =>  $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'post_type'         => 'required|in:I,V,G',
            'hidden_post_file'  => 'nullable|string',
            'post_file'         => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    $post_type = $request->post_type;
                    foreach ($value as $file) {
                        if ($post_type === 'I' && !in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/jpg'])) {
                            $fail('The ' . $attribute . ' must be a valid PNG, JPG, or JPEG image.');
                        } elseif ($post_type === 'V' && !in_array($file->getMimeType(), ['video/mp4', 'video/mpeg'])) {
                            $fail('The ' . $attribute . ' must be a valid MP4 or MPEG video.');
                        } elseif ($post_type === 'G' && $file->getMimeType() !== 'image/gif') {
                            $fail('The ' . $attribute . ' must be a valid GIF image.');
                        }
                    }
                },
            ],
            'bio'               => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $postType = $request->post_type;
        $assetType = null;

        $postTypeMap = [
            'I' => 'images',
            'V' => 'videos',
            'G' => 'gif'
        ];

        if ($request->hasfile('post_file')) {
            $assetType = $postTypeMap[$postType] ?? null;
            if ($assetType) {
                // Remove Old File
                foreach ($post->assets as $asset) {
                    $this->unlink($asset->asset_url);
                }
                $files = $request->file('post_file');

                foreach ($files as $file) {
                    $filename = $this->createFilename($file);
                    $image    = $this->storeFile($file, $filename, auth()->id(), $assetType);
                }
            }
        } else {
            $image = $request->hidden_image;
        }

        $post->update([
            'bio'       => $request->bio,
        ]);

        // Update each asset individually
        $post->assets->each(function ($asset) use ($image, $postType) {
            $asset->update(['type' => $postType, 'asset_url' => $image]);
        });

        return ok(__('strings.success', ['name' => 'Post update']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);

        foreach ($post->assets as $asset) {
            $this->deleteDirectory(dirname($asset->asset_url));
            $asset->delete();
        }

        $post->delete();

        return ok(__('strings.success', ['name' => 'Post delete']));
    }

    /**
     * Like and Unlike post
     */
    public function likeUnlike(string $id)
    {
        $post = Post::findOrFail($id);

        if ($post->likes()->where('user_id', Auth::user()->id)->exists()) {
            // Unlike the post
            $post->likes()->detach(Auth::user()->id);
            $likeCount = $post->likes()->count();
            return ok(__('strings.success', ['name' => 'Post unlike']), ['post' => $post, 'likeCount' => $likeCount]);
        } else {
            // Like the post
            $post->likes()->attach(Auth::user()->id);
            $likeCount = $post->likes()->count();
            return ok(__('strings.success', ['name' => 'Post like']), ['post' => $post, 'likeCount' => $likeCount]);
        }
    }

    /**
     * Add comment on post
     */
    public function commentAdd(Request $request)
    {
        $this->validate($request, [
            'post_id'       => 'required|exists:posts,id',
            'comment_id'    => 'nullable|exists:post_comments,id',
            'comment'       => 'required|string',
        ]);

        $comment = PostComment::create([
            'post_id'       => $request->post_id,
            'user_id'       => Auth::user()->id,
            'comment_id'    => $request->comment_id,
            'comment'       => $request->comment,
        ]);

        return ok(__('strings.success', ['name' => 'Comment added']), [
            'comment'     =>  $comment
        ]);
    }

    public function commentUpdate(Request $request)
    {
        $this->validate($request, [
            'id'        => 'required|exists:post_comments',
            'comment'   => 'required|string',
        ]);

        $comment = PostComment::findOrFail($request->id);

        $comment->update([
            'comment'       => $request->comment,
        ]);

        return ok(__('strings.success', ['name' => 'Comment update']), [
            'comment'     =>  $comment
        ]);
    }

    public function commentDelete(string $id)
    {
        $comment = PostComment::findOrFail($id);
        $comment->delete();
        return ok(__('strings.success', ['name' => 'Comment delete']));
    }

    public function report(Request $request)
    {
        $this->validate($request, [
            'post_id'       => 'required|exists:posts,id',
            'reason'        => 'required|in:S,I,O,Oth',
            'other_reason'  => 'nullable|required_if:reason,Oth|string',
        ]);

        $existingReport = Report::where('post_id', $request->post_id)
            ->where('reported_by', Auth::user()->id)
            ->first();

        if ($existingReport) {
            return ok(__('strings.already', ['name' => 'Post', 'status' => 'reported']), ['report' => $existingReport]);
        }

        $report = Report::create([
            'post_id'       => $request->post_id,
            'reported_by'   => Auth::user()->id,
            'reason'        => $request->reason,
            'other_reason'  => $request->other_reason,
        ]);

        return ok(__('strings.success', ['name' => 'Report']), ['report' => $report]);
    }
}
