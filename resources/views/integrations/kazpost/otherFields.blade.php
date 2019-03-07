{{--<div class="form-group">--}}
    {{--<label for="track" class="col-lg-3 control-label --}}{{--required--}}{{--">Track</label>--}}
    {{--<div class="col-lg-8">--}}
        {{--<input name="track" id="track" class="form-control"--}}
               {{--value="@if (!empty($target_value) && $target_value->track){{ $target_value->track }} @endif">--}}
    {{--</div>--}}
{{--</div>--}}
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
                    >{{implode(' ', [$sender->name_last, $sender->name_fm])}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="text-center">
    <button type="button" class="btn btn-default"
            id="delivery_note_create"> <span class="fa fa-print"></span>  Sticker 2
    </button>
    <button type="button" class="btn btn-default"
            id="create_blank"> <span class="fa fa-print"></span> Blank
    </button>
</div>
<br>
@php
    !empty($target_value->track2) ? $class = '' : $class = 'hidden';
        ($order->procStatus->action == 'sent') ? $disabled = 'disabled' : $disabled = '' ;
@endphp
@if(!empty($procStatuses) && !empty($procStatuses->where('action', 'to_print')->first()->id) && !empty($order) && !$order->final_target)
    <button type="button" class="btn btn-primary {{$class}} sent-to-print"
            data-proc-status="{{$procStatuses->where('action', 'to_print')->first()->id}}"> + Добавить в очередь на
        печать
    </button>
@endif
<br>
<br>
<script src="{{ URL::asset('js/post_js/kazpost.js') }}"></script>