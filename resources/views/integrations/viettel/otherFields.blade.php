<div class="form-group">
    <label for="order_note" class="col-lg-3 control-label">Order note</label>
    <div class="col-lg-8">
        <textarea class="form-control" placeholder="" id="order_note" rows="2"
                  name="order_note"
                  cols="50">@if (isset($integrationKeys)) @if(isset($target_value->sender_id) && isset($integrationKeys[0]->id) && $integrationKeys[0]->id == $target_value->sender_id) @if(isset($integrationKeys[0]->senders) && $integrationKeys[0]->senders->count() == 1 ) @if(isset($integrationKeys[0]->senders[0])) {{!empty($integrationKeys[0]->senders[0]->order_note) ? $integrationKeys[0]->senders[0]->order_note : ''}} @endif @endif @else @if(isset($integrationKeys[0]->senders) && $integrationKeys[0]->senders->count() == 1 ) {{!empty($integrationKeys[0]->senders[0]->order_note) ? $integrationKeys[0]->senders[0]->order_note : ""}} @endif @endif @endif </textarea>
    </div>
</div>
{{--<div class="form-group">--}}
{{--<label for="address" class="col-lg-3 control-label">Receiver address</label>--}}
{{--<div class="col-lg-8">--}}
{{--<textarea class="form-control" placeholder="Receiver address" id="address" rows="2"--}}
{{--name="address" cols="50"></textarea>--}}
{{--</div>--}}
{{--</div>--}}
<div class="form-group">
    <label for="products_description" class="col-lg-3 control-label required">Products description</label>
    <div class="col-lg-8">
        <textarea class="form-control" placeholder="Products description" id="products_description" rows="3"
                  name="products_description" data-product-description = "1"
                  cols="50">@if(isset($products)) @foreach($products as $product){{ $product->title .'('.$product->countProducts.')'}}, @endforeach @endif</textarea>
    </div>
</div>
<input id="products_count" name="products_count" type="hidden" class="form-control"
       value="{{$offers->count()}}">
<input id="offers" name="offers" type="hidden" class="form-control"
       value="{{$offers}}">
<input id="price_total" name="price_total" type="hidden" class="form-control"
       value="{{$order->price_total}}">
<div class="form-group ">
    <label class="col-lg-3  control-label required" for="product_weight">WEIGHT</label>
    <div class="col-lg-8">
        <input type="text" id="product_weight" name="product_weight" class="product_weight form-control">
    </div>
</div>
{{--<div class="form-group ">--}}
{{--<label class="col-lg-3  control-label" for="product_length">LENGTH</label>--}}
{{--<div class="col-lg-8">--}}
{{--<input type="text" id="product_length" name="product_length" class="product_length form-control">--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="form-group ">--}}
{{--<label class="col-lg-3  control-label" for="product_width">WIDTH</label>--}}
{{--<div class="col-lg-8">--}}
{{--<input type="text" id="product_width" name="product_width" class="product_width form-control">--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="form-group ">--}}
{{--<label class="col-lg-3  control-label" for="product_height">HEIGHT</label>--}}
{{--<div class="col-lg-8">--}}
{{--<input type="text" id="product_height" name="product_height" class="product_height form-control">--}}
{{--</div>--}}
{{--</div>--}}

<div class="form-group ">
    <label class="col-lg-3 control-label" for="sender">Отправитель</label>
    <div class="col-lg-8">
        <select required id="sender" name="sender" class="form-control">
            @if (isset($integrationKeys))
                @if($integrationKeys->count() == 1)
                    @foreach( $integrationKeys as $key)
                        <option selected readonly="" value="{{$key->id}}"
                                data-value="{{$key->id}}">{{$key->name}}</option>
                    @endforeach
                @else
                    <option value="">Выберите отправителя</option>
                    @foreach( $integrationKeys as $key)
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
<div class="form-group ">
    <div class="sender-block">
        <label class="col-lg-3 control-label" for="sender_warehouse">Склад отправителя</label>
        <div class="col-lg-8">
            <select required id="sender_warehouse" name="sender_warehouse" class="form-control">
                @if (isset($integrationKeys))
                    @foreach($integrationKeys as $key)
                        @if(isset($target_value->sender_id) && $key->id == $target_value->sender_id)
                            @if(isset($integrationKeys[0]->senders) && $integrationKeys[0]->senders->count() == 1 )
                                @foreach( $integrationKeys[0]->senders as $sender)
                                    <option selected readonly="" value="{{$sender->id}}"
                                            data-value="{{$sender->id}}">{{$sender->name}}</option>
                                @endforeach
                            @endif
                        @else
                            @if(isset($integrationKeys[0]->senders) && $integrationKeys[0]->senders->count() == 1 )
                                @foreach( $integrationKeys[0]->senders as $sender)
                                    <option selected readonly="" value="{{$sender->id}}"
                                            data-value="{{$sender->id}}">{{$sender->name}}</option>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    @php
        use Carbon\Carbon as Carbon;
    @endphp
    <label class="col-lg-3 control-label" for="datetimepicker"> Дата отправки </label>
    <div class="col-lg-5">
        <input id="datetimepicker" name="approve[date]" type="text" class="form-control"
               value="{{!empty($field_values->date->field_value) ? $field_values->date->field_value : Carbon::now()->addHours(4)->format('d/m/Y H:m:s')}}">
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
        </div>
    @endif
</div>
<div class="form-group ">
    <label class="col-lg-3  control-label" for="deliveryCost">Delivery Cost</label>
    <div class="col-lg-3">
        <input type="text" id="deliveryCost" name="approve[cost_actual]"
               value="{{!empty($field_values->cost_actual->field_value) ? $field_values->cost_actual->field_value : NULL}}"
               readonly
               class="deliveryCost form-control">
    </div>
</div>
