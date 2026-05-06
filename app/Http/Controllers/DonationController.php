<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Mostrar página de donaciones
     */
    public function index()
    {
        // Totales globales
        $totalDonated = Donation::where('status', 'completed')
            ->sum('amount');
        $totalDonors = Donation::where('status', 'completed')
            ->distinct('user_id')
            ->count('user_id');
        $animalsHelped = ceil($totalDonated / 30); // Aproximado: 30€ por animal

        // Últimas donaciones (público)
        $recentDonations = Donation::where('status', 'completed')
            ->with('user:id,nombre,foto_perfil')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Mis donaciones (si está autenticado)
        $myDonations = null;
        if (auth()->check()) {
            $myDonations = Donation::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('sections.donate', [
            'totalDonated'    => $totalDonated,
            'totalDonors'     => $totalDonors,
            'animalsHelped'   => $animalsHelped,
            'recentDonations' => $recentDonations,
            'myDonations'     => $myDonations,
        ]);
    }

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
