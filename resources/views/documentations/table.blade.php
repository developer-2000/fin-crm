<div class="table-responsive ">
  <table class="table">
    <thead>
      <tr>
        <th><span> @lang('general.name')</span></th>
        <th class="text-center"><span> @lang('general.category')</span></th>
        <th><span> @lang('general.date-created')</span></th>
        <th class="text-center"><span> @lang('general.action')</span></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($documentations as $documentation)
        <tr id="{{ $documentation->id }}">
          <td>
            {{ $documentation->name }}
          </td>
          <td class="text-center">
            <span class="label label-success">{{ $documentation->category->name }}</span>
          </td>
          <td>{{ $documentation->created_at->format("d/m/Y H:i") }}</td>
          <td style="width: 15%;">
            @if(isset($permissions['documentations_show']))
            <a href="{{ route('documentations.show', $documentation->id) }}" class="table-link">
              <span class="fa-stack">
  <i class="fa fa-square fa-stack-2x"></i>
  <i class="fa fa-eye fa-stack-1x fa-inverse"></i>
  </span>
            </a>
          @endif
          @if(isset($permissions['documentations_edit']))
            <a href="{{ route('documentations.edit', $documentation->id) }}" class="table-link">
              <span class="fa-stack">
  <i class="fa fa-square fa-stack-2x"></i>
  <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
  </span>
            </a>
          @endif
          @if(isset($permissions['documentations_destroy']))
            <a href="#" class="delete-link table-link danger" data-url="{{ route('documentations.destroy', $documentation->id) }}">
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
        'total'=>$documentations->total(),
        'current_page'=> $documentations->currentPage(),
        'per_page' => $documentations->perPage(),
        'last_page' => $documentations->lastPage(),
        'action' => 'search'
        ]) !!}
</div>
