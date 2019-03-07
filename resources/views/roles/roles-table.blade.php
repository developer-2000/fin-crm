@if ($roles)
    @foreach($roles as $role)
        <tr>
            <td>
                {{$role->name}}
            </td>
        </tr>
    @endforeach
@endif