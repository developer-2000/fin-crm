@if ($statusGroupBy->isNotEmpty())
    @foreach($statusGroupBy as $status)
        <div class="form-group">
            <label class="col-md-4 control-label" for="name">{{$status->procStatus->name ?? '-'}}</label>
            <label class="col-md-1 control-label" >{{$status->count}}</label>
            <div class="col-md-offset-2 col-md-5">
                <select name="status[{{$status->proc_status}}]" class="form-control">
                    @if ($statuses->isNotEmpty())
                        @foreach($statuses as $ownStatus)
                            <option value="{{$ownStatus->id}}">{{$ownStatus->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    @endforeach
@else
    <div class="text-center">
        Товаров не найдено
    </div>
@endif