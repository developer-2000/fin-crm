<label class="col-lg-3 control-label required" for="approve[timezone]">Timezone</label>
<div class="col-lg-8">
    <select required id="approve[timezone]" name="approve[timezone]" class="form-control">
        <option value="">Choose timezone</option>
        @if(!empty(config('integrations.ninjaxpress_timezones')))
            @foreach (config('integrations.ninjaxpress_timezones') as $key =>$value)
                <option value="{{$key}}"
                        @if(isset($timezone) && $key == $timezone ) selected @endif>{{ $value }}</option>
            @endforeach
        @endif
    </select>
</div>