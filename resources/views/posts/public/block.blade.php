@if(!empty($posts))
    @foreach($posts as $post)
        <li>
            <h3 class="title">
                <a href="{{ route('posts.show', $post->id) }}">
                    {!! $post->title !!}
                </a>
            </h3>
            <div class="link-title">
                {{ $post->author->name .' '. $post->author->surname }}
            </div>
            <div class="desc">
                {!! \Illuminate\Support\Str::words($post->body, 25) !!}
            </div>
            <div class="post-time">
                Опубликовано: {{$post->created_at->format('d/m/Y H:i').'   '}}
                @if($post->count_view) <i class="fa fa-eye"></i> {{$post->count_view}} @endif
            </div>
            <div class="pull-right">
                <a href="{{route('posts.show', $post->id)}}">
                    <i class="fa fa-2x fa-arrow-circle-right"></i>
                </a>
            </div>
        </li>
    @endforeach
@endif

<div>
  {!! view('paginator', [
        'total'=>$posts->total(),
        'current_page'=> $posts->currentPage(),
        'per_page' => $posts->perPage(),
        'last_page' => $posts->lastPage(),
        'action' => 'search'
        ]) !!}
</div>
