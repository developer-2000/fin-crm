<hr>
<div class="form-group ">
    <label class="col-lg-3 control-label" for="sender">Отправитель</label>
    <div class="col-lg-8">
        <select required id="sender" name="sender" class="form-control">
            @if (isset($integrationKeys))
                    <option value=""> @lang('integrations.select-sender')</option>
                    @foreach( $integrationKeys as $key)
                            <option value="{{$key->id}}"
                              @if(isset($target_value) && $target_value->sender_id == $key->id) selected @endif
                            data-value="{{$key->id}}">{{ $key->name }}</option>
                    @endforeach

            @endif
        </select>
    </div>
</div>

<div class="form-group ">
    <label class="col-lg-3 control-label" for="sender"> @lang('integrations.select-services')</label>

    <div class="col-lg-8">
      <label for="additional_service_30"> @lang('integrations.fitting-home')</label>
      <input type="checkbox" id="additional_service_30" name="additional_service_30">

      <label for="additional_service_36"> @lang('integrations.partial-delivery')</label>
      <input type="checkbox" id="additional_service_36" name="additional_service_36">

      <label for="additional_service_37"> @lang('integrations.attachment-inspection')</label>
      <input type="checkbox" id="additional_service_37" name="additional_service_37">


    </div>
</div>

<div class="form-group ">
    <label class="col-lg-3  control-label" for="weight"> @lang('integrations.general-weight')</label>
    <div class="col-lg-8">
        <input type="number" id="weight" name="weight" step="0.01" class="weight form-control">
    </div>
    <div class="col-lg-1">кг</div>
</div>

<div class="form-group ">
    <label class="col-lg-3  control-label" for="deliveryCost"> @lang('integrations.delivery-cost')</label>
    <div class="col-lg-3">
        <input type="text" id="deliveryCost" name="approve[cost_actual]"
               value="{{!empty($field_values->cost_actual->field_value) ? $field_values->cost_actual->field_value : NULL}}"
               @if (!empty($field_values->cost_actual->field_settings->range_min))
               min="{{$field_values->cost_actual->field_settings->range_min}}"
               @endif
               @if (!empty($field_values->cost_actual->field_settings->range_max))
               max="{{$field_values->cost_actual->field_settings->range_max}}"
               @endif
               class="deliveryCost form-control">
    </div>
</div>
<div class="form-group">
    <label for="products_description" class="col-lg-2 control-label required"> @lang('integrations.products-description')</label>
    <div class="col-lg-8">
        <textarea class="form-control" placeholder=" @lang('integrations.products-description')" id="products_description" rows="3"
                  name="products_description" data-product-description = "1"
                  cols="50">@if(isset($products)) @foreach($products as $product){{ $product->title .'('.$product->countProducts.')'}}, @endforeach @endif</textarea>
    </div>
</div>

<input id="products_count" name="products_count" type="hidden" class="form-control"
       value="@if(isset($offers)) {{$offers->count()}} @endif">
<input id="offers" name="offers" type="hidden" class="form-control"
       value="@if(isset($offers)) {{$offers}} @endif">
<input id="price_total" name="price_total" type="hidden" class="form-control"
       value="@if(isset($order->price_total)) {{$order->price_total}} @endif">
<hr>
<div class="form-group ">
    <label class="col-lg-3  control-label required" for="product_weight"> @lang('integrations.package-dimensions'):</label>
    <div class="col-lg-8">
        <input type="text" id="product_weight" name="product_weight" class="product_weight form-control">
    </div>
</div>

<div class="form-group">
    <label class="col-lg-3 control-label" for="datetimepicker"> @lang('integrations.departure-date') </label>
    <div class="col-lg-5">
        <input id="datetimepicker" name="approve[date]" type="text" class="form-control"
               value="{{!empty($field_values->date->field_value) ? $field_values->date->field_value : \Carbon\Carbon::now()->addHours(4)->format('d/m/Y H:m:s')}}">
    </div>
</div>
@php
    $integrationClass = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($target_option['approve']->alias ?? '');
@endphp

<div class="form-group">
    <label class="col-lg-3  control-label" for="track">Track</label>
    <div class="col-lg-3">
        <input type="text" id="trackVal" name="approve[track]"
               value="{{!empty($target_value->track) ? $target_value->track : NULL}}" disabled
               class="track form-control">
    </div>
        <div class="col-lg-3">
            <div class="form-group">
                <div class="main-box-body clearfix text-center">
                    @if($integrationClass::CREATE)
                        <button type="button" class="btn btn-success"
                                id="delivery_note_create">
                            <span class="fa fa-save"></span> @lang('integrations.track-create')
                        </button>
                    @endif
                    @if($integrationClass::EDIT)
                        <button type="button" class="btn btn-warning"
                                id="delivery_note_edit">
                            <span class="fa fa-edit"></span>
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
            @if($target_value->track)
            <div class="form-group">
                <div class="main-box-body clearfix text-center">
                    <div class="poshta-data">
                        <div class="print_note">
                            {!! view('integrations.cdek.print', ['target_value' => $target_value]) !!}
                        </div>
                    </div>
                </div>
            </div>
          @endif
        </div>
</div>
