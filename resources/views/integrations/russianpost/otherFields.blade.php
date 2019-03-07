@php
    $senderId = 0;
@endphp
@if (!empty($senders))
    @if(count($senders) > 1)
        <div class="form-group">
            <label for="sender" class="col-lg-3 control-label required">Отправитель</label>
            <div class="col-lg-8">
                <select class="form-control" id="sender" name="sender">
                    <option value=""></option>
                    @if(empty($target_value->sender_id))
                        @php
                            $activeSender = $senders->where('active', 1)->first();
                        @endphp
                        <option value="{{$activeSender->id}}"
                                selected
                        >{{implode(' ', [$activeSender->name_last, $activeSender->name_first, $activeSender->name_middle])}}</option>
                        <input type="hidden" name="sender" value="{{$activeSender->id}}">
                        @php
                            $senderId = $activeSender->id;
                        @endphp
                    @endif
                    @if (!empty($senders) && count($senders) && !empty($target_value->sender_id))
                        @foreach($senders as $sender)
                            <option value="{{$sender->id}}"
                                    @if (!empty($target_value) && $target_value->sender_id == $sender->id)
                                    @php
                                        $senderId = $sender->id;
                                    @endphp
                                    selected
                                    @endif
                            >{{implode(' ', [$sender->name_last, $sender->name_first, $sender->name_middle])}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    @elseif(!empty($target_value) && $target_value->sender_id)
        <input type="hidden" name="sender" value="{{$target_value->sender_id}}">
        @php
            $senderId = $target_value->sender_id;
        @endphp
    @elseif(count($senders) == 1)
        <input type="hidden" name="sender" value="{{$senders[0]->id}}">
        @php
            $senderId = $senders[0]->id;
        @endphp
    @endif
@endif
<div class="text-center">
    <a href="{{route('russianpost-get-blank_7', [$order->id ,$senderId])}}" target="_blank" id="get_blank_7"
       class="btn btn-default blanks">
        <span class="fa fa-print"></span> Blank 7
    </a>
    <a href="{{route('russianpost-get-blank_113', [$order->id, $senderId])}}" target="_blank" id="get_blank_113"
       class="btn btn-default blanks">
        <span class="fa fa-print"></span> Blank 113
    </a>
    <a href="{{route('russianpost-get-blank_107', [$order->id ,$senderId])}}" target="_blank" id="get_blank_107"
       class="btn btn-default blanks">
        <span class="fa fa-print"></span> Blank 107
    </a>
    <a href="{{route('russianpost-get-sticker2', [$order->id, $senderId])}}" target="_blank" id="get_sticker2"
       class="btn btn-default blanks">
        <span class="fa fa-print"></span> Sticker 2
    </a>
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
<script src="{{ URL::asset('js/post_js/russianpost.js') }}"></script>