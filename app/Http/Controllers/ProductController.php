<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductProject;
use App\Models\Project;
use App\Repositories\FilterRepository;
use function foo\func;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProductController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $filter = [
            'project'     => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'category'    => $request->input('category'),
            'product'     => $request->input('product'),
            'offers'      => $request->input('offers')
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('products') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $dataFilters = FilterRepository::processFilterData($filter);
        return view('products.index', [
            'projects'   => Project::where('parent_id', 0)->get(),
            'categories' => Category::where('entity', 'product')->get(),
            'products'   => $this->getProducts($filter)
        ], $dataFilters);
    }

    /**
     * @param $filter
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProducts($filter)
    {
        $products = Product::with([
            'productProjects',
            'productProjects.subProject',
            'productProjects.project',
            'productProjects'
        ]);
        //->orderBy('id', 'desc');

        if ($filter['project']) {
            $filter['project'] = explode(',', $filter['project']);
            $products->whereHas('productProjects', function ($query) use ($filter) {
                $query->whereIn('project_id', $filter['project']);
            });
        }
        if (isset($filter['sub_project'])) {
            $filter['sub_project'] = explode(',', $filter['sub_project']);
            $products->whereHas('productProjects', function ($query) use ($filter) {
                $query->whereIn('subproject_id', $filter['sub_project']);
            });
        }
        if ($filter['category']) {
            $filter['category'] = explode(',', $filter['category']);
            $products = $products->whereIn('category_id', $filter['category']);
        }
        if ($filter['product']) {
            $filter['product'] = explode(',', $filter['product']);
            $products = $products->whereIn('id', $filter['product']);
        }

        return $products->orderBy('title')->paginate(50)->appends(Input::except('page'));
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(ProductRequest $request)
    {
        $newProduct = Product::create([
            'project_id'     => 0,
            'sub_project_id' => 0,
            'product_id'     => $request->product_id,
            'category_id'    => $request->category_id,
            'title'          => $request->title,
            'weight'         => $request->weight,
            'price_cost'     => $request->price_cost,
            'status'         => $request->status,
            'type'           => 0
        ]);

        if ($newProduct) {
            return response()->json([
                'success' => true,
                'html'    => view('products.ajax.update-products-table', ['newProduct' => $newProduct])->render()
            ]);
        }
    }

    /**
     * поиск всех товаров по селекту
     */
    public static function findByWord(Request $request, Product $productModel)
    {
        $term = trim($request->q);
        isset($request->except_product) ?? $request['except_product'] = NULL;

        $products = $productModel->searchProductByWord($term, $request->project_id, $request->except_product, $request->sub_project);
        $formatted_products = [];

        foreach ($products as $product) {
            $formatted_products[] = [
                'id'   => $product->id,
                'text' => $product->title
            ];
        }

        return \Response::json($formatted_products);

    }

    public static function findOptionByWord(Request $request)
    {

        $term = trim($request->q);

        $options = ProductOption::findOptions($term, $request->product_id);
        $formatted_products = [];

        if ($options->isNotEmpty()) {
            foreach ($options as $option) {
                $formatted_products[] = ['id' => $option->id, 'text' => $option->value];
            }
        }
        return response()->json($formatted_products);

    }

    /**
     * @param Request $request
     * @param OrderProduct $ordersOffersModel
     * @return \Illuminate\Http\JsonResponse
     */
    public function moderationChangeProductType(Request $request, OrderProduct $ordersOffersModel)
    {
        $this->validate($request, [
            'id'    => 'required|numeric|min:1',
            'type'  => 'required|string',
            'value' => 'required|boolean'
        ]);

        $res = [
            'success' => $ordersOffersModel->changeProductType(
                $request->get('id'),
                $request->get('type'),
                $request->get('value')
            ),
            'message' => trans('alerts.data-not-saved')
        ];

        if ($res['success']) {
            $res['message'] = trans('alerts.data-successfully-saved');
        }

        return response()->json($res);

    }

    /**
     * поиск по товарам
     */
    public function searchProduct(Request $request, Product $productModel)
    {
        $this->validate($request, [
            'search'       => 'required|string',
            'project_id'   => 'required|int|min:0',
            'subProjectId' => 'required|int|min:0'
        ]);

        $data = $productModel->search($request->project_id, $request->search, $request->subProjectId);

        $html = view('orders.ajax.order_one_search_ajax', [
            'data' => $data,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $subProjects = Project::whereIn('id', $product->productProjects->pluck('subproject_id')->toArray())->get()
            ->toArray();

        $subProjectsJson = array_map(function ($element) {
            return $elements = [
                'id'   => "" . $element['id'] . "",
                'text' => $element['name']
            ];
        }, $subProjects);
        $subProjectsJson = json_encode($subProjectsJson, JSON_UNESCAPED_UNICODE);

        return view('products.edit',
            [
                'product'         => $product,
                'subProjectsJson' => $subProjectsJson,
                'projects'        => Project::where('parent_id', 0)->get(),
                'categories'      => Category::where('entity', 'product')->get()
            ]);
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        if ($request->sub_projects) {
            foreach (explode(',', $request->sub_projects) as $subProject) {
                $subProjectModel = Project::find($subProject);
                ProductProject::firstOrCreate([
                    'product_id'    => $request->productId,
                    'project_id'    => $subProjectModel->parent_id,
                    'subproject_id' => $subProject,
                    'status'        => 1
                ]);
            }

            $existProductProjects = ProductProject::where('product_id', $request->productId)
                ->get();
            if ($existProductProjects->count()) {
                foreach ($existProductProjects as $existProductProject) {
                    if (!in_array($existProductProject->subproject_id, explode(',', $request->sub_projects))) {
                        $existProductProject->delete();
                    }
                }
            }
        }
        $res = Product::findOrFail($request->productId)->update($request->all());

        if ($request->options) {
            ProductOption::addOptions($request->options, $request->productId);
        }

        return response()->json(['success' => $res]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        $product = Product::where('id', $request->pk)->has('orderProducts')->first();
        if ($product) {
            return response()->json(['exist' => true]);
        } else {
            if (Product::findOrFail($request->pk)->delete()) {
                return response()->json(['success' => true, 'pk' => $request->pk]);
            }
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function editProductTitle(Request $request)
    {
        return Product::where('id', $request->pk)->update(['title' => $request->value]);
    }

    /*изменение статуса товара*/
    public function changeStatus($id, $status)
    {
        $product = Product::findOrFail($id);

        $product->status = $status;
        if ($product->save()) {
            return response()->json(['success' => true]);
        }
    }

    public function addProductOptionToOrderAjax(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|exists:' . ProductOption::tableName() . ',id',
            'pk'    => 'required|exists:' . OrderProduct::tableName() . ',id',
        ]);

        $result = [
            'success' => OrderProduct::where('id', $request->pk)->update(['product_option_id' => $request->value]),
            'message' => trans('alerts.data-not-saved'),
        ];

        if ($result['success']) {
            $result['message'] = trans('alerts.data-not-saved');
        }

        return response()->json($result);
    }

    public function mergeProducts(Request $request)
    {
        $this->validate($request, [
            'product_to_merge' => 'required',
            'productId'        => 'required',
        ]);

        $result = Product::mergeProducts($request->all());
        return response()->json([
            'success'       => $result['success'],
            'error_message' => isset($result['error_message']) ? $result['error_message'] : ''
        ]);
    }
}
