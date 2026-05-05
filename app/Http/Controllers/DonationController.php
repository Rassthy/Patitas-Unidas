<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Crear orden de PayPal
     */
    public function createOrder(Request $request, PayPalService $paypal)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $order = $paypal->createOrder($request->amount);

        return response()->json([
            'id' => $order['id']
        ]);
    }

    /**
     * Capturar orden de PayPal y guardar donación
     */
    public function captureOrder(Request $request, PayPalService $paypal)
    {
        $orderId = $request->input('orderID');

        if (!$orderId) {
            return response()->json([
                'error' => 'Missing orderID'
            ], 422);
        }

        try {
            // 👇 toda la lógica de PayPal está en el service
            $data = $paypal->captureOrder($orderId);

            $capture = $data['purchase_units'][0]['payments']['captures'][0] ?? null;

            if ($capture && ($data['status'] ?? '') === 'COMPLETED') {

                Donation::create([
                    'user_id'         => auth()->id(),
                    'paypal_order_id' => $orderId,
                    'amount'          => $capture['amount']['value'] ?? 0,
                    'currency'        => 'EUR',
                    'status'          => 'completed',
                    'payer_email'     => $data['payer']['email_address'] ?? null,
                ]);
            }

            return response()->json([
                'status' => 'ok',
                'data'   => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}