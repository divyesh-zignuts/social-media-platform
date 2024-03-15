<?php

namespace App\Http\Controllers\API;

use finfo;
use App\Models\Post;
use App\Models\User;
use App\Models\PostAsset;
use Illuminate\Http\Request;
use App\Http\Traits\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $post = Post::select('id', 'user_id', 'bio')->with('assets:id,post_id,type,asset_url', 'user:id,first_name,last_name,profile_image_url')->where('user_id', Auth::user()->id)->get();

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
}
