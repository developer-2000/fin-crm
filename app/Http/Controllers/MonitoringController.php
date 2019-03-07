<?php

namespace App\Http\Controllers;  

use App\Models\Campaign;
use App\Models\NP;
use App\Models\Order;
use Illuminate\Http\Request;
use \App\Models\User;
use App\Models\CallProcessing;

class MonitoringController extends BaseController
{
    /**
     * Данные по агентам
     */
    public function getMonitoringAgentsAjax(Request $request, User $authModel)
    {
        if ($request->isMethod('post')) {
            return response()->json([
                'data' => $authModel->getAgentsMonitor(),
            ]);
        }
        abort(404);
    }

    /**
     * Мониторинг по компаниям
     */
    public function monitoringCompany(User $authModel, Campaign $companyElastixModel)
    {
        return view('monitoring.company', [
            'operators'   => $authModel->getOperators(),
            'campaigns'   => $companyElastixModel->getAllCompanyElastix()
        ]); 
    }

    function processing(Campaign $companyElastixModel)
    {
        $companies = $companyElastixModel->getNameCompanyElastix();
        $result['hp'] = [];
        $result['order'] = [];

        if ($companies) {
            foreach ($companies as $company) {
                if ($company->company_id) {
                    $result['hp'][] = $company;
                } else {
                    $result['order'][] = $company;
                }
            }
        }
        return view('monitoring.monitoring_calls', $result);
    }

    /**
     * Данные по компаниям(лист ожиданий)
     */
    public function getMonitoringCompanyAjax(Request $request, User $authModel, Campaign $companyElastixModel, Order $orderModel)
    {
        if ($request->isMethod('post')) {
            $result = $authModel->getCampaignMonitor();
            $campaigns = $companyElastixModel->getNameCompanyElastix();
            $data = [];
            $countries = [];
            if ($result->status == 200) {
                $data = $result->data;
                $countries = $orderModel->getCountriesOrders($data);
            }
            return response()->json([
                'data' => $data,
                'campaigns' => $campaigns,
                'countries' => $countries,
            ]);
        }
        abort(404);
    }

    /**
     * Мониторинг целей(ajax)
     */
    public function monitoringTargetsAjax(Request $request, Order $orderModel, Targets $targetsModel, NP $npModel, TargetsFinal $targetsFinalModel)
    {
        if ($request->isMethod('post')) {
            return response()->json([
                'orders' => $orderModel->ordersForMonitoring($targetsFinalModel, $targetsModel, $npModel, true)
                ]);
        }
        abort(404);
    }

    public function getMonitoringCallsAjax(Request $request, CallProcessing $callProcessingModel)
    {
        if($request->isMethod('POST')) {
            return response()->json(['data' => $callProcessingModel->updateCountCalls()]);
        }
        abort(404);
    }

    public function monitoringOrdersByWeight(Campaign $companyElastixModel)
    {
        return view('monitoring.monitoringOrdersByWeight',[
            'campaigns' => $companyElastixModel->getAllCompanyElastix()
        ]);
    }
}