@foreach ($keys as $key)
    <tr>
        <td>{{ $key->id }}</td>
        <td>{{ $key->subProject->name }}</td>
        <td>{{ $key->name }}</td>
        <td>{{ $key->email }}</td>
        <td>
            <div class="checkbox-nice">
                <input type="checkbox" class="activate_key" name="activity"
                       id="{{$key->id}}"
                       @if ($key->active) checked="checked" @endif>
                <label for="{{$key->id}}">
                </label>
            </div>
        </td>
        <td>{{ $key->client_id }}</td>
        <td>{{ $key->client_secret }}</td>
        <td>
            <a href="{{ route('ninjaxpress-edit-key', $key->id) }}" class="table-link">
                    <span class="fa-stack">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
            </a>
        </td>
    </tr>
@endforeach