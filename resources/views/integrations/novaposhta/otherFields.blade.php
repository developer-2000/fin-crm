<hr>
<div class="poshta-data">
    <div class="form-group ">
        <label class="col-lg-3 control-label"
               for="payer-type">Плательщик</label>
        <div class="col-lg-8">
            <select disabled id="payer-type" name="payer-type"
                    class="form-control ">
                <option selected value="recipient">Получатель</option>
            </select>
        </div>
    </div>
    <div class="form-group ">
        <label class="col-lg-3 control-label" for="sender">Отправитель</label>
        <div class="col-lg-8">
            <select required id="sender" name="sender" class="form-control">
                @if (isset($integrationKeys))
                    @if($integrationKeys->count() == 1)
                        @foreach( $integrationKeys as $key)
                            <option selected readonly="" value="{{$key->id}}"
                                    data-value="{{$key}}">{{json_decode($key->contacts, true)['full_name']}}</option>
                        @endforeach
                    @else
                        <option value="">Выберите отправителя</option>
                        @foreach( $integrationKeys as $key)
                            @if($key->active)
                                <option value="{{$key->id}}" @if($target_value->sender_id == $key->id) selected @endif
                                data-value="{{$key}}">{{json_decode($key->contacts, true)['full_name']}}</option>
                            @elseif(!$key->active && $target_value->sender_id == $key->id)
                                <option readonly value="{{$key->id}}" @if($target_value->sender_id == $key->id) selected @endif
                                data-value="{{$key}}">{{json_decode($key->contacts, true)['full_name']}}</option>
                            @endif
                        @endforeach
                    @endif
                @endif
            </select>
        </div>
    </div>
    <div class="form-group ">
        @php
            if(isset($integrationKeys) && $integrationKeys->count() == 1){
                $size = floatval($integrationKeys[0]->size);
            }
        @endphp
        <label class="col-lg-3  control-label" for="volume_general">Обьемный вес</label>
        <div class="col-lg-8">
            <input type="number" step="0.001" id="volume_general" name="volume_general"
                   class="volume_general form-control" value="{{!empty($size) ? $size : 0}}">
        </div>
        <div class="col-lg-1">м.куб</div>
    </div>
    <div class="form-group ">
        <label class="col-lg-3  control-label" for="weight">Фактический вес</label>
        <div class="col-lg-8">
            <input type="number" id="weight" name="weight" step="0.01" class="weight form-control">
        </div>
        <div class="col-lg-1">кг</div>
    </div>
    <div class="form-group ">
        @php
            $description = '';
                if(isset($integrationKeys)){
                 if($integrationKeys->count() == 1){
                    if(!empty($integrationKeys[0]->description)){
                 $description = $integrationKeys[0]->description;
                 }
                }elseif ($target_value->sender_id){
                 $npKey = \App\Models\Api\NovaposhtaKey::where('id', $target_value->sender_id)->first();
                 if($npKey){
                 $description = $npKey->description;
                 };
                }
            }
        @endphp
        <label class="col-lg-3  control-label" for="description">Полное описание отправки</label>
        <div class="col-lg-8">
            <input id="description" name="description"
                   class="description form-control" value="{{!empty($description) ?  $description : ''}}">
        </div>
    </div>
    <div class="form-group ">
        <label class="col-lg-3  control-label" for="description">Дополнительная информация об отправке</label>
        <div class="col-lg-8">
            <textarea id="add_information" name="add_information"
                      class="add_information form-control" data-product-description="1"
                      rows="4">@if(isset($products)) @foreach($products as $product){{ $product->title .'('.$product->countProducts.')'}} @if(!empty($product->comment)){{'('. trim($product->comment).'),'}}@endif {{','}} @endforeach @endif
            </textarea>
        </div>
    </div>
    <div class="form-group ">
        <label class="col-lg-3 control-label" for="datetimepicker"> Дата отправки </label>
        <div class="col-lg-5">
            <input id="datetimepicker" name="approve[date]" type="text" class="form-control"
                   value="{{!empty($field_values->date->field_value) ? $field_values->date->field_value : now()->format('d.m.Y')}}">
        </div>
    </div>
    @php
        $integrationClass = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($target_option['approve']->alias ?? '');
    @endphp
    <div class="form-group ">
        <label class="col-lg-3  control-label" for="track">Track</label>
        <div class="col-lg-3">
            <input type="text" id="trackVal" name="approve[track]"
                   value="{{!empty($target_value->track) ? $target_value->track : NULL}}" disabled
                   class="track form-control">
        </div>
        @if($target_option['approve'] && $target_option['approve']->integration && !$order->locked && $integrationClass && ($integrationClass::CREATE || $integrationClass::EDIT || $integrationClass::DELETE))
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="main-box-body clearfix text-center">
                        @if($integrationClass::CREATE)
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

    <input type="hidden" name="totalCost" value="{{$order->price_total}}">
    <input type="hidden" name="order" value="{{$order}}">{{--todo переделать--}}
    <input type="hidden" name="recipientCityName" id="recipientCityName" value="">
    <input type="hidden" name="recipientAddressName" id="recipientAddressName" value="">
</div>
<input type="hidden" name="clientPhone" value="{{$order->phone}}">
<br>
<div class="form-group">
    <div class="main-box-body clearfix text-center">
        <div class="poshta-data">
            <div class="print_note hidden">
                @include('integrations.novaposhta.print', ['target_value' => $target_value])
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="track2" name="approve[track2]"
       value="{{!empty($target_value->track2) ? $target_value->track2 : NULL}}">
<input type="hidden" id="track" name="track"
       value="{{!empty($target_value->track) ? $target_value->track : NULL}}">
@php
    $sendingStatus = 0;
    if($procStatuses && $procStatuses->count()){
    $sendingStatus = !empty($procStatuses->where('action', 'to_print')->first()->id) ? $procStatuses->where('action', 'to_print')->first()->id : $procStatusesSystem->where('action', 'to_print')->first()->id;
    }else{
    $sendingStatus = !empty($procStatusesSystem->where('action', 'to_print')->first()->id) ? $procStatusesSystem->where('action', 'to_print')->first()->id :0;
    }
        !empty($target_value->track) ? $class = '' : $class = 'hidden';
            $order->procStatus->action == 'to_print' ? $disabled = 'disabled' : $disabled = '' ;
            $order->procStatus->action == 'sent' ? $disabled = 'disabled' : $disabled = '' ;
@endphp
@if($sendingStatus  && !empty($order) && !$order->final_target)
    <button type="button" class="btn btn-primary {{$class}} sent-to-print"
            data-proc-status="{{$sendingStatus}}" {{$disabled}}> + Добавить в очередь на
        печать
    </button>
@endif
<br>
<br>