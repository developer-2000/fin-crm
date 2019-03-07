<div class="form-group">
    <a target="_blank" class="btn btn-default"
       href="{{route('cdek-print',  !empty($target_value->track) ? $target_value->track : 1)}}"><i
                class="fa fa-print"></i>
        @lang('integrations.invoice-printing')</a>
</div>
