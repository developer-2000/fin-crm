<div class="form-group">
    <label for="approve-city" class="col-lg-3 control-label required">Город/Населенный пункт</label>
    <div class="col-lg-8">
        <input required id="approve-city" class="approve-city " name="approve[city]" value="" style="width: 100%">
        <input type="hidden" id="city" name="city" value="{{!empty($city) ? $city : ''}}" style="width: 100%">
    </div>
</div>
<div class="form-group">
    <label for="approve-warehouse" class="col-lg-3 control-label required">Отделение</label>
    <div class="col-lg-8">
        <input required id="approve-warehouse" class="approve-warehouse" name="approve[warehouse]" value=""
               style="width: 100%">
        <input type="hidden" id="warehouse" name="warehouse" value="{{!empty($warehouse) ? $warehouse : NULL}}"
               style="width: 100%">
    </div>
</div>
<div class="form-group">
    <label for="note" class="col-lg-3 control-label">Заметка</label>
    <div class="col-lg-8">
        <input type="text" id="note" class="form-control" name="approve[note]" value="{{!empty($note) ? $note : NULL}}">
    </div>
</div>
<script src="{{ URL::asset('js/post_js/novaposhta.js')}}"></script>