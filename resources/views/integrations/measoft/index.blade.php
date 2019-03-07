<div class="form-group">
    <label for="approve[date]" class="col-lg-3 control-label required">Дата доставки</label>
    <div class="col-lg-8">
        <input placeholder="Дата доставки"
               id="approve[date]"
               name="approve[date]"
               type="text"
               class="form-control"
               value="@if (!empty($date)){{$date}} @endif" readonly>
    </div>
</div>

<div class="form-group">
    <label for="approve[time_min]" class="col-lg-3 control-label">Время от</label>
    <div class="col-lg-8">
        <input class="form-control"
               placeholder="Время от"
               id="approve[time_min]"
               name="approve[time_min]"
               type="text" value="@if (!empty($time_min)){{$time_min}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[time_max]" class="col-lg-3 control-label">Время до</label>
    <div class="col-lg-8">
        <input class="form-control"
               placeholder="Время до"
               id="approve[time_max]"
               name="approve[time_max]"
               type="text" value="@if (!empty($time_max)){{$time_max}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[postal_code]" class="col-lg-3 control-label">Индекс</label>
    <div class="col-lg-8">
        <input class="form-control"
               placeholder="Индекс"
               id="approve[postal_code]"
               name="approve[postal_code]"
               type="text" value="@if (!empty($postal_code)){{$postal_code}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[city]" class="col-lg-3 control-label required">Город</label>
    <div class="col-lg-8">
        <input placeholder="Город"
               id="approve[city]"
               name="approve[city]"
               type="text" value="@if (!empty($city)){{$city}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[street]" class="col-lg-3 control-label required">Адрес</label>
    <div class="col-lg-8">
        <input class="form-control"
               placeholder="Адрес"
               id="approve[street]"
               name="approve[street]"
               type="text" value="@if (!empty($street)){{$street}} @endif">
    </div>
</div>
<div class="form-group">
    <label for="approve[cost]" class="col-lg-3 control-label required">Сумма доставки</label>
    <div class="col-lg-8">
        <input class="form-control"
               placeholder="Сумма доставки"
               id="approve[cost]"
               name="approve[cost]"
               type="number" value="@if (!empty($cost)){{$cost}} @endif"
               @if (!empty($target_config->cost->field_settings->range_min))
               min="{{$target_config->cost->field_settings->range_min}}"
               @endif
               @if (!empty($target_config->cost->field_settings->range_max))
               max="{{$target_config->cost->field_settings->range_max}}"
                @endif>
    </div>
</div>
<div class="form-group">
    <label for="approve[note]" class="col-lg-3 control-label required">Вложение</label>
    <div class="col-lg-8">
        <input class="form-control"
               placeholder="Вложение"
               id="approve[note]"
               name="approve[note]"
               type="text" value="@if (!empty($note)){{$note}} @endif">
    </div>
</div>
<script src="{{ URL::asset('js/vendor/typeahead.jquery.min.js')}}"></script>
<script src="{{ URL::asset('js/post_js/measoft.js')}}"></script>
<link type="text/css" rel="stylesheet" href="{{URL::asset('css/typeahead.css')}}">
<style>
    .typeahead.dropdown-menu {
        padding-right: 0 !important;
        padding-left: 0 !important;
        border: none;
    }
    .open .typeahead.dropdown-menu{
        border: 1px solid #ccc;
    }
    .typeahead.dropdown-menu li a {
        padding-left: 5px;
    }
</style>