<div class="form-group">
    <label for="sender" class="col-lg-3 control-label required">Sender</label>
    <div class="col-lg-8">
        <select class="form-control" id="sender" name="sender">
            @if (!empty($senders) && count($senders))
                @foreach($senders as $sender)
                    <option value="{{$sender->id}}"
                    @if (!empty($target_value) && $target_value->sender_id == $sender->id)
                        selected
                    @endif
                    >{{$sender->sender}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
@php
    $integrationClass = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($target_option['approve']->alias ?? '');
@endphp
<div class="form-group">
    @if($target_option['approve'] && $target_option['approve']->integration && $integrationClass && ($integrationClass::CREATE || $integrationClass::EDIT || $integrationClass::DELETE))
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
    @endif
</div>