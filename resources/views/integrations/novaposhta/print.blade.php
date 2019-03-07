<div class="form-group" style="text-align: left; padding-left:  5em">
    <a target="_blank" class="btn btn-default"
       href="{{route('novaposhta-delivery-note-print',  !empty($target_value->track) ? $target_value->track : 1)}}"><i
                class="fa fa-print"></i>
        Печать накладной</a>
    <a target="_blank" class="btn btn-default"
       href="{{route('novaposhta-markings-print', !empty($target_value->track) ? $target_value->track : 1)}}"><i
                class="fa fa-print"></i>
        Печать маркировки</a>
    <a target="_blank" class="btn btn-default"
       href="{{route('novaposhta-markings-zebra-print', !empty($target_value->track) ? $target_value->track : 1)}}"><i
                class="fa fa-print"></i>
        Печать маркировки (Zebra)</a>
    </div>

