<?php

namespace App\Http\Controllers;
use illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        //get post
        $posts = Post::latest()->paginate(5);

        //render view with post 
        return view('posts.index', compact('posts'));
    }
    //

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
       //validate form
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,spg|max:2048',
            'title' => 'required|min:5',
            'content' => ' required|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image -> storeAs('public/posts', $image->hashName());
        //create post
        Post::create([
            'image' => $image ->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        //validate form
        $this->validate($request,[
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,spg|max:2048',
            'title' => 'required|min:5',
            'content' => ' required|min:10'
        ]);
        // check bahwa gambar terupload
        if($request->hasFile('image')) {
            //upload new image
            $image = $request->file('image');
            $image -> storeAs('public/posts', $image->hashName());
            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post dengan gambar baru
            $post->update([
                'image' => $image ->hashName(),
                'title' => $request->title,
                'content' => $request->content
            ]);

        }else{
            $post->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }
        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(Post $post)
    {
        Storage::delete('public/posts/'.$post->image);
        $post->delete();
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }
}
