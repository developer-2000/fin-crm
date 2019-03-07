<div class="form-group">
    <label for="approve[region]" class="col-lg-3 control-label required">Province</label>
    <div class="col-lg-8">
        <input placeholder="Province"
               id="province"
               name="approve[region]"
               type="text"
               value="@if (!empty($region)){{$region}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[district]" class="col-lg-3 control-label required">District</label>
    <div class="col-lg-8">
        <input placeholder="District"
               id="district"
               name="approve[district]"
               type="text"
               value="@if (!empty($district)){{$district}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[note]" class="col-lg-3 control-label required">Address</label>
    <div class="col-lg-8">
        <textarea class="form-control" placeholder="Address" id="approve[note]" rows="3"
                                    name="approve[note]" cols="50">@if (!empty($note)){{$note}} @endif</textarea>
    </div>
</div>
<div class="form-group">
    <label for="approve[cost_actual]" class="col-lg-3 control-label">Total shipping</label>
    <div class="col-lg-8">
        <input class="form-control"  type="number" id="approve[cost_actual]" name="approve[cost_actual]" @if (!empty($cost_actual)) value="{{$cost_actual}}" @endif
        @if (!empty($target_config->cost->field_settings->range_min))
        min="{{$target_config->cost->field_settings->range_min}}"
               @endif
               @if (!empty($target_config->cost->field_settings->range_max))
               max="{{$target_config->cost->field_settings->range_max}}"
                @endif>
    </div>
</div>
<div class="form-group">
    <label for="approve[track]" class="col-lg-3 control-label">Track</label>
    <div class="col-lg-8">
        <input class="form-control" id="approve[track]" name="approve[track]" @if (!empty($track)) value="{{$track}}" @endif>
    </div>
</div>

<script src="{{ URL::asset('js/post_js/wefast.js') }}"></script>