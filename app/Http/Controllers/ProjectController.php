<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Project;
use App\Models\StorageContent;
use App\Models\MovingProduct;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Api\Integration;

class ProjectController extends BaseController
{
    /**
     * @return Factory|View
     */
    public function projects()
    {
        return view('projects.projects', [
            'projects' => Project::where(['parent_id' => 0])->orderBy('id', 'asc')->get(),
        ]);
    }

    /**
     * @param $id
     * @return Factory|View
     */
    public function show($id)
    {
//        $integrationsJson = json_encode(array_map(function ($element) {
//            return $element = ['value' => $element['id'],
//                               'text'  => $element['name']];
//        }, Integration::where('active', 1)->get()->toArray()), JSON_UNESCAPED_UNICODE);
        $project = Project::with(['children', 'parent'])->where(['id' => $id])->firstOrFail();
        return view('projects.show', [
//            'integrationsJson' => $integrationsJson,
'project' => $project
        ]);
    }

    /**
     * @param int $project_id
     * @return Factory|View
     */
    public function subCreate(int $project_id)
    {
        $parent_project = Project::findOrFail($project_id);
        return view('projects.subcreate', ['parent_project' => $parent_project]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function subStore(Request $request)
    {

        // пока добавляет админ, поэтому никаких дополнительных проверок нет
        // но потом можно проверять, если другая роль, его ли это проект, коль он добавляет сюда склад

        $r = $request->only(['parent_id', 'alias', 'name']);

        $validator = Validator::make($r, [
            'parent_id' => ['required', 'exists:projects,id'],
            'alias'     => [
                'required',
                'alpha_dash',
                Rule::unique(Project::tableName())->where(function ($query) use ($r) {
                    return $query->where('parent_id', $r['parent_id']);
                })
            ],
            'name'      => [
                'required',
                'string',
                Rule::unique(Project::tableName())->where(function ($query) use ($r) {
                    return $query->where('parent_id', $r['parent_id']);
                })
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $project = new Project;
        $project->fill($r);
        $project->save();

        return redirect()->route('project-show', $project->parent_id)->with([
            'message' => 'Субпроект "' . $project->name . '" успешно создан.'
        ]);
    }

    /**
     * @param int $id
     * @return Factory|View
     */
    public function edit(int $id)
    {
        $project = Project::findOrFail($id);
        return view('projects.edit', ['project' => $project]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $r = $request->only(['alias', 'name']);
        $validator = Validator::make($r, [
            'alias' => [
                'required',
                'alpha_dash',
                Rule::unique(Project::tableName())->where(function ($query) use ($project) {
                    return $query->where('parent_id', $project->parent_id);
                })->ignore($project->id)
            ],
            'name'  => [
                'required',
                'string',
                Rule::unique(Project::tableName())->where(function ($query) use ($project) {
                    return $query->where('parent_id', $project->parent_id);
                })->ignore($project->id)
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $project->fill($r);
        $project->save();

        $redirect = $project->parent_id
            ? redirect()->route('project-show', $project->parent_id)
            : redirect()->route('projects');
        return $redirect
            ->with(['message' => ($project->parent_id ? 'Субпроект ' : 'Проект ') . $project->name . ' успешно изменён.']);
    }

    public function findByWordProject(Request $request)
    {
        $projects = Project::findByWordProject(trim($request->get('query')), $request->partner_id);

        $result = [];

        if ($projects->isNotEmpty()) {
            foreach ($projects as $project) {
                $result[] = [
                    'id'   => $project->id,
                    'text' => $project->name
                ];
            }
        }
//        $results = array_merge([
//            [
//                'id'   => "",
//                'text' => 'Все'
//            ]
//        ], $result);

        return response()->json($result);
    }

    public function findByWordSubProject(Request $request)
    {
        $projects = Project::findByWordSubProject(
          trim($request->input('query'))??null,
          $request->input('project_id'),
          $request->partner_id
        );

        $result = [];

        if ($projects->isNotEmpty()) {
            foreach ($projects as $project) {
                $result[] = [
                    'id'   => $project->id,
                    'text' => $project->name
                ];
            }
        }

        return response()->json($result);
    }

    public function findByWordDivisions(Request $request)
    {
        $projects = $request->input('project_id') ? explode(',', $request->input('project_id')) : [];
        $subProjectIds = $request->input('sub_project_id') ? explode(',', $request->input('sub_project_id')) : [];
        $divisions = Project::findByWordDivisions(trim($request->input('query'))??null, $subProjectIds, $projects);

        $result = [];

        if ($divisions->isNotEmpty()) {
            foreach ($divisions as $division) {
                $result[] = [
                    'id'   => $division->id,
                    'text' => $division->name
                ];
            }
        }

        return response()->json($result);
    }

    public function integrationSave(Request $request)
    {
        Integration::find($request->value)->update(['subproject_id' => $request->pk]);
    }

    // при создании проекта должен создаваться один storehouse с такими же name и alias
}
