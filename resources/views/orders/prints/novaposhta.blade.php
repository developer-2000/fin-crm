<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle"
            data-toggle="dropdown" aria-expanded="false">
        Действия <span
                class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a target="_blank"
               href="{{route('novaposhta-delivery-note-print', $printOrder->getTargetValue->track)}}">Печать
                ТТН</a></li>
        <li>
            <a target="_blank"
               href="{{route('novaposhta-markings-print', $printOrder->getTargetValue->track)}}">Печать
                маркировки</a></li>
        <li>
            <a target="_blank"
               href="{{route('novaposhta-markings-zebra-print', $printOrder->getTargetValue->track)}}">Печать
                маркировки (Zebra)</a></li>
        @if(isset($ordersToPrintByPostCollection[0]->procStatus->id))
            <li>
                <a class="sent" style="color: green" id="sent" data-order-id="{{$printOrder->id}}"
                   data-proc-status="{{$ordersToPrintByPostCollection[0]->procStatus->id}}" href="#">Отправить</a></li>
        @endif
    </ul>
</div>
<script src="{{ URL::asset('js/prints/novaposhta.js')}}"></script>