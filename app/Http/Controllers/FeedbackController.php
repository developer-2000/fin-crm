<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Country;
use App\Models\Feedback;
use App\Models\Offer;
use App\Models\OperatorMistake;
use App\Models\Order;
use App\Models\OrdersOpened;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use \App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class FeedbackController extends BaseController
{
    public function index(Request $request)
    {
        $currentUser = User::find(auth()->user()->id);
        if (auth()->user()->role_id == 1) {
            $feedbacks = Feedback::with('user', 'order', 'moderator', 'user.company')->where(function ($query) use ($currentUser) {
                $query->whereIn('type',
                    ['failed_call', 'fault', 'info'])->where([['user_id', $currentUser->id], ['status', 'opened']]);
            })->orWhere(function ($query) use ($currentUser) {
                $query->whereIn('type',
                    ['failed_call', 'fault', 'info'])->where([['moderator_id', $currentUser->id], ['status', 'opened']]);
            })->paginate(50);
        } else {
            $feedbacks = Feedback::with('user', 'order', 'moderator', 'user.company')->where(function ($query) use ($currentUser) {
                $query->whereIn('type',
                    ['failed_call', 'fault', 'info'])->where('status', 'opened');
            })->paginate(50);
        }

        $filter = [
            'date-type' => $request->input('date-type'),
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'status' => $request->input('status'),
            'id' => $request->input('id'),
            'user' => $request->input('user'),
            'moderator' => $request->input('moderator'),
            'oid' => $request->input('oid'),
            'company' => $request->input('company'),
            'mistake_type' => $request->input('mistake_type'),
        ];

        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('operator-mistakes') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $feedbacks = Feedback::getFeedbacks($filter);
        $moderatorsIds = Feedback::get(['moderator_id'])->toArray();
        $moderators = User::whereIn('id', $moderatorsIds)->get();

        foreach ($feedbacks as $key1 => $feedback) {
            if (!empty($feedback->mistakes)) {
                foreach (json_decode($feedback->mistakes) as $key => $mistake) {
                    $mistakes[$key1][$key] = $mistake->type;
                }
                $feedback->mistakes = OperatorMistake::whereIn('id', $mistakes[$key1])->get();
            }
        }

        return view('feedbacks.mistakes', ['feedbacks' => $feedbacks, 'companies' => Company::all(),
            'countries' => Country::all(), 'operatorMistakes' => OperatorMistake::all(),
            'users' => User::all(), 'moderators' => $moderators, 'moderatorsAll' => User::whereIn('role_id', [2,]), 'operators' => User::where('role_id', 1)->get(),
            'orders' => Order::select('id')]);
    }

    public function getSuccessCalls(Request $request)
    {

        $filter = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'user' => $request->input('user'),
            'offers' => $request->input('offers'),
//            'moderator' => $request->input('moderator'),
        ];

        if ($request->isMethod('post')) {
            $filter['date_start'] = $filter['date_start'] ? strtotime($request->request->get('date_start') . ' 00:00:00') : 0;
            $filter['date_end'] = $filter['date_end'] ? strtotime($request->request->get('date_end') . ' 23:59:59') : 0;
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('success-calls') . $this->getFilterUrl($filter), true, 303);
            exit;

        }
        if ($filter) {
            $feedbacks = Feedback::getFeedbacks($filter);
        } else {
            $feedbacks = Feedback::with('user', 'comments', 'order')->where('type', 'success_call')->paginate(50);
        }

        foreach ($feedbacks as $key => $feedback) {
            $orderOpened = OrdersOpened::find($feedback->orders_opened_id);

            if ($orderOpened) {
                $feedback->record = DB::table('call_progress_log as cpl')->select('file')
                    ->where('unique_id', $orderOpened->unique_id)->first();
            }
            if ($feedback->order) {
                $feedback->offer = Offer::where('id', $feedback->order->offer_id)->first();
            }
            $feedback->products = DB::table('order_products')->where('order_id', $feedback->order_id)->get();
        }
        $moderatorsIds = Feedback::where('type', 'success_call')->get(['moderator_id'])->toArray();
        $moderators = User::whereIn('id', $moderatorsIds)->get();

        return view('feedbacks.success-calls', ['feedbacks' => $feedbacks, 'companies' => Company::all(), 'countries' => Country::all(),
            'users' => User::all(), 'offers' => Offer::all()]);
    }

    public function add($orderId, $userId, $ordersOpenedId, Request $request)
    {
        if ($request->method('post')) {
            $result = [
                'success' => false,
                'message' => trans('alerts.data-not-added'),
            ];
            $user = User::find($userId);
            $mistakes = $request->input('mistakes');
            if (!empty($mistakes)) {
                foreach ($mistakes as $key => $mistake) {
                    $mistakes[$key] = ['type' => $mistake, 'active' => true];
                }
            }
            $newFeedback = Feedback::create(['user_id' => $userId, 'order_id' => $orderId, 'orders_opened_id' => $ordersOpenedId,
                'company_id' => $user->company_id, 'moderator_id' => auth()->user()->id,
                'type' => $request->input('type'), 'mistakes' => !empty($mistakes) ? json_encode($mistakes) : 0,
                'status' => 'opened', 'read' => 2]);

            if ($newFeedback) {
                $comment = new Comment();
                $comment->order_id = $orderId;
                $comment->user_id = auth()->user()->id;
                $comment->text = $request->input('comment');
                $comment->entity = 'feedback';
                $comment->feedback_id = $newFeedback->id;
                $comment->timestamps = false;
                $comment->date = now();


                if ($comment->save()) {
                    $html = view('feedbacks.feedback-ajax', [
                        'date' => $newFeedback->created_at,
                    ])->render();
                    $result['success'] = true;
                    $result['message'] = trans('alerts.data-successfully-added');
                    $result['html'] = $html;
                }
            }
            return response()->json($result);
        }
    }

    public function addNewComment(Request $request)
    {
        if ($request->method('post')) {
            $feedback = Feedback::find($request->input('feedback_id'));
            if (auth()->user()->role_id == 2) {
                $feedback->read = 2;
                $feedback->save();
            }
            if (auth()->user()->role_id == 1) {
                $feedback->read = 2;
                $feedback->save();
            }

            $comment = new Comment();
            $comment->order_id = !empty($feedback->order_id) ? $feedback->order_id : $request->input('orderId');
            $comment->user_id = auth()->user()->id;
            $comment->text = $request->input('new_comment');
            $comment->entity = 'feedback';
            $comment->timestamps = false;
            $comment->feedback_id = $feedback->id;
            $comment->date = now();
            $comment->save();
            $feedback = Feedback::with('user', 'moderator', 'user.company')->where('id', $feedback->id)->first();
            $comments = Comment::with('user')->where([['order_id', $feedback->order_id], ['feedback_id', $feedback->id], ['entity', 'feedback']])->get();

            $result = [
                'success' => false,
                'message' => trans('alerts.data-not-added')
            ];
            if ($comments) {
                $html = view('feedbacks.feedback-comments-ajax', [
                    'comments' => $comments,
                    'feedback' => $feedback,
                ])->render();
                $result['html'] = $html;
                $result['success'] = true;
                $result['message'] = trans('alerts.data-successfully-added');
            }

            return response()->json($result);
        }
    }

    public function show($id)
    {
        $callRecord = '';
        $feedback = Feedback::with('user', 'moderator', 'user.company')->where('id', $id)->first();
        if (auth()->user()->id == $feedback->user_id && $feedback->read == 2 || auth()->user()->id == $feedback->moderator_id && $feedback->read == 1) {
            $feedback->read = 0;
            $feedback->save();
        }
        if (empty($feedback->user_id && auth()->user()->role_id != 1)) {
            $feedback->user_id = auth()->user()->id;
            $feedback->save();
        }
        if (!empty($feedback->mistakes)) {
            foreach (json_decode($feedback->mistakes) as $mistake) {
                $mistakes[] = $mistake->type;
                $mistakeActive[] = $mistake->active;
                $feedback->mistakes = OperatorMistake::whereIn('id', $mistakes)->get();
                $feedback->mistakes->active = $mistakeActive;
            }
        }

        $orderOpened = OrdersOpened::find($feedback->orders_opened_id);
        if (!empty($orderOpened)) {
            $callRecord = DB::table('call_progress_log as cpl')->select('file')
                ->where('unique_id', $orderOpened->unique_id)->first();
        }

        $comments = Comment::with('user')->where([['order_id', $feedback->order_id], ['feedback_id', $id], ['entity', 'feedback']])->get();

        return view('feedbacks.feedback-show', ['feedback' => $feedback, 'comments' => $comments, 'callRecord' => $callRecord]);
    }

    /*изменение статуса фидбека*/
    public function changeStatus(Request $request)
    {
        if ($request->method('post')) {

            $feedback = Feedback::where('id', $request->input('feedbackId'))->first();
            if ($feedback) {
                $feedback->status = $request->input('status');

                if (!empty($feedback->mistakes)) {
                    $newMistakes = [];
                    foreach (json_decode($feedback->mistakes, true) as $key => $mistake) {
                        if(!empty($request->input('mistakes'))){
                            if (in_array($mistake['type'], $request->input('mistakes'))) {
                                $mistake['active'] = true;
                            } else {
                                $mistake['active'] = false;
                            }
                        }
                        else{
                            $mistake['active'] = false;
                        }


                        $newMistakes[] = $mistake;
                    }
                }

                if (!empty($newMistakes)) {
                    $feedback->mistakes = json_encode($newMistakes);
                }

                if ($feedback->save()) {
                    return response()->json([
                        'success' => true,
                        'message' => trans('alerts.data-successfully-changed')
                        ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => trans('alerts.data-not-changed')
                ]);
            }
        }
    }

    /*get users by company*/
    public static function getUsersByCompany($companyId)
    {
        return response()->json([
            'html' => view('feedbacks.operators-block-ajax',
                ['operators' => UserRepository::getUsersByCompanyId($companyId)])->render()]);
    }

    /*process sended failed ticket*/
    public function sendTicket(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        $orderId = !empty($request->input('set-order')) ? $request->input('set-order') : Null;


        $companyId = !empty($request->input('company_id')) ? $request->input('company_id') : NULL;
        $userId = intval($request->input('operator'));
        $mistakes = $request->input('mistakes');
        if (!empty($mistakes)) {
            foreach ($mistakes as $key => $mistake) {
                $mistakes[$key] = ['type' => $mistake, 'active' => true];
            }
        }
        $newFeedback = Feedback::create(['user_id' => $userId, 'order_id' => $orderId, 'orders_opened_id' => NULL,
            'company_id' => $companyId, 'moderator_id' => auth()->user()->id,
            'type' => $request->input('type'), 'mistakes' => !empty($mistakes) ? json_encode($mistakes) : 0,
            'status' => 'opened', 'read' => 2]);

        if ($newFeedback) {
            $comment = new Comment();
            $comment->order_id = $orderId;
            $comment->user_id = auth()->user()->id;
            $comment->text = $request->input('comment');
            $comment->entity = 'feedback';
            $comment->feedback_id = $newFeedback->id;
            $comment->timestamps = false;
            $comment->date = now();


            $order = DB::table('orders AS o')
                ->select('o.id', 'f.id as feedback_id', 'f.created_at as feedback_created_at'
                )->leftJoin('feedback AS f', 'f.order_id', '=', 'o.id')->where('o.id', $orderId)->first();

            if ($comment->save()) {
                $html = view('feedbacks.feedback-added-ajax', [
                    'order' => $order, 'feedbackId'=> $newFeedback->id])->render();
                return response()->json([
                    'success' => true,
                    'html'    => $html,
                    'orderId' => $orderId,
                    'message' => trans('alerts.data-successfully-added')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => trans('alerts.data-not-added')
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('alerts.data-not-added')
            ]);
        }
    }

    public function sendInfoFaultTicket(Request $request)
    {
        if ($request->method('post')) {
            if (auth()->user()->role_id == 1) {
                $read = 1;
            } else {
                $read = 2;
            }
            $this->validate($request, [
                'title' => 'required',
                'comment' => 'required',
                'operator' => 'required',
            ]);

            $user = User::find($request->input('operator'));
            $userCompanyId = !empty($user) ? $user->company_id : 0;
            $newFeedback = Feedback::create(['user_id' => $request->input('operator'),
                'company_id' => !empty($request->input('company_id')) ? $request->input('company_id')
                    : $userCompanyId, 'moderator_id' => auth()->user()->id,
                'type' => $request->input('ticket-type'), 'title' => $request->input('title'),
                'status' => 'opened', 'read' => $read]);

            if ($newFeedback) {
                $comment = new Comment();
                $comment->order_id = 0;
                $comment->user_id = auth()->user()->id;
                $comment->text = $request->input('comment');
                $comment->entity = 'feedback';
                $comment->feedback_id = $newFeedback->id;
                $comment->timestamps = false;
                $comment->date = now();

                if ($comment->save()) {
                    $html = view('feedbacks.update-mistakes-table-ajax', [
                        'feedback' => Feedback::with('user', 'order', 'moderator', 'user.company')->where('id', $newFeedback->id)->first()
                    ])->render();
                    return response()->json([
                        'success' => true,
                        'html'    => $html,
                        'message' => trans('alerts.data-successfully-added')
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => trans('alerts.ticket-not-added')
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => trans('alerts.ticket-not-added')
                ]);
            }
        }
    }


}
