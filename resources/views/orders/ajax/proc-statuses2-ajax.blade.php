<br>
<br>
<div class="form-group">
    <div class="col-lg-3">
        <label class="control-label" for="procStatus2"> Изменить
            подстатус </label></div>
    <div class="col-lg-8">
        <select name="proc_status2" id="procStatus2" class="form-control">
            <option value="">Выберите</option>
            @if ($procStatuses2)
                @foreach($procStatuses2 as $procStatus2)
                    <option value="{{$procStatus2->id}}"
                            @if ($procStatus2->id == $orderOne->proc_status2) selected @endif>{{$procStatus2->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<br>