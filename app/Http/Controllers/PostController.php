<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
class PostController extends Controller
{
     public function createpost(Request $request){


        // $validatedData = $request->validate([
        //     'user_id' => 'required',
        //     'title' => 'required',
        //     'content' => 'required',
        //     'post_media' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);


        $post = new Post();
        $post->id = $request['user_id'];
        $post->title = $request['title'];
        $post->content = $request['content'];


        if ($request->hasFile('post_media')) {
            $mediaFile = $request->file('post_media');
            $mediaFileName = $mediaFile->getClientOriginalName();
            $mediaPath = $mediaFile->storeAs('public/media', $mediaFileName);


            $post->post_media = str_replace('public/', '', $mediaPath);
        }

        $post->save();

        return response()->json($post, 201);
     }
     public function likePost(Request $request, $postId)
     {
         $like = new Like();
         $like->post_id = $postId;
         $like->id = $request->user_id;
         $like->save();

         return response()->json($like, 201);
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

         return response()->json($comment, 201);
     }
     public function getPostWithLikesAndComments($userId)
     {

        $posts = Post::with(['likes', 'comments'])->where('id', $userId)->get();

         return response()->json($posts, 200);
     }

}
