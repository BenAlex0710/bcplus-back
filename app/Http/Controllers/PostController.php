<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    public function createpost(Request $request)
{
    $post = new Post();
    $post->id = $request->input('user_id');
    $post->title = $request->input('title');
    $post->content = $request->input('content');

    if ($request->filled('post_media')) {
        $base64Image = $request->input('post_media');

        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

        $imageData = base64_decode($base64Image);

        $filename = 'post_media_' . Str::random(10) . '.png';

        $directory = public_path('post_media');




        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath =    'post_media'. '/' . $filename;
        file_put_contents($filePath, $imageData);



        $post->post_media = 'http://192.168.0.112:8000/'.$filePath;



    }
    $post->save();

    $user = User::find($post->user_id);

    return response()->json([
        'post' => $post,
        'user' => $user,
    ], 200);
}
public function likepost(Request $request, $postId)
{

    $validatedData = $request->validate([
        'user_id' => 'required',
      
    ]);


    $like = new Like();
    $like->post_id = $postId;
    $like->id = $validatedData['user_id'];

    $like->save();
    $user = User::where('id', $like->id)->get();
    return response()->json([
       'like' => $like,
        'user'=>$user
   ], 200);

}

     public function commentpost(Request $request, $postId)
     {

         $validatedData = $request->validate([
             'user_id' => 'required',
             'content' => 'required',
         ]);


         $comment = new Comment();
         $comment->post_id = $postId;
         $comment->id = $validatedData['user_id'];
         $comment->comment_content = $validatedData['content'];
         $comment->save();
         $user = User::where('id', $comment->id)->get();
         return response()->json([
            'comment' => $comment,
             'user'=>$user
        ], 200);

     }
     public function getPostWithLikesAndComments($userId)
     {

         $posts = Post::where('id', $userId)->with('user')->get();


         foreach ($posts as $post) {

             $post->likes = Like::where('post_id', $post->post_id)->get();
             $post->comments = Comment::where('post_id', $post->post_id)->get();

         }
         foreach ($posts as $post) {
            $likes = Like::where('post_id', $post->post_id)->get();
            $comments = Comment::where('post_id', $post->post_id)->get();

            foreach ($likes as $like) {
                $likeUserId = $like->id;
                $like->user = User::find($likeUserId);
            }

            // Loop through each comment and append user details
            foreach ($comments as $comment) {
                $commentUserId = $comment->id;
                $comment->user = User::find($commentUserId);
            }

            // Assign the modified likes and comments array to the post
            $post->likes = $likes;
            $post->comments = $comments;
        }





         $user = User::find($userId);

         return response()->json([
             'posts' => $posts,
             'user' => $user
         ], 200);
     }

}
