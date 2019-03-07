<?php

namespace App\Http\Controllers;  

use App\Models\Country;
use App\Models\Offer;
use App\Models\Project;
use App\Models\TargetConfig;
use Illuminate\Http\Request;

class TargetController extends BaseController
{
    public function index(TargetConfig $targetConfigModel)
    {
        $data['targets'] = $targetConfigModel->getAllTargets();
        return view('targets.index', $data);
    }

    public function create()
    {
        $data['countries'] = Country::all();
        $data['offers'] = Offer::all();
        $data['projects'] = Project::all();
        return view('targets.create', $data);
    }

    public function targetCreateAjax(Request $request, TargetConfig $targetConfigModel)
    {
        $this->validate($request, [
            'active'                                    => 'boolean',
            'name'                                      => 'required|max:255|min:2',
            'alias'                                     => 'required|max:255|min:2|regex:/^[a-zA-Z0-9\-\_]*$/|unique:target_configs,alias',
            'entity'                                    => 'nullable|max:50|min:2',
            'target_type'                               => 'required|max:50|min:2',
            'country'                                   => 'nullable|min:2|max:5',
            'offer'                                     => 'nullable|numeric',
            'project'                                   => 'nullable|numeric',
            'tag_campaign'                              => 'nullable|string|max:255',
            'tag_content'                               => 'nullable|string|max:255',
            'tag_medium'                                => 'nullable|string|max:255',
            'tag_source'                                => 'nullable|string|max:255',
            'tag_term'                                  => 'nullable|string|max:255',
            'template'                                  => 'required|max:50|min:2',
            'product_template'                          => 'required_if:template,product|min:1|array',
            'product_template.*.field-title'            => 'required_if:template,product|min:1|max:50',
            'product_template.*.field-name'             => 'required_if:template,product|max:255|min:2|regex:/^[a-zA-Z0-9\-\_]*$/',
            'product_template.*.field-type'             => 'required_if:template,product|max:50|min:2',
            'product_template.*.field-relation-name'    => 'min:2|max:255|regex:/^[a-zA-Z0-9\-\_]*$/',
            'product_template.*.field-relation-value'   => 'min:2|max:255',
            'product_template.*.field-required'         => 'min:1|max:5',
            'product_template.*.field-show-result'      => 'min:1|max:5',
            'product_template.*.field-range-min'        => 'numeric',
            'product_template.*.field-range-max'        => 'numeric',
            'product_template.*.option'                 => 'required_if:product_template.*.field-type,select,radio,checkbox|min:1|array',
            'product_template.*.option.*.value'         => 'required_if:product_template.*.field-type,select,radio,checkbox|min:1|max:255|regex:/^[a-zA-Z0-9\-\_]*$/',
            'product_template.*.option.*.title'         => 'required_if:product_template.*.field-type,select,radio,checkbox|min:1|max:255|',
            'custom_template'                           => 'required_if:template,custom|min:1|array',
            'custom_template.*.field-title'             => 'required_if:template,custom|min:1|max:50',
            'custom_template.*.field-name'              => 'required_if:template,custom|max:255|min:2|regex:/^[a-zA-Z0-9\-\_]*$/',
            'custom_template.*.field-type'              => 'required_if:template,custom|max:50|min:2',
            'custom_template.*.field-relation-name'     => 'min:2|max:255|regex:/^[a-zA-Z0-9\-\_]*$/',
            'custom_template.*.field-relation-value'    => 'min:2|max:255',
            'custom_template.*.field-required'          => 'min:1|max:5',
            'custom_template.*.field-show-result'       => 'min:1|max:5',
            'custom_template.*.field-range-min'         => 'numeric',
            'custom_template.*.field-range-max'         => 'numeric',
            'custom_template.*.option'                  => 'required_if:custom_template.*.field-type,select,radio,checkbox|min:1|array',
            'custom_template.*.option.*.value'          => 'required_if:custom_template.*.field-type,select,radio,checkbox|min:1|max:255|regex:/^[a-zA-Z0-9\-\_]*$/',
            'custom_template.*.option.*.title'          => 'required_if:custom_template.*.field-type,select,radio,checkbox|min:1|max:255|'
        ]);


        $options = [];
        if ($request->get('template') == "product") {
            $options = $this->getDataForTarget($request->get('product_template'));
        } elseif ($request->get('template') == "custom") {
            $options = $this->getDataForTarget($request->get('custom_template'));
        } else {
            abort(404);
        }

        $data['name'] = $request->get('name');
        $data['alias'] = $request->get('alias');
        $data['entity'] = $request->get('entity');
        $data['template'] = $request->get('template');
        $data['target_type'] = $request->get('target_type');
        $data['filter_geo'] = $request->get('country');
        $data['filter_offer'] = $request->get('offer');
        $data['filter_project'] = $request->get('project');
        $data['tag_campaign'] = $request->get('tag_campaign');
        $data['tag_content'] = $request->get('tag_content');
        $data['tag_medium'] = $request->get('tag_medium');
        $data['tag_source'] = $request->get('tag_source');
        $data['tag_term'] = $request->get('tag_term');
        $data['options'] = json_encode($options);
        $data['active'] = $request->get('active') ? 1 : 0;
        return response()->json([
            'success'  => $targetConfigModel->addData($data)
        ]);
    }

    public function getDataForTarget($data)
    {
        $template = [];
        if ($data) {
            foreach ($data as $field) {
                $options = [];
                $name = isset($field['field-name']) ? $field['field-name'] : '';
                if (isset($field['option'])) {
                    foreach ($field['option'] as $option) {
                        $options[$option['value']] = $option['title'];
                    }
                }

                $template[$name] = [
                    'field_title'           => isset($field['field-title']) ? $field['field-title'] : '',
                    'field_name'            => $name,
                    'field_type'            => isset($field['field-type']) ? $field['field-type'] : '',
                    'field_value'           => '',
                    'field_relation_name'   => isset($field['field-relation-name']) ? $field['field-relation-name'] : '',
                    'field_relation_value'  => isset($field['field-relation-value']) ? $field['field-relation-value'] : '',
                    'field_required'        => isset($field['field-required']) ? $field['field-required'] : '',
                    'field_show_result'     => isset($field['field-show-result']) ? $field['field-show-result'] : '',
                    'options'               => $options,
                    'field_settings'        => []
                ];

                if (!empty($field['field-range-min'])) {
                    $template[$name]['field_settings']['range_min'] = $field['field-range-min'];
                }

                if (!empty($field['field-range-max'])) {
                    $template[$name]['field_settings']['range_max'] = $field['field-range-max'];
                }

            }
        }

        return $template;
    }

    public function edit($id)
    {
        $data['target'] = TargetConfig::where('id', $id)->first();
        if ($data['target']) {
            $data['countries'] = Country::all();
            $data['offers'] = Offer::all();
            $data['projects'] = Project::all();
            return view('targets.edit', $data);
        }
        abort(404);
    }

    public function updateTargetOneAjax(Request $request, TargetConfig $targetConfigModel)
    {
        $this->validate($request, [
            'id'                                        => 'required|int',
            'active'                                    => 'boolean',
            'name'                                      => 'required|max:255|min:2',
            'country'                                   => 'nullable|min:2|max:5',
            'offer'                                     => 'nullable|numeric',
            'project'                                   => 'nullable|numeric',
            'tag_campaign'                              => 'nullable|string|max:255',
            'tag_content'                               => 'nullable|string|max:255',
            'tag_medium'                                => 'string|max:255',
            'tag_source'                                => 'string|max:255',
            'tag_term'                                  => 'string|max:255',
            'product_template'                          => 'required_if:template,product|min:1|array',
            'product_template.*.field-title'            => 'required_if:template,product|min:1|max:50',
            'product_template.*.field-required'         => 'min:1|max:5',
            'product_template.*.field-show-result'      => 'min:1|max:5',
            'product_template.*.field-range-min'        => 'numeric',
            'product_template.*.field-range-max'        => 'numeric',
            'custom_template'                           => 'required_if:template,custom|min:1|array',
            'custom_template.*.field-title'             => 'required_if:template,custom|min:1|max:50',
            'custom_template.*.field-required'          => 'min:1|max:5',
            'custom_template.*.field-show-result'       => 'min:1|max:5',
            'custom_template.*.field-range-min'         => 'numeric',
            'custom_template.*.field-range-max'         => 'numeric',
            ]);

        $target = TargetConfig::where('id', $request->get('id'))->first();
        if ($target) {
            $options = [];
            if ($target->template == "product") {
                $options = $this->changeTargetOptions(json_decode($target->options), $request->get('product_template'));
            } elseif ($target->template == "custom") {
                $options = $this->changeTargetOptions(json_decode($target->options), $request->get('custom_template'));
            } else {
                abort(404);
            }

            $data['name'] = $request->get('name');
            $data['filter_geo'] = $request->get('country');
            $data['filter_offer'] = $request->get('offer');
            $data['filter_project'] = $request->get('project');
            $data['tag_campaign'] = $request->get('tag_campaign');
            $data['tag_content'] = $request->get('tag_content');
            $data['tag_medium'] = $request->get('tag_medium');
            $data['tag_source'] = $request->get('tag_source');
            $data['tag_term'] = $request->get('tag_term');
            $data['options'] = json_encode($options);
            $data['active'] = $request->get('active') ? 1 : 0;
            return response()->json([
                'success'  => $targetConfigModel->updateDataTarget($data, $request->get('id'))
            ]);
        }
        abort(404);
    }

    public function changeTargetOptions($options, $newData)
    {
        $options = collect($options)->keyBy('field_name');
        if ($newData) {
            foreach ($newData as $nData) {
                if (isset($options[$nData['field-name']])) {

                    $options[$nData['field-name']]->field_title = $nData['field-title'];
                    $options[$nData['field-name']]->field_required = isset($nData['field-required']) ? $nData['field-required'] : '';
                    $options[$nData['field-name']]->field_show_result = isset($nData['field-show-result']) ? $nData['field-show-result'] : '';

                    if (empty($options[$nData['field-name']]->field_settings)) {
                        $options[$nData['field-name']]->field_settings = [];
                    }

                    if (isset($nData['field-range-min'])) {
                        $options[$nData['field-name']]->field_settings->range_min = $nData['field-range-min'];
                    }
                    if (isset($nData['field-range-max'])) {
                        $options[$nData['field-name']]->field_settings->range_max = $nData['field-range-max'];
                    }
                }
            }
        }
        return $options;
    }

    public function findByWord(Request $request)
    {
        $this->validate($request, [
            'q' => 'string',
            'country_code' => 'required|exists:countries,code'
        ]);

        $targets = TargetConfig::findTarget($request->q, $request->country_code);

        $results = [];
        foreach ($targets as $target) {
            $results[] = ['id' => $target->id, 'text' => $target->name];
        }

        return $results;
    }
}
