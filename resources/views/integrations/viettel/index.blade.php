<div class="form-group">
    <label for="approve[warehouse]" class="col-lg-3 control-label required">Province
    </label>
    <div class="col-lg-8">
        <input placeholder="Province"
               id="province"
               name="approve[warehouse]"
               type="text"
               value="@if (!empty($warehouse)){{$warehouse}} @endif">
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
    <label for="approve[region]" class="col-lg-3 control-label">Ward
    </label>
    <div class="col-lg-8">
        <input placeholder="Ward"
               id="ward"
               name="approve[region]"
               type="text"
               value="@if (!empty($region)){{$region}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="street" class="col-lg-3 control-label">Receiver address</label>
    <div class="col-lg-8">
        <textarea class="form-control" placeholder="Receiver address" id="street" rows="2"
                  name="approve[street]" cols="50">@if (!empty($street)){{$street}} @endif</textarea>
    </div>
</div>
<div class="form-group">
    <label for="note" class="col-lg-3 control-label">Note(Заметка)</label>
    <div class="col-lg-8">
        <input type="text" id="note" class="form-control" name="approve[note]" value="{{!empty($note) ? $note : NULL}}">
    </div>
</div>
{{--<script>--}}
{{--$.getScript("/js/post_js/viettel.js");--}}

{{--</script>--}}
<script src="{{ URL::asset('/js/post_js/viettel.js')}}"></script>