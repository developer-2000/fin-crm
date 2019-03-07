<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public static function search($words)
    {
        foreach ($words as $word) {
            $posts[] = Post::where([['title', 'like', '%' . $word . ' %'], ['active', 1], ['publish_complete', 1]])
                ->orWhere([['body', 'like', '%' . $word . '%'], ['active', 1], ['publish_complete', 1]])->with('author', 'postCategory')->withCount('postsUsers')
                ->first();
            foreach ($posts as $post) {

                $post->title = str_replace($word, "<span class='highlight'>$word</span>", $post->title);
                $post->body = str_replace($word, "<span class='highlight'>$word</span>", $post->body);
            }
        }
        return $posts;
    }
}