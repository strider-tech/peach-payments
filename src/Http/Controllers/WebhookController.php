<?php

namespace StriderTech\PeachPayments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // todo modifications with transaction id $id
        $id = $request->input('id');
        $resourcePath = $request->input('resourcePath');

        $paymentService = app()->get('peachpayments');
        $notificationResponse = $paymentService->getNotificationStatus($resourcePath);

        return new JsonResponse($notificationResponse);
    }
}
