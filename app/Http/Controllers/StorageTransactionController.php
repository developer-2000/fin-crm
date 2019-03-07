<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\StorageTransaction;
use App\Models\StorageContent;
use App\Models\Product;
use App\Models\Moving;
use App\Models\MovingProduct;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StorageTransactionController extends BaseController
{
    /**
     * @param Request $request
     * @return Factory|View
     */
    public function all(Request $request) {
        $begin_query = StorageTransaction::searchQuery($request)
            ->searchSelect();

        $query = StorageTransaction::searchQuery($request)
            ->searchSelect()
            ->setSearchWhere($request)
            ->setSearchSort($request);

        $transactions = $query->paginate(15);
        $sortLinks = (new StorageTransaction)->sortLinks($request);
        $appendPage = (new StorageTransaction)->appendPage($request);
        $types = StorageTransaction::langTypes();
        $projects = auth()->user()->project_id
            ? false
            : $begin_query->groupBy('pj_id')->pluck('project_name', 'pj_id')->toArray();
        $subprojects = (auth()->user()->project_id && auth()->user()->sub_project_id)
            ? false
            : $begin_query->groupBy('subproject_id')->pluck('subproject_name', 'subproject_id')->toArray();
        $users = $begin_query->groupBy('user_id')->pluck('user_name', 'user_id')->toArray();

        //$products = $begin_query->groupBy('product_id')->pluck('product_name', 'product_id')->toArray();
        $product_id = $request->get('product_id', 0);
        $product_name = $product_id ? Product::where('id', $product_id)->pluck('title')->first() : '';

        return view('transactions.all', compact(
            'transactions', 'sortLinks', 'appendPage', 'types', 'projects', 'subprojects', 'users',
            /*'products',*/ 'product_id', 'product_name'
        ));
    }


    public function getProductsList2(Request $request) {
        if (!$request->ajax()) {
            abort(403);
        }
        $word = $request->post('word', '');
        $products = StorageTransaction::searchQuery($request)
            ->where('p.title', 'like', '%' . $word . '%')
            ->select(DB::raw('p.title as text, p.id as id'))
            ->groupBy('p.id')
            ->orderBy('p.title')
            ->get();
        return $products;
    }

    public function create() {
        $user_project_id = auth()->user()->project_id;
        $user_subproject_id = auth()->user()->sub_project_id;

        $query = $user_project_id
            ? Project::where('id', $user_project_id)
            : Project::where('parent_id', 0)->orderBy('id');
        $projects = $query->pluck( 'name', 'id');
        $storage = $user_subproject_id
            ? Project::where('id', $user_subproject_id)->select('id', 'name')->first()
            : false;

        return view('transactions.create', compact('projects', 'storage'));
    }

    public function store(Request $request) {
        if (!$request->ajax()) {
            abort(403);
        }

        /*$user_project_id = (int) auth()->user()->project_id;
        $user_subproject_id = (int) auth()->user()->sub_project_id;
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects.parent_id'],
            'storage_id' => ['required', 'exists:projects.id'],
            'product_id' => ['required', 'exists:product.id'],
            'amount' => ['required_unless:hold,+,-', 'in:+,-'],
            'hold' => ['required_unless:amount,+,-', 'in:+,-'],
            'amount_sign' => ['re']
        ]);
        $validator->sometimes('project_id', 'exists:user.project_id', function ($input) use ($user_project_id) {
            return $user_project_id && ($user_project_id != $input->project_id);
        });
        $validator->sometimes('storage_id', 'exists:user.sub_project_id', function ($input) use ($user_subproject_id) {
            return $user_subproject_id && ($user_subproject_id != $input->storage_id);
        });*/

        $project_id = (int) $request->post('project_id', 0);
        if (!$project_id) {
            return ['error' => trans('alerts.select-project')];
        }
        $user_project_id = auth()->user()->project_id;
        if ($user_project_id && ($user_project_id != $project_id)) {
            return ['error' => trans('alerts.cant-work-with-project')];
        }

        $storage_id = (int) $request->post('storage_id', 0);
        if (!$storage_id) {
            return ['error' => trans('alerts.select-storage')];
        }
        $user_subproject_id = auth()->user()->sub_project_id;
        if ($user_subproject_id && ($user_subproject_id != $storage_id)) {
            return ['error' => trans('warehouses.cant-work-with-storage')];
        }

        $product_id = (int) $request->post('product_id', 0);
        if (!$product_id) {
            return ['error' => trans('alerts.select-product')];
        }

        $amount = (int) $request->post('amount', 0);
        $hold = (int) $request->post('hold', 0);
        if (!$amount && !$hold) {
            return ['error' => trans('alerts.enter-amount-or-hold')];
        }

        $amount_sign = $request->post('amount_sign', '');
        if ($amount && !$amount_sign) {
            $w = trans('general.count');
            return ['error' => trans('alerts.select-sign')];
        }
        $hold_sign = $request->post('amount_sign', '');
        if ($hold && !$hold_sign) {
            $w = trans('general.hold');
            return ['error' => trans('alerts.select-sign')];
        }
        $comment = trim($request->post('comment', ''));
        if (!$comment) {
            return ['error' => trans('alerts.comment-required')];
        }
        if (strlen($comment) > 1000) {
            return ['error' => trans('alerts.comment-cant-longer')];
        }

        if ($amount && ($amount_sign == '-')) {
            $amount *= -1;
        }
        if ($hold && ($hold_sign == '-')) {
            $hold *= -1;
        }

        $sc = StorageContent::where('project_id', $storage_id)
            ->where('product_id', $product_id)->first();
        if ($sc) {
            $sc->amount += $amount;
            $sc->hold += $hold;
            if (($sc->amount < 0) || ($sc->hold < 0)) {
                return ['error' => trans('alerts.storage-cant-negative')]; //In a storage there can not be a negative Number or Hold
            }
            $sc->save();
        } else {
            if (($amount < 0) || ($hold < 0)) {
                return ['error' => trans('warehouses.storage-cant-negative')];
            }
            $sc = StorageContent::create([
                'project_id' => $storage_id,
                'product_id' => $product_id,
                'amount' => $amount,
                'hold' => $hold,
            ]);
        }
        $st = StorageTransaction::create([
            'product_id' => $product_id,
            'project_id' => $storage_id,
            'user_id' => auth()->user()->id,
            'amount1' => $sc->amount - $amount,
            'amount2' => $sc->amount,
            'hold1' => $sc->hold - $hold,
            'hold2' => $sc->hold,
            'type' => StorageTransaction::TYPE_MANUAL,
            'moving_id' => 0
        ]);
        Comment::create([
            'commentable_id' => $st->id,
            'commentable_type' => StorageTransaction::class,
            'entity' => 'transaction',
            'user_id' => auth()->user()->id,
            'text' => $comment,
            'date' => time()
        ]);
        return [
            'message' => trans('alerts.transaction-successful'), //The transaction is successful. The page will be reloaded.
            'link' => route('transactions')
        ];
    }

    public function getStorages(Request $request) {
        if (!$request->ajax()) {
            abort(403);
        }
        $users_project_id = (int) auth()->user()->project_id;
        $project_id = (int) $request->post('project_id', 0);
        if ($users_project_id && ($users_project_id != $project_id)) {
            abort(403);
        }
        $storages = Project::where('parent_id', $project_id)
            ->orderBy('id', 'asc')
            ->pluck('name', 'id');
        $users_subproject_id = auth()->user()->sub_project_id;
        $storages_view = view('transactions.storages', [
            'storages' => $users_subproject_id ? $storages->only([$users_subproject_id]) : $storages
        ]);
        return ['storages_html' => $storages_view->render()];
    }

    public function getProducts() {
        return ['product_html' => view('transactions.products2')->render()];
    }

    public function getProductsList(Request $request) {
        // надо извлечь продукты с левым складом
        // нужны имена, холды, эмаунты, иды
        if (!$request->ajax()) {
            abort(403);
        }
        $users_project_id = auth()->user()->project_id;
        $users_subproject_id = auth()->user()->sub_project_id;
        $project_id = (int) $request->post('project_id', 0);
        $storage_id = (int) $request->post('storage_id', 0);
        $word = $request->post('word', '');
        if ($users_project_id && ($users_project_id != $project_id)) {
            abort(403);
        }
        if ($users_subproject_id && ($users_subproject_id != $storage_id)) {
            abort(403);
        }
        $p = Product::tableName();
        $sc = StorageContent::tableName();
        $products = Product::leftJoin($sc . ' as sc', $p . '.id', '=', 'sc.product_id')
            ->where($p . '.project_id', $project_id)
            ->where(function($query) use ($storage_id) {
                $query->where('sc.project_id', $storage_id)
                    ->orWhere(function($query) {
                        $query->where('sc.amount', NULL)->where('sc.hold', NULL);
                    });
            })
            ->where('title', 'like', '%' . $word . '%')
            ->select(DB::raw('if(sc.amount, sc.amount, 0) as amount, '
                . 'if(sc.hold, sc.hold, 0) as hold, '
                . $p . '.id as id, ' . $p . '.title as title'))
            ->orderBy('amount', 'desc')
            ->get();
        $ps = [];
        if ($products->isNotEmpty()) {
            foreach ($products as $product) {
                $ps[] = [
                    'id' => $product->id,
                    'text' => $product->title . ' [' . (int) $product->amount . ' / ' . (int) $product->hold . ']'
                ];
            }
        }
        return $ps;
    }

    public function getProduct(Request $request) {
        if (!$request->ajax()) {
            abort(403);
        }
        $storage_id = (int) $request->post('storage_id', 0);
        $product_id = (int) $request->post('product_id', 0);
        if  (!$product_id || !$storage_id) {
            abort(404);
        }
        $p = Product::tableName();
        $sc = StorageContent::tableName();
        $product = Product::leftJoin($sc . ' as sc', $p . '.id', '=', 'sc.product_id')
            ->where($p . '.id', $product_id)
            ->where(function($query) use ($storage_id) {
                $query->where('sc.project_id', $storage_id)
                    ->orWhere(function($query) {
                        $query->where('sc.amount', NULL)->where('sc.hold', NULL);
                    });
            })
            ->select(DB::raw('if(sc.amount, sc.amount, 0) as amount, if(sc.hold, sc.hold, 0) as hold'))
            ->firstOrFail();
        return $product;
    }
}
