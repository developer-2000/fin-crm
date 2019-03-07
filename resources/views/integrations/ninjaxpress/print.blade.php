<div class="form-group" style="text-align: left; padding-left:  5em">
    <a target="_blank" class="btn btn-default"
       href="{{route('ninjaxpress-delivery-note-print', [
       !empty($target_value->sender_id) ? $target_value->sender_id : 0,
       !empty($target_value->track) ? $target_value->track : 1
       ])}}"><i class="fa fa-print"></i> Print track</a>
</div>

