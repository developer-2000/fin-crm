<hr>
<div class="form-group">
    <label class="col-lg-3 control-label" for="sender">Отправитель</label>
    <div class="col-lg-8">
        <select required id="sender" name="sender" class="form-control">
            @if (isset($keys))
                @if($keys->count() == 1)
                    @foreach( $keys as $key)
                        <option selected readonly="" value="{{$key->id}}"
                                data-value="{{$key->id}}">{{$key->name}}</option>
                    @endforeach
                @else
                    <option value="">Выберите отправителя</option>
                    @foreach( $keys as $key)
                        <option value="{{$key->id}}"
                                @if(isset($target_value->sender_id) && $target_value->sender_id == $key->id)
                                selected
                                @endif
                                data-value="{{$key->id}}">{{$key->name}}</option>
                    @endforeach
                @endif
            @endif
        </select>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
    </div>
    <div class="col-lg-8">
        <h2> Pickup details</h2>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label" for="pickup_date">Pickup date</label>
    <div class="col-lg-2">
        <input id="pickup_date" name="approve[pickup_date]" type="text" class="form-control"
               value="{{!empty($field_values->pickup_date->field_value) ? $field_values->pickup_date->field_value : \Carbon\Carbon::now()->toDateString()}}">
    </div>
    <div class="col-lg-3">
        <label class="col-lg-5 control-label" for="pickup_time_min">Time start </label>
        <div class="col-lg-7">
            <input readonly id="pickup_time_min" name="approve[pickup_time_min]" type="text" class="form-control"
                   value="{{!empty($field_values->pickup_time_min->field_value) ? $field_values->pickup_time_min->field_value:  "09:00"}}">
        </div>
    </div>
    <div class="col-lg-3">
        <label class="col-lg-5 control-label" for="pickup_time_max">Time end </label>
        <div class="col-lg-7">
            <input readonly id="pickup_time_max" name="approve[pickup_time_max]" type="text" class="form-control"
                   value="{{!empty($field_values->pickup_time_max->field_value) ? $field_values->pickup_time_max->field_value : "22:00"}}">
        </div>
    </div>
</div>
<div class="form-group">
    <label for="pickup_instructions" class="col-lg-3 control-label">Pickup instruction</label>
    <div class="col-lg-8">
        <input type="text" id="pickup_instructions" class="form-control" name="approve[pickup_instructions]"
               value="{{!empty($field_values->pickup_instructions->field_value) ? $field_values->pickup_instructions->field_value : ''}}">
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
    </div>
    <div class="col-lg-8">
        <h2> Delivery details</h2>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label" for="delivery_date">Delivery date</label>
    <div class="col-lg-2">
        <input id="delivery_date" name="approve[delivery_date]" type="text" class="form-control"
               value="{{!empty($field_values->delivery_date->field_value) ? $field_values->delivery_date->field_value  : \Carbon\Carbon::now()->toDateString() }}">
    </div>
    <div class="col-lg-3">
        <label class="col-lg-5 control-label" for="delivery_time_min">Time start </label>
        <div class="col-lg-7">
            <input readonly id="delivery_time_min" name="approve[delivery_time_min]" type="text" class="form-control"
                   value="{{!empty($field_values->delivery_time_min->field_value) ? $field_values->delivery_time_min->field_value : "09:00"}}">
        </div>
    </div>
    <div class="col-lg-3">
        <label class="col-lg-5 control-label" for="delivery_time_max">Time end </label>
        <div class="col-lg-7">
            <input readonly id="delivery_time_max" name="approve[delivery_time_max]" type="text" class="form-control"
                   value="{{!empty($field_values->delivery_time_max->field_value) ? $field_values->delivery_time_max->field_value : "22:00"}}">
        </div>
    </div>
</div>
<div class="form-group">
    <label for="delivery_instructions" class="col-lg-3 control-label">Delivery instruction</label>
    <div class="col-lg-8">
        @php
            if($keys->count() == 1)
                $deliveryInstruction = $keys->first()->delivery_instraction;
        @endphp
        <input type="text" id="delivery_instructions" class="form-control" name="approve[delivery_instructions]"
               value="{{isset($deliveryInstruction) && !empty($deliveryInstruction) ? $deliveryInstruction: ''}}">
    </div>
</div>

@php
    $integrationClass = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($target_option['approve']->alias ?? '');
@endphp
<div class="form-group">
    <label class="col-lg-3  control-label" for="trackVal">Track</label>
    <div class="col-lg-3">
        <input type="text" id="trackVal" name="approve[track]"
               value="{{!empty($target_value->track) ? $target_value->track : NULL}}" readonly
               class="track form-control">
    </div>
    @if($target_option['approve'] && $target_option['approve']->integration && $integrationClass && ($integrationClass::CREATE || $integrationClass::EDIT || $integrationClass::DELETE))
        <div class="col-lg-3">
            <div class="form-group">
                <div class="main-box-body clearfix text-center">
                    @if($integrationClass::CREATE && !$order->locked )
                        <button type="button" class="btn btn-success"
                                id="delivery_note_create">
                            <span class="fa fa-save"></span> Создать Track
                        </button>
                    @endif
                    @if($integrationClass::DELETE)
                        <button type="button" class="btn btn-danger"
                                id="delivery_note_delete">
                            <span class="fa fa-trash"></span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
<br>
{{--<div class="form-group">--}}
    {{--<div class="main-box-body clearfix text-center">--}}
        {{--<div class="poshta-data">--}}
            {{--<div class="print_note hidden">--}}
                {{--@include('integrations.ninjaxpress.print', ['target_value' => $target_value])--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
