<div class="col-md-6">
    <div class="form-group">
        <label for="title">
            @lang('general.title')
        </label>
        <input type="text" id="title" name="title" class="form-control" required value="{{$itemMenu->title ?? ''}}">
    </div>
    <div class="form-group">
        <label for="type">
            @lang('general.type')
        </label>
        <select name="type" id="type" class="form-control" required>
            @foreach(\App\Models\Menu::getType() as $type)
                <option value="{{$type}}" @if(!empty($itemMenu->type) && $itemMenu->type == $type) selected @endif>
                    @lang('menu.' . $type)
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="route">
            @lang('general.route')
        </label>
        <input type="text" id="route" name="route" class="form-control" value="{{$itemMenu->route ?? ''}}">
    </div>
    <div class="form-group">
        <label for="icon">
            Fontawesome
        </label>
        <input type="text" id="icon" name="icon" class="form-control" value="{{$itemMenu->icon ?? ''}}">
    </div>
</div>
<div class="error_messages"></div>