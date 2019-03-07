<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle"
            data-toggle="dropdown" aria-expanded="false">
        Действия <span
                class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @if(!empty($printOrder->getTargetValue->track))
        <li>
            <a class="print" data-order-id="{{$printOrder->id}}"
               href="#">Печать
                ТТН</a></li>
        @endif
        @if(isset($ordersToPrintByPostCollection[0]->procStatus->id))
        <li>
            <a class="sent" style="color: green" id="sent" data-order-id="{{$printOrder->id}}"
               data-proc-status="{{$ordersToPrintByPostCollection[0]->procStatus->id}}" href="#">Отправить</a></li>
            @endif
    </ul>
</div>
<script src="{{ URL::asset('js/prints/kazpost.js')}}"></script>