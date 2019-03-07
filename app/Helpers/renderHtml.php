<?php
/**
 * @param array $options
 * @param string $prefix
 * @param string $postfix
 * @param string $targetName
 */
function renderTarget($options, $prefix = '', $postfix = '', $targetName = '')
{
    if (render_integration($options, $targetName)) {
        return;
    }

    if (function_exists('render_' . $targetName)) {
        $funcName = 'render_' . $targetName;
        $funcName($options, $prefix, $postfix);
        return;
    }

    if ($options) {
        foreach ($options as $option) {
            $type = $option->field_type;
            switch ($type) {
                case 'text' :
                    {
                        getTextHTML($option, $prefix, $postfix);
                        break;
                    }
                case 'textarea' :
                    {
                        getTextHTML($option, $prefix, $postfix);
                        break;
                    }
                case 'checkbox' :
                    {
                        getCheckboxHtml($option, $prefix, $postfix);
                        break;
                    }
                case 'radio' :
                    {
                        getRadioHtml($option, $prefix, $postfix);
                        break;
                    }
                case 'select' :
                    {
                        getSelectHtml($option, $prefix, $postfix);
                        break;
                    }
                case 'number' :
                    {
                        getNumberHtml($option, $prefix, $postfix);
                        break;
                    }
            }
        }
    }
}


//{#641 ▼
//    +"field_title": "checkbox"
//    +"field_name": "boxcheck"
//    +"field_type": "checkbox"
//    +"field_value": ""
//    +"field_relation_name": ""
//    +"field_relation_value": ""
//    +"field_required": ""
//    +"options": {#642 ▼
//          +"val1": "title1"
//          +"val2": "title2"
//          +"val3": "title3"
//    }
//  }

function getTextHTML($option, $prefix = '', $postfix = '')
{
    $name = $prefix . $option->field_name . $postfix;

    $classLabel = 'col-lg-3 control-label';
    if ($option->field_required) {
        $classLabel .= ' required';
    }

    $attr = [
        'class'       => 'form-control',
        'placeholder' => $option->field_title,
        'id'          => $name,
        'rows'        => 3
    ];
    $type = $option->field_type;
    echo '<div class="form-group">';
    echo Form::label($name, $option->field_title, ['class' => $classLabel]);
    echo '<div class="col-lg-8">';
    echo Form::$type($name, $option->field_value, $attr);
    echo '</div>';
    echo '</div>';
}

function getCheckboxHtml($option, $prefix = '', $postfix = '')
{
    $name = $prefix . $option->field_name . $postfix;

    $classLabel = 'col-lg-3 control-label';
    if ($option->field_required) {
        $classLabel .= ' required';
    }

    echo '<div class="form-group">';
    echo Form::label($name, $option->field_title, ['class' => $classLabel]);
    echo '<div class="col-lg-8">';
    if ($option->options) {
        $i = 0;
        foreach ($option->options as $value => $title) {
            $checkboxName = $name . '[' . $value . ']';
            $attr = [
                'id' => $checkboxName . '-' . $i,
            ];

            $checked = false;
            if (isset($option->field_value->$value)) {
                $checked = true;
            }

            echo '<div class="checkbox-nice">';
            echo Form::checkbox($checkboxName, $value, $checked, $attr);
            echo Form::label($checkboxName . '-' . $i, $title);
            echo '</div>';
            $i++;
        }
    }
    echo '</div>';
    echo '</div>';
}

function getRadioHtml($option, $prefix = '', $postfix = '')
{
    $name = $prefix . $option->field_name . $postfix;
    $classLabel = 'col-lg-3 control-label';
    if ($option->field_required) {
        $classLabel .= ' required';
    }
    echo '<div class="form-group">';
    echo Form::label($name, $option->field_title, ['class' => $classLabel]);
    echo '<div class="col-lg-8">';
    if ($option->options) {
        $i = 0;
        foreach ($option->options as $value => $title) {
            $attr = [
                'id' => $name . '-' . $i,
            ];
            $checked = false;

            if ($option->field_value == $value) {
                $checked = true;
            }

            echo '<div class="radio">';
            echo Form::radio($name, $value, $checked, $attr);
            echo Form::label($name . '-' . $i, $title);
            echo '</div>';
            $i++;
        }
    }
    echo '</div>';
    echo '</div>';
}

function getSelectHtml($option, $prefix = '', $postfix = '')
{
    $name = $prefix . $option->field_name . $postfix;
    $classLabel = 'col-lg-3 control-label';
    if ($option->field_required) {
        $classLabel .= ' required';
    }

    $attr = [
        'class' => 'form-control',
        'id'    => $name,
    ];
    echo '<div class="form-group">';
    echo Form::label($name, $option->field_title, ['class' => $classLabel]);
    echo '<div class="col-lg-8">';
    echo Form::select($name, $option->options, $option->field_value, $attr);
    echo '</div>';
    echo '</div>';
}

function getNumberHtml($option, $prefix = '', $postfix = '')
{
    $name = $prefix . $option->field_name . $postfix;
    $classLabel = 'col-lg-3 control-label';
    if ($option->field_required) {
        $classLabel .= ' required';
    }

    $attr = [
        'class' => 'form-control',
        'id'    => $name,
    ];

    if (!empty($option->field_settings->range_min)) {
        $attr['min'] = $option->field_settings->range_min;
    }
    if (!empty($option->field_settings->range_max)) {
        $attr['max'] = $option->field_settings->range_max;
    }

    echo '<div class="form-group">';
    echo Form::label($name, $option->field_title, ['class' => $classLabel]);
    echo '<div class="col-lg-8">';
    echo Form::number($name, $option->field_value, $attr);
    echo '</div>';
    echo '</div>';
}

function render_integration($options = [], $alias)
{
    $params = [];
    if ($options) {
        foreach ($options as $value) {
            $params[$value->field_name] = $value->field_value;
        }
    }

    $html = integrationForm($alias, $params);

    echo $html;

    return !empty($html);
}

function render_measoft($options = [], $prefix = '', $postfix = '')
{
    if ($options) {
        foreach ($options as $option) {
            if ($option->field_name == 'date') {
                getInputDate($option, $prefix, $postfix);
                continue;
            }
            $type = $option->field_type;
            switch ($type) {
                case 'text' :
                    {
                        getTextHTML($option, $prefix, $postfix);
                        break;
                    }
                case 'textarea' :
                    {
                        getTextHTML($option, $prefix, $postfix);
                        break;
                    }
                case 'checkbox' :
                    {
                        getCheckboxHtml($option, $prefix, $postfix);
                        break;
                    }
                case 'radio' :
                    {
                        getRadioHtml($option, $prefix, $postfix);
                        break;
                    }
                case 'select' :
                    {
                        getSelectHtml($option, $prefix, $postfix);
                        break;
                    }
                case 'number' :
                    {
                        getNumberHtml($option, $prefix, $postfix);
                        break;
                    }
            }
        }
        echo '<script src="' . URL::asset('js/post_js/measoft.js') . '"></script>';
    }
}

function getInputDate($option, $prefix = '', $postfix = '')
{
    $name = $prefix . $option->field_name . $postfix;

    $classLabel = 'col-lg-3 control-label';
    if ($option->field_required) {
        $classLabel .= ' required';
    }

    $attr = [
        'class'       => 'form-control measoft_date',
        'placeholder' => $option->field_title,
        'id'          => $name,
        'rows'        => 3
    ];
    $type = $option->field_type;
    echo '<div class="form-group">';
    echo Form::label($name, $option->field_title, ['class' => $classLabel]);
    echo '<div class="col-lg-8">';
    echo Form::$type($name, $option->field_value, $attr);
    echo '</div>';
    echo '</div>';
}

function renderTargetForModeration($options, $orderId, $prefix = '', $postfix = '')
{
    if ($options) {
        foreach ($options as $option) {
            $type = $option->field_type;
            switch ($type) {
                case 'text' : {
                    getTextHTML($option, $prefix, $postfix);
                    break;
                }
                case 'textarea' : {
                    getTextHTML($option, $prefix, $postfix);
                    break;
                }
                case 'checkbox' : {
                    getCheckboxHtml($option, $prefix, $postfix);
                    break;
                }
                case 'radio' : {
                    getRadioHtmlModeration($option, $orderId);
                    break;
                }
                case 'select' : {
                    getSelectHtml($option, $prefix, $postfix);
                    break;
                }
                case 'number' : {
                    getNumberHtml($option, $prefix, $postfix);
                    break;
                }
            }
        }
    }
}

function getRadioHtmlModeration($option, $orderId)
{
        $name = $option->field_name;
        $classLabel = 'col-lg-3 control-label';
        if ($option->field_required) {
            $classLabel .= ' required';
        }
        echo '<div class="form-group">';
        echo Form::label($name, $option->field_title, ['class' => $classLabel]);
        echo '<div class="col-lg-8">';
        if ($option->options) {
            $i = 0;
            foreach ($option->options as $value => $title) {
                $attr = [
                    'id'    => $name . '-' . $i . '-' . $orderId,
                ];
                $checked = false;

                if ($option->field_value == $value) {
                    $checked = true;
                }

                echo '<div class="radio">';
                echo Form::radio($name, $value, $checked, $attr);
                echo Form::label($name . '-' . $i . '-' . $orderId , $title);
                echo '</div>';
                $i++;
            }
        }
        echo '</div>';
        echo '</div>';
}


function getTitleValuesNovaposhta($values, $orderOld)
{
    $result = [];
    if ($values) {
        $result['name'] = 'Новая Почта';
        foreach ($values as $value) {
            if ($value->field_name == 'city') {
                if ($orderOld) {
                    $result[$value->field_name] = [
                        'title' => $value->field_title,
                        'value' => [\App\Models\NP::where('cid', $value->field_value)->value('city_ru')]
                    ];
                } else {
                    $request = request();
                    $request['SettlementRef'] = $value->field_value;
                    $request['q'] = "";
                    $city = \App\Models\Api\Posts\Novaposhta::settlementFind($request);


                    $result[$value->field_name] = [
                        'title' => $value->field_title,
                        'value' => isset($city[0]['text']) ? [$city[0]['text']] : []
                    ];
                }
            }
            if ($value->field_name == 'warehouse') {
                if ($orderOld) {
                    $result[$value->field_name] = [
                        'title' => $value->field_title,
                        'value' => [\App\Models\NP::where('wid', $value->field_value)->value('whs_address_ru')]
                    ];
                } else {
                    $request = request();
                    $request['warehouseRef'] = $value->field_value;
                    $warehouse = \App\Models\Api\Posts\Novaposhta::warehouseFind($request);
                    $result[$value->field_name] = [
                        'title' => $value->field_title,
                        'value' => isset($warehouse[0]['text']) ? [$warehouse[0]['text']] : []
                    ];
                }
            }
            if ($value->field_name == 'note' && $value->field_value) {
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [$value->field_value]
                ];
            }
        }
    }
    return $result;
}

function getTitleValuesViettel($values, $orderOld)
{
    $result = [];
    if ($values) {
        $result['name'] = 'Viettel';
        foreach ($values as $value) {
            if ($value->field_name == 'warehouse') {
                $warehouse = $value->field_value;
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [\App\Models\VietnamWard::where('province_id', $value->field_value)
                                    ->groupBy('province_id')
                                    ->first(['province_name'])->province_name]
                ];
            }

            if ($value->field_name == 'district') {

                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [\App\Models\VietnamWard::where([['province_id', $values->warehouse->field_value],
                                                                ['district_id', $value->field_value]])
                                    ->groupBy('district_id')
                                    ->first(['district_name']) ? \App\Models\VietnamWard::where([['province_id', $values->warehouse->field_value],
                                                                                                 ['district_id', $value->field_value]])
                        ->groupBy('district_id')
                        ->first(['district_name'])->district_name : '']
                ];
            }
            if ($value->field_name == 'region' && $value->field_value) {
                $ward = \App\Models\VietnamWard::where('ward_id', $value->field_value)
                    ->first(['ward_name'])->ward_name ?? NULL;
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => !empty($ward) ? [$ward] : []
                ];
            }
            if ($value->field_name == 'note' && $value->field_value) {
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [$value->field_value]
                ];
            }
        }
    }
    return $result;
}

function getTitleValuesWefast($values, $orderOld)
{
    $result = [];
    if ($values) {
        $result['name'] = 'WeFast';
        foreach ($values as $value) {
            if ($value->field_name == 'region') {
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [\App\Models\Api\WeFast\WeFastOffice::where('province_code', $value->field_value)->value('province_name')]
                ];
            }
            if ($value->field_name == 'district') {
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [\App\Models\Api\WeFast\WeFastOffice::where('district_code', $value->field_value)->value('district_name')]
                ];
            }
            if ($value->field_name == 'note') {
                $result[$value->field_name] = [
                    'title' => $value->field_title,
                    'value' => [$value->field_value]
                ];
            }
        }
    }
    return $result;
}

function renderSubCategories($categories)
{
    if ($categories->isNotEmpty()) {
        echo '<ol class="dd-list">';

        foreach ($categories as $category) {
            echo '<li class="dd-item" data-id="' . $category->id . '">
                    <div class="dd-handle">
                        <span class="dd-nodrag">
                            <a href="#" class="nested-link category_name"
                                        data-pk="' . $category->id . '"
                                        data-value="' . $category->name . '"
                                        data-name="name">
                                        ' . $category->name . '
                                        </a>
                        </span>
                        <div class="nested-links dd-nodrag">
                            <a href="#" class="nested-link delete_sub_categories" data-id="' . $category->id . '">
                                         <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>';
            if ($category->subCategories->isNotEmpty()) {
                renderSubCategories($category->subCategories->sortBy('position'));
            }
            echo '</li>';
        }

        echo '</ol>';
    }
}


function integrationForm($alias, $params = [])
{
    try {
        $className = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($alias);

        if (class_exists($className)) {
            $view = $className::renderView($params);

            return $view ? $view->render() : '';
        }
    } catch (\Exception $exception) {
        return '';
    }
}

function integrationOtherFields($alias, $params = [])
{
    try {
        $className = \App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($alias);

        if (class_exists($className)) {

            $view = $className::otherFieldsView($params);

            return $view ? $view->render() : '';
        }
    } catch (\Exception $exception) {
        return '';
    }
}
