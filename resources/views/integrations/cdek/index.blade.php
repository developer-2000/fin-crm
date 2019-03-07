<div class="form-group ">
    <label class="col-lg-2 control-label" for="approve[delivery_mode]"> @lang('integrations.delivery-mode')</label>
    <div class="col-lg-8">
        <select required id="approve[delivery_mode]" name="approve[delivery_mode]" class="form-control">
            <option value=""> @lang('integrations.select-delivery-mode')</option>
            <option disabled value="" style="font-weight: bold"> Посылка (до 30 кг).</option>
            <option @if(!empty($delivery_mode) && $delivery_mode == 136) selected @endif value="136">&#160; Cклад-склад (С-С)</option>
            <option @if(!empty($delivery_mode) && $delivery_mode == 137) selected @endif  value="137">&#160; Cклад-дверь (С-Д)</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="approve[region]" class="col-lg-2 control-label required"> @lang('general.regione')
    </label>
    <div class="col-lg-8">
        <input placeholder="Область"
               id="approve[region]"
               name="approve[region]"
               type="text"
               value="@if(!empty($region)){{$region}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[city]" class="col-lg-2 control-label required"> @lang('general.city')
    </label>
    <div class="col-lg-8">
        <input placeholder=" @lang('general.city')"
               id="approve[city]"
               name="approve[city]"
               type="text"
               value="@if (!empty($city)){{$city}} @endif">
    </div>
</div>
<div class="hidden" id="block_pvz">
    <div class="form-group">
        <label for="approve[warehouse]" class="col-lg-2 control-label required"> @lang('integrations.warehouse')
        </label>
        <div class="col-lg-8">
            <input placeholder="ПВЗ"
                   id="approve[warehouse]"
                   name="approve[warehouse]"
                   type="text"
                   value="@if (!empty($warehouse)){{$warehouse}} @endif">
        </div>
    </div>
</div>
<div class="hidden" id="block_address">
    <div class="form-group"><label for="approve[street]" class="col-lg-2 control-label required"> @lang('general.street')</label>
        <div class="col-lg-8"><input class="form-control" placeholder="Улица" id="approve[street]" rows="3"
                                     name="approve[street]" type="text" value="@if($street) {{ $street }} @endif"></div>
    </div>
    <div class="form-group"><label for="approve[house]" class="col-lg-2 control-label required"> @lang('general.house')</label>
        <div class="col-lg-8"><input class="form-control" placeholder=" @lang('general.house')" id="approve[house]" rows="3"
                                     name="approve[house]" type="text" value="@if($house) {{ $house }} @endif"></div>
    </div>
    <div class="form-group"><label for="approve[flat]" class="col-lg-2 control-label"> @lang('general.flat')</label>
        <div class="col-lg-8"><input class="form-control" placeholder=" @lang('general.flat')" id="approve[flat]" rows="3"
                                     name="approve[flat]" type="text" value="@if($flat) {{ $flat }} @endif"></div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="approve[cost]" class="col-lg-2 control-label"> @lang('integrations.delivery-coming')</label>
        <div class="col-lg-3"><input class="form-control" placeholder=" @lang('integrations.delivery-coming')" id="approve[cost]"
                                     name="approve[cost]"
                                     type="number"
                                     value="{{!empty($cost) ? $cost : NULL}}"></div>
        <label for="approve[cost_actual]" class="col-lg-2 control-label"> @lang('integrations.delivery-actual')</label>
        <div class="col-lg-3"><input class="form-control" placeholder=" @lang('integrations.delivery-actual')" id="approve[cost_actual]"
                                     name="approve[cost_actual]" type="number"
                                     value="{{!empty($cost_actual) ? $cost_actual : NULL}}"></div>

    </div>
</div>

<div class="form-group"><label for="approve[track]" class="col-lg-2 control-label"> @lang('general.track')</label>
    <div class="col-lg-8"><input class="form-control" placeholder="Track" id="approve[track]"
                                 name="approve[track]" type="text" value="{{!empty($track) ? $track : NULL}}"></div>
</div>
<div class="form-group">
    <label for="note" class="col-lg-2 control-label"> @lang('general.note')</label>
    <div class="col-lg-8">
        <input type="text" id="approve[note]" class="form-control" name="approve[note]"
               value="{{!empty($note) ? $note : NULL}}">
    </div>
</div>
<div class="form-group hidden" id="calculate_cost_actual">
    <div class="main-box-body clearfix text-center">
        <button type="button" class="btn btn-success"
                id="calculate_cost_actual_button">
            <span class="fa fa-truck"></span> @lang('integrations.clculate-delivery')
        </button>
    </div>
</div>
<script>
    var url = "/js/post_js/cdek.js";
    $.getScript(url);

    @if($region)
    var region = document.getElementById('approve[region]');
    $( document ).ready(function() {
      $.ajax({
        type: 'post',
        url: '/ajax/cdek/regions/find',
        dataType: 'json',
        data: ({

        }),
      }).done(function(response) {
        $.each(response, function(i, el) {
          if (el.id == '{{ $region }}') {
              $(region).select2('data', {id:el.id,text:el.text});
              return false;
          }
        });
      }).fail(function() {
        showMessage('error', @lang('integrations.error-load-region'));
      });
    });
    @endif

    @if($city)
    var city = document.getElementById('approve[city]');
    $( document ).ready(function() {
      $.ajax({
        type: 'post',
        url: '/ajax/cdek/cities/find',
        dataType: 'json',
        data: ({
          regionCode: '{{ $region }}'
        }),
      }).done(function(response) {
        $.each(response, function(i, el) {
          if (el.id == '{{ $city }}') {
              $(city).select2('data', {id:el.id,text:el.text});
              return false;
          }
        });
      }).fail(function() {
        showMessage('error', @lang('integrations.error-load-city'));
      });
    });
    @endif

</script>
