@if ($counterparties->isNotEmpty())
    @foreach($counterparties as $counterparty)
        <tr>
            <td>{{$counterparty->id}}</td>
            <td>{{$counterparty->key ? $counterparty->key->name : ''}}</td>
            <td>{{$counterparty->sender}}</td>
            <td>{{$counterparty->contact}}</td>
            <td>{{$counterparty->phone}}</td>
            <td>{{$counterparty->address}}</td>
            <td>{{$counterparty->warehouse}}</td>
            <td class="text-center">
                <div class="checkbox-nice col-md-offset-3">
                    <input type="checkbox"
                           class="status_conterparty"
                           id="active-{{$counterparty->id}}"
                           data-id="{{$counterparty->id}}"
                           @if ($counterparty->active) checked @endif>
                    <label for="active-{{$counterparty->id}}"></label>
                </div>
            </td>
            <td>
                <a href="{{ route('wefast-counterparties-edit', $counterparty->id) }}" class="table-link">
                    <span class="fa-stack">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
                </a>
            </td>
            <td>
                <a href="#" class="table-link danger delete_counterparty pull-right"
                   data-title="Удалить контрагента?"
                   data-pk="{{$counterparty->id}}"
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
        <td class="text-center" colspan="7">Нет контрагентов</td>
    </tr>
@endif