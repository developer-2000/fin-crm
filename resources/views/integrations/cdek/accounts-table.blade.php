@foreach ($keys as $key)
    <tr>
        <td>{{ $key->id??null }}</td>
        <td>{{ $key->subProject->name??null }}</td>
        <td>{{ $key->name??null }}</td>
        <td>
            <div class="checkbox-nice">
                <input type="checkbox" class="activate_account" name="activity"
                       id="{{$key->id}}"
                       @if ($key->active) checked="checked" @endif>
                <label for="{{$key->id}}">
                </label>
            </div>
        </td>
        <td>{{ $key->account }}</td>
        <td>
            <a href="{{ route('cdek-edit-key', $key->id) }}" class="table-link">
                    <span class="fa-stack">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
            </a>
        </td>
    </tr>
@endforeach
