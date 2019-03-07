@forelse($ranks as $rank)
    <tr>
        <td>{{$rank->id}}</td>
        <td>
            @if(isset($permissions['edit_rank']))
                <a href="#" class="rank_name"
                   data-title="Выберите название"
                   data-pk="{{$rank->id}}"
                   data-name="name">
                    {{$rank->name}}</a>
            @else
                {{$rank->name}}
            @endif
        </td>
        <td>
            @if ($rank->role)
                @if(isset($permissions['edit_rank']))
                    <a href="#" class="rank_role"
                       data-title="Выберите роль"
                       data-pk="{{$rank->id}}"
                       data-name="role_id"
                       data-value="{{$rank->role_id}}" >
                        {{$rank->role->name}}
                    </a>
                @else
                    {{$rank->role->name}}
                @endif
            @endif
        </td>
        <td>
            @if (isset($permissions['delete_rank']))
                <a href="#" class="table-link danger delete_rank pull-right"
                   data-title="Удалить ранг?"
                   data-pk="{{$rank->id}}"
                   data-name="delete"
                   style="border-bottom: 0;">
                                                                    <span class="fa-stack ">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                </a>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center">Нет рангов</td>
    </tr>
@endforelse