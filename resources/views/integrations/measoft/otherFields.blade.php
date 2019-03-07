<div class="form-group">
    <label for="approve[track]" class="col-lg-3 control-label">Track</label>
    <div class="col-lg-8">
        <input class="form-control" id="approve[track]" name="approve[track]" value="{{$target_value->track ?? ''}}" disabled>
    </div>
</div>


@if ($senders->count() > 1)
    <div class="form-group">
        <label for="sender" class="col-lg-3 control-label required">Отправитель</label>
        <div class="col-lg-8">
            <select class="form-control" id="sender" name="sender">
                @if (!empty($senders) && count($senders))
                    @foreach($senders as $sender)
                        <option value="{{$sender->id}}"
                                @if (!empty($target_value) && $target_value->sender_id == $sender->id)
                                selected
                                @endif
                        >{{$sender->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
@elseif($senders->count() == 1)
    <input type="hidden"
           name="sender"
           value="{{$senders->first()->id ?? 0}}">
@else
    <p class="text-center">Нет отправителя</p>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <button type="button" class="btn btn-success @if (!empty($target_value->track) || $senders->isEmpty()) hidden @endif"
                    id="delivery_note_create">
                <span class="fa fa-save"></span> Создать Track
            </button>
            <button type="button" class="btn btn-danger @if (empty($target_value->track)) hidden @endif"
                    id="delivery_note_delete">
                <span class="fa fa-trash"></span>
            </button>
        </div>
    </div>
</div>