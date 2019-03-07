@if($target)
    {{renderTarget(json_decode($target->options), $target->target_type . '[', ']', $target->alias)}}
@endif