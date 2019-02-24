<?php

namespace App\Http\Controllers\Api;
use Auth;
use DB; 
use App\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    //
   public function index(){

  //  $user_id=Auth::user()->id;
     
    	$posts = Auth::user()->posts()->get();

    	//$posts = Post::where('user_id',Auth::user()->id)->get();
     
      //dd($posts);

         // $posts = User::find(user_id)->posts;

    	//$posts = DB::table('posts')->get();
    	//$posts=Auth::user()->posts()->get();
    	
    	return response()->json(['data' => $posts], 200, [], JSON_NUMERIC_CHECK);
    }
}
