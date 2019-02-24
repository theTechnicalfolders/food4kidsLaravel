<?php

namespace App\Http\Controllers\Api;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    //
    public function index(){
    	
    	$posts = Auth::user()->posts()->get();
    	dd($posts);
    }
}
