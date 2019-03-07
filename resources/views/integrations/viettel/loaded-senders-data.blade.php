<label class="col-lg-3 control-label" for="sender_warehouse">Склад отправителя</label>
<div class="col-lg-8">
    <select required id="sender_warehouse" name="sender_warehouse" class="form-control">
        @if (isset($integrationKeys))
            @if($integrationKeys->count() == 1)

                    @if(!empty($integrationKeys[0]->senders))
                        @foreach( $integrationKeys[0]->senders as $sender)
                            <option selected readonly="" value="{{$sender->id}}"
                                    data-value="{{$sender->id}}">{{$sender->name}}</option>
                        @endforeach
                    @endif
            @endif
        @else
            <option value="">Выберите склад</option>
            @foreach( $integrationKeys as $key)
                <option value="{{$key->id}}"
                        data-value="{{$key->id}}">{{$key->name}}</option>
            @endforeach
        @endif
    </select>
</div>