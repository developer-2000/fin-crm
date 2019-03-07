<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

use App\Models\PostUser;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now = date("Y-m-d H:i:s");

        $categories = Category::withCount(['posts'=> function ($query) use ($now) {
            $query->where('active', 1);
            $query->where('publish_at', '<=', $now);
        }])
        ->whereHas('posts')->get();

        return view('posts.public.index')
                ->with('posts', Post::with('author', 'category')->public($now)->paginate())
                ->with('categories', $categories);
    }

    public function searchPublic(Request $request)
    {
        $param['title'] = $request->title??null;
        $param['category'] = $request->category??null;
        $now = date("Y-m-d H:i:s");

        return view('posts.public.block')
                ->with('posts', Post::with('author', 'category')
                ->public($now)->filter($param)->paginate());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create')
                ->with('categories', Category::where('entity', 'post')->get());
    }

    /**
     * Store a newly created resource to db.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $date = parseDate($request->publish_at);
        $request['publish_at'] = $date??date("Y-m-d H:i:s");
        $request['author_id'] = auth()->user()->id;

        $post = Post::create($request->all());

        if ($post->id) {
            return response()->json(['success' => true]);
        }

        return response()->json(
        [
          'success' => false,
          'message' => 'Error created post'
        ]
      );
    }

    public function update(PostRequest $request)
    {
      $post = Post::find($request->id);

      if($post){
        $date = parseDate($request->publish_at);
        $request['publish_at'] = $date??date("Y-m-d H:i:s");
        $post->update($request->all());

        return response()->json(['success' => true]);
      }

      return response()->json(
      [
        'success' => false,
        'message' => 'Post not found!'
      ]
    );
    }

    /**
     * how the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      return view('posts.edit')
              ->with('categories', Category::where('entity', 'post')->get())
              ->with('post', Post::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        $postUser = PostUser::where('post_id', $id)
                  ->where('user_id', auth()->user()->id)->first();

        if(! $postUser){
          $postUser = PostUser::create(
            [
              'user_id' => auth()->user()->id,
              'post_id' => $post->id
            ]
          );

          $post->count_view++;
          $post->save();
        }

        return view('posts.public.show')
              ->with('post', $post)
              ->with('familiar', $postUser->familiar);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $postId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $post = Post::find($id);

      if($post){
        $row= PostUser::where('post_id', $post->id)->delete();
        $post->delete();
          return response()->json(['success' => true]);
      }

      return response()->json(['success' => false]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function settings()
    {
        $posts = Post::with('author')->with('category')->paginate(20);

        return view('posts.settings')
                ->with('posts', $posts)
                ->with('categories', Category::where('entity', 'post')->get());
    }

    public function search(Request $request)
    {
        $data['title'] = $request->title??null;
        $data['category'] = $request->category??null;

        return view('posts.table')
              ->with('posts', Post::filter($data)->paginate(20));
    }

    public function changeActivity(Request $request)
    {
      $post = Post::find($request->id);

      if($post){
        if($post->active){
          $post->active = 0;
        }else{
          $post->active = 1;
        }

        $post->save();

        return response()->json(['success' => true]);
      }
      return response()->json(['success' => false]);
    }

    public function setFamiliar(Request $request)
    {
      $postUser = PostUser::where('post_id', $request->id)
                ->where('user_id', auth()->user()->id)->first();
      if($postUser){
        $postUser->familiar = 1;
        $postUser->save();

        return response()->json(['success' => true]);
      }

      return response()->json(['success' => false]);
    }
}
