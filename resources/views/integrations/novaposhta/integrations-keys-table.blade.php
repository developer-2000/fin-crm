@if ($integrationsKeys->count())
    @foreach ($integrationsKeys as $key)
        <tr>
            <td>{{ $key->id }}</td>
            <td>{{ $key->key }}</td>
            <td>{{ $key->name }}</td>
            <td>@if( isset($key->subProject)){{ $key->subProject->name }}@else N/A @endif</td>
            <td>{{ $key->exp_key_date }}</td>
            <td class="text-center">
                <div class="checkbox-nice checkbox">
                    {{ Form::checkbox('activity', $key->id, $key->active == 1, ['id' => 'activate_' . $key->id, 'class' => 'activate']) }}
                    {{ Form::label('activate_' . $key->id, ' ') }}
                </div>
            </td>
        </tr>
    @endforeach
@endif