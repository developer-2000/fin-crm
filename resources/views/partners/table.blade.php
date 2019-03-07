@forelse($partners as $partner)
    <tr>
        <td>{{$partner->id}}</td>
        <td>
            @if(isset($permissions['edit_partner']))
                <a href="#" class="partner_name"
                   data-title="Выберите название"
                   data-pk="{{$partner->id}}"
                   data-name="name">
                    {{$partner->name}}</a>
            @else
                {{$partner->name}}
            @endif
        </td>
        <td>
            {{$partner->key}}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center">Нет партнеров</td>
    </tr>
@endforelse