@if ($target)
    <?
    function getTargetView($target, &$result, $targetFinal)
    {
        foreach ($target as $key => $t) {
            $final = ($t['final']) ? 1 : 0;
            $result .= "<li data-final='". $final ."'>";
            if ($t['type'] == 1) {
                $checked = '';
                $hidden = '';
                if ($t['name'] == 'type') {
                    $checked = 'checked';
                    $hidden = 'style="display: none"';
                }
                if (isset($targetFinal[$t['name']])) {
                    if ($targetFinal[$t['name']]->value == $t['value']) {
                        $checked = 'checked';
                    }
                }
                $result .= '<div class="checkbox-nice" '. $hidden .' >';
                $result .= '<input type="checkbox" class="cause" name="'. $t['name'] .'" id="'. $t['name'] . '_' . $key .'" value="'. $t['value'] .'" '. $checked;
                $result .= '>';
                $result .= '<label class="target_radio" for="'. $t['name'] . '_' . $key .'">' . $t['title'] . '</label></div>';
            } elseif ($t['type'] == 2) {
                $result .= '<div class="checkbox-nice">';
                $result .= '<input type="checkbox" name="'. $t['name'] .'" id="'. $t['name'] . '_' . $key .'" value="'. $t['value'] .'">';
                $result .= '<label for="'. $t['name'] . '_' . $key .'">' . $t['title'] . '</label></div>';

            } elseif ($t['type'] == 3) {
                $class = '';
                if (count($t['title']) == 1) {
                    $class = $t['title'][1];
                }
                $result .= '<div class="form-group">';
                $result .= '<select class="crm_target_input '. $class .'" name="'. $t['name'] .'" >';
                if ($t['title']) {
                    foreach ($t['title'] as $keyTitle => $valueTitle) {
                        $result .= "<option value='". $keyTitle ."'>" . $valueTitle . "</option>";
                    }
                }
                $result .= '</select></div>';
            } elseif ($t['type'] == 4) {
                $result .= '<div class="form-group" style="margin-bottom: 10px;">';
                $result .= '<label class="col-sm-4 control-label" style="text-align: left">' . $t['title'] . '</label>';
                $result .= '<input type="text" class="form-control crm_target_input" name="'. $t['name'] .'" placeholder="'. $t['title'] .'" data-required="'. $t['required'] .'" data-max="'. $t['max_len'] .'" data-min="'. $t['min_len'] .'"';
                if (isset($targetFinal[$t['name']])) {
                    $result .= 'value="' . $targetFinal[$t['name']]->value . '"';
                }
                $result .= '>';
                $result .= '</div>';
            } elseif ($t['type'] == 5) {
                $result .= '<div class="form-group">';
                $result .= '<label class="col-sm-4 control-label" style="text-align: left">' . $t['title'] . '</label>';
                $result .= '<textarea class="form-control crm_target_input" rows="4" name="'. $t['name'] .'" placeholder="'. $t['title'] .'" data-required="'. $t['required'] .'" data-max="'. $t['max_len'] .'" data-min="'. $t['min_len'] .'">';
                if (isset($targetFinal[$t['name']])) {
                    $result .= $targetFinal[$t['name']]->value;
                }
                $result .= '</textarea>';
                $result .= '</div>';
            }
            if (!$t['final']) {
                $result .= '<ul style="display: none">';
                getTargetView($t['childs'], $result, $targetFinal);
                $result .= '</ul>';
            }
            $result .= '</li>';
        }
    }
    $success = '';
    if (isset($target[0])) {
        getTargetView($target[0], $success, $targetFinal);

    }
    $repealed = '';
    if (isset($target[1])) {
        getTargetView($target[1], $repealed, $targetFinal);
    }
    ?>
@endif

<div class="tab-pane fade " id="approve">
    <div class="main-box clearfix">
        <div class="main-box-body clearfix text-center" style="padding-top: 20px;">
            @lang('orders.change-target')
            @if ($changeTargets)
                <select name="date-type" id="change_targets" class="form-control" style="width: 300px;display: inline-block">
                    <option value="" > @lang('general.not')</option>
                    @foreach ($changeTargets as $keyCt => $valueCt)
                        <option @if ($orderTargetId == $keyCt) selected @endif value="{{ $keyCt }}" >{{ $valueCt }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <form action="#" class="form-horizontal" onsubmit="return false">
            <div class="main-box-body clearfix">
                <p class="text-center title_tab_content"> @lang('orders.fill-data-order')</p>

                @if ($success)
                    <div class="col-sm-offset-3">
                        <ul>
                            {!! $success !!}
                        </ul>
                    </div>
                @endif
            </div>
            <div class="main-box-body clearfix text-center">
                <input class="btn btn-success order_confirm" type="button" name="submit" value=" @lang('general.save')">
            </div>
        </form>
    </div>
</div>
<div class="tab-pane fade " id="failure">
    <div class="main-box clearfix">
        <form action="#" onsubmit="return false">
            <div class="main-box-body clearfix">
                <p class="text-center title_tab_content"> @lang('orders.enter-cause-refusal')</p>
                <div class="form-group">
                    <textarea class="form-control" id="cancel_text" rows="3" placeholder=" @lang('orders.cause-refusal')"></textarea>
                </div>
            </div>
            <div class="main-box-body clearfix text-center">
                <input class="btn btn-success failure_order" type="button" name="submit" value=" @lang('general.save')">
            </div>
        </form>
    </div>
</div>
<div class="tab-pane fade" id="fake">
    <div class="main-box clearfix">
        <form action="#" onsubmit="return false">
            <div class="main-box-body clearfix">
                @if ($repealed)
                    <div class="col-sm-offset-3">
                        <ul>
                            {!! $repealed !!}
                        </ul>
                    </div>
                @endif
            </div>
            <div class="main-box-body clearfix text-center">
                <input class="btn btn-success cancel_order" type="button" name="submit" value=" @lang('general.save')">
            </div>
        </form>
    </div>
</div>
