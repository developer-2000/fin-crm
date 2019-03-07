<label for="operator"> @lang('general.select-operator')</label>
<select required class="form-control" name="operator"
        id="operator">
    @if(!empty($operators))
        <option value=""> @lang('general.select-operator')</option>
        @foreach($operators as $key=>$operator)
            <option
                    value="{{$operator->id}}">{{$operator->name . ' ' . $operator->surname }}</option>
        @endforeach
    @endif
</select>
