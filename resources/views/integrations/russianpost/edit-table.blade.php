@if ($senders->isNotEmpty())
    @foreach($senders as $sender)
        <tr>
            <td>
                {{$sender->id}}
            </td>
        @if (!auth()->user()->sub_project_id)
                <td>
                    {{$sender->subProject ? $sender->subProject->name : ''}}
                </td>
        @endif
            <td>
                {{implode(' ', [$sender->name_last, $sender->name_first, $sender->name_middle])}}
            </td>
            <td>
                <a href="{{ route('russianpost-edit-sender', $sender->id) }}" class="table-link">
                    <span class="fa-stack">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="@if (!auth()->user()->sub_project_id) 4 @else 3 @endif" class="text-center">Нет отправителей</td>
    </tr>
@endif