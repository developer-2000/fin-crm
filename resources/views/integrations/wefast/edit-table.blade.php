@if (count($keys))
    @foreach($keys as $key)
        <tr>
            <td>{{$key->id}}</td>
            @if (!auth()->user()->sub_project_id)
                <td>
                    <a href="#" class="key_sub_project"
                       data-title="Выберите под проект"
                       data-type="select2"
                       data-pk="{{$key->id}}"
                       data-name="sub_project_id">
                        {{$key->subProject ? $key->subProject->name : ''}}</a>
                </td>
            @endif
            <td>
                <a href="#" class="key_name"
                   data-title="Выберите название"
                   data-pk="{{$key->id}}"
                   data-name="name">
                    {{$key->name}}</a>
            </td>
            <td>
                <a href="#" class="key_token"
                   data-title="Введите токен"
                   data-pk="{{$key->id}}"
                   data-name="token">
                    {{$key->token}}</a>
            </td>
            <td>
                <div class="checkbox-nice">
                    <input type="checkbox"
                           class="status"
                           id="checkbox-{{$key->id}}"
                           data-id="{{$key->id}}"
                           @if ($key->active) checked @endif>
                    <label for="checkbox-{{$key->id}}"></label>
                </div>
            </td>
            <td>
                <a href="#" class="table-link danger delete_key pull-right"
                   data-title="Удалить ключ?"
                   data-pk="{{$key->id}}"
                   data-name="delete"
                   style="border-bottom: 0;">
                    <span class="fa-stack ">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                    </span>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="text-center">
            Ключи не нейдены
        </td>
    </tr>
@endif