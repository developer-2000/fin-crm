@foreach ($keys as $key)
    <tr>
        <td>{{ $key->id }}</td>
        <td>{{ $key->subProject->name }}</td>
        <td>{{ $key->name }}</td>
        <td>{{ $key->user_name }}</td>
        <td>
            <div class="checkbox-nice">
                <input type="checkbox" class="activate_account" name="activity"
                       id="{{$key->id}}"
                       @if ($key->active) checked="checked" @endif>
                <label for="{{$key->id}}">
                </label>
            </div>
        </td>
        <td>{{ $key->user_name }}</td>
        <td class="txtOver">{{ mb_strimwidth($key->token_key, 0, 20, "...") }}</td>
        <td>
            <a href="{{ route('viettel-edit-key', $key->id) }}" class="table-link">
                    <span class="fa-stack">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
            </a>
        </td>
    </tr>
@endforeach