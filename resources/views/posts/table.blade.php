<div class="table-responsive ">
  <table class="table">
    <thead>
      <tr>
        <th><span> @lang('general.name')</span></th>
        <th class="text-center"><span> @lang('general.category')</span></th>
        <th class="text-center"><span> @lang('general.author')</span></th>
        <th class="text-center"><i class="fa fa-eye"></i></th>
        <th><span> @lang('general.date-created')</span></th>
        <th><span> @lang('general.date-publish')</span></th>
        <th><span> @lang('general.active')</span></th>
        <th class="text-center"><span> @lang('general.action')</span></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($posts as $post)
        <tr id="{{ $post->id }}">
          <td>
            {{ $post->title }}
          </td>
          <td class="text-center">
            <span class="label label-success">{{ $post->category->name }}</span>
          </td>
          <td class="text-center">
            {{ $post->author->name .' '. $post->author->surname }}
          </td>
          <td class="text-center">
            {{ $post->count_view }}
          </td>
          <td>
            @if ($post->created_at)
              {{ $post->created_at->format("d/m/Y H:i") }}
            @endif
          </td>
          <td>
            @if($post->publish_at)
              {{ $post->publish_at->format("d/m/Y H:i") }}
            @endif
          </td>
          <td>
            <div class="checkbox-nice checkbox-inline">
												<input id="checkbox-inl-{{ $post->id }}"
                        @if($post->active) checked @endif
                        onclick="changeActivity('{{ $post->id }}');" type="checkbox">
												<label for="checkbox-inl-{{ $post->id }}">
												</label>
											</div>
          </td>
          <td style="width: 15%;">
            @if(isset($permissions['documentations_show']))
            <a href="{{ route('posts.show', $post->id) }}" class="table-link">
              <span class="fa-stack">
  <i class="fa fa-square fa-stack-2x"></i>
  <i class="fa fa-eye fa-stack-1x fa-inverse"></i>
  </span>
            </a>
          @endif
          @if(isset($permissions['documentations_edit']))
            <a href="{{ route('posts.edit', $post->id) }}" class="table-link">
              <span class="fa-stack">
  <i class="fa fa-square fa-stack-2x"></i>
  <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
  </span>
            </a>
          @endif
          @if(isset($permissions['documentations_destroy']))
            <a href="#" class="delete-link table-link danger" data-url="{{ route('posts.destroy', $post->id) }}">
              <span class="fa-stack">
  <i class="fa fa-square fa-stack-2x"></i>
  <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
  </span>
            </a>
          @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div>
  {!! view('paginator', [
        'total'=>$posts->total(),
        'current_page'=> $posts->currentPage(),
        'per_page' => $posts->perPage(),
        'last_page' => $posts->lastPage(),
        'action' => 'search'
        ]) !!}
</div>
