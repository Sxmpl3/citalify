<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function redirect(Request $request)
    {
        $user = auth()->user();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $subscription_data = [];
        if (! $user->hasHadTrial()) {
            $subscription_data['trial_period_days'] = 30;
        }

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => 'price_1TSvGRAmHMeZ8eQoxSDLSjDE',
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('register'), // Optional: where to go if they cancel
            'customer_email' => $user->email,
            'client_reference_id' => $user->id,
            'subscription_data' => $subscription_data,
        ]);

        return redirect()->away($checkout_session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            try {
                $session = Session::retrieve($sessionId);
                $user = auth()->user();

                if ($session->payment_status === 'paid' || $session->status === 'complete') {
                    $updateData = [
                        'stripe_customer_id' => $session->customer,
                        'plan_id' => \App\Models\Plan::where('slug', 'basico')->first()?->id ?? \App\Models\Plan::first()?->id ?? null,
                    ];

                    // Solo establecemos trial_ends_at si no tenía uno previo
                    if (! $user->trial_ends_at) {
                        $updateData['trial_ends_at'] = now()->addDays(30);
                    }

                    $user->update($updateData);

                    // Si es su primera vez (nueva prueba), enviamos la factura de $0
                    if (isset($updateData['trial_ends_at'])) {
                        $invoiceData = [
                            'amount' => 0,
                            'date' => now()->format('d/m/Y'),
                            'is_trial' => true,
                        ];

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', [
                            'user' => $user,
                            'amount' => 0,
                            'date' => $invoiceData['date'],
                            'is_trial' => true,
                        ]);

                        \Illuminate\Support\Facades\Mail::to($user->email)->send(
                            new \App\Mail\InvoiceMail($user, $invoiceData, $pdf->output())
                        );
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe Checkout Success Error: ' . $e->getMessage());
                // Silently fail, let the webhook handle it
            }
        }

        $user = auth()->user();
        $message = $user->hasHadTrial() 
            ? '¡Suscripción iniciada correctamente!' 
            : '¡Suscripción iniciada correctamente! Tienes 30 días de prueba.';

        // Si ya completó el onboarding, al dashboard. Si no, al onboarding.
        $redirectRoute = $user->onboarding_completed ? 'dashboard' : 'onboarding.step';
        $redirectParams = $user->onboarding_completed ? [] : ['step' => 1];

        return redirect()->route($redirectRoute, $redirectParams)
            ->with('status', $message);
    }
}
