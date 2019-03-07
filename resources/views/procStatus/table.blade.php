@if ($statuses)
    @foreach($statuses as $status)
        <tr>
            <td  class="text-center">
                @if ($status->project)
                    @if(isset($permissions['edit_status']))
                        <a href="#" class="edit_status"
                           data-title="Выберите проект"
                           data-type="select2"
                           data-pk="{{$status->id}}"
                           data-name="project">
                            {{$status->project->name}}</a>
                    @else
                        {{$status->project->name}}
                    @endif
                @elseif( $status->project_id == 0)
                    Системный статус
                @else
                    Проект не найден
                @endif
            </td>
            <td  class="text-center">
                @if (!$status->locked)
                    <a href="#" class="edit_status"
                       data-title="Выберите название"
                       data-pk="{{$status->id}}"
                       data-name="name">
                        {{$status->name}}</a>
                @else
                    {{$status->name}}
                @endif
            </td>
            <td  class="text-center">
                @if ($status->type == \App\Models\ProcStatus::TYPE_CALL_CENTER)
                    Call Center
                @elseif($status->type == \App\Models\ProcStatus::TYPE_SENDERS)
                    Отправители
                @else
                    Не определен
                @endif
            </td>
            <td class="text-center">
                <input type="color" name="color" class="change_color" @if (!isset($permissions['edit_status']) && $status->locked) disabled @endif data-pk="{{$status->id}}" value="{{$status->color}}">
            </td>
            <td  class="text-center">
                <div class="sub-statuses">
                    @if ($status->subStatuses->isNotEmpty())
                        @foreach($status->subStatuses as $subStatus)
                            <div style="padding-bottom: 10px">

                                @if (!$status->locked)
                                    <a href="#" class="edit_status"
                                       data-title="Выберите название"
                                       data-pk="{{$subStatus->id}}"
                                       data-name="name">
                                        {{$subStatus->name}}</a>
                                @else
                                    {{$subStatus->name}}
                                @endif

                                @if (!$subStatus->locked && isset($permissions['delete_status']))
                                    <a href="#" class="table-link danger delete_status del_sub_status"
                                       data-title="Удалить подстатус?"
                                       data-pk="{{$subStatus->id}}"
                                       data-name="delete"
                                       style="border-bottom: 0"></a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                    @if (!$status->locked && isset($permissions['edit_status']))
                        <a href="#" class="table-link add_sub_status"
                           data-title="Добавление подстатуса"
                           data-pk="{{$status->id}}"
                           data-name="add"
                           style="border-bottom: 0;color: #1ABC9C"></a>
                    @endif
                </div>
            </td>
            <td  class="text-center">
                @if (!empty($status->action))
                    {{$status->name}}
                    @else
                    {{'N/A'}}
                @endif
            </td>
            <td  class="text-center">
                @if (!$status->locked && isset($permissions['delete_status']))
                    <a href="#" class="table-link danger delete_status"
                       data-title="Удалить статус?"
                       data-pk="{{$status->id}}"
                       data-name="delete"
                       style="border-bottom: 0"></a>
                @endif
            </td>
        </tr>
    @endforeach
@endif