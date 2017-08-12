<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\Tag;
use Auth;
use Gate;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function getIndex()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(2);
        return view('blog.index', ['posts' => $posts]);
    }

    public function getAdminIndex()
    {
      //  if(!Auth::check()){//This code will check if user is authenticated and return boolean value
        //  return redirect()->back();
        //}
        $posts = Post::orderBy('title', 'asc')->get();
        return view('admin.index', ['posts' => $posts]);
    }

    public function getPost($id)
    {
        $post = Post::where('id', $id)->with('likes')->first();// this is eager loading - see documentation
        return view('blog.post', ['post' => $post]);
    }

    public function getLikePost($id)
    {
        $post = Post::where('id', $id)->first();
        $like = new Like();
        $post->likes()->save($like);
        return redirect()->back();
    }

    public function getAdminCreate()
    {
        //if(!Auth::check()){//This code will check if user is authenticated and return boolean value
          //return redirect()->back();
        //}
        $tags = Tag::all();
        return view('admin.create', ['tags' => $tags]);
    }

    public function getAdminEdit($id)
    {
        //if(!Auth::check()){//This code will check if user is authenticated and return boolean value
        //  return redirect()->back();
        //}
        $post = Post::find($id);
        $tags = Tag::all();
        return view('admin.edit', ['post' => $post, 'postId' => $id, 'tags' => $tags]);
    }

    public function postAdminCreate(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        //Here we create code to check if user is logged of register(authenticated) - we will change it later with route protection
        $user = Auth::user();// Here we use Auth facade to get the user if it is authenticated
        //if (!$user) {
          //return redirect()->back();
        //}
        $post = new Post([
          'title' => $request->input('title'),
          'content' => $request->input('content')
        ]);
        $user->posts()->save($post); // Here we create relations and store eloquent instance in post table with user id of authenticated user
        $post->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));

        return redirect()->route('admin.index')->with('info', 'Post created, Title is: ' . $request->input('title'));
    }

    public function postAdminUpdate(Request $request)
    {
        //if(!Auth::check()){//This code will check if user is authenticated and return boolean value
          //return redirect()->back();
        //}
        $this->validate($request, [
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        $post = Post::find($request->input('id'));//Here we find the post to update...
        if(Gate::denies('manipulating-post', $post)){// Here we say if gate 'manipulating-post' denies $post
          return redirect()->back();
        }
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();
        //$post->tags()->detach();
        //$post->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));
        $post->tags()->sync($request->input('tags') === null ? [] : $request->input('tags'));
        return redirect()->route('admin.index')->with('info', 'Post edited, new Title is: ' . $request->input('title'));
    }

    public function getAdminDelete($id)
    {
      //if(!Auth::check()){//This code will check if user is authenticated and return boolean value
        //return redirect()->back();
      //}
      $post = Post::find($id);
      if(Gate::denies('manipulating-post', $post)){// Here we say if gate 'manipulating-post' denies $post [ here we ]
        return redirect()->back();
      }
      $post->likes()->delete();
      $post->tags()->detach();
      $post->delete();
      return redirect()->route('admin.index')->with('info', 'Post deleted!');

    }
}
