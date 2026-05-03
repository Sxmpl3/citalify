<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionCancelledMail;

class SubscriptionController extends Controller
{
    public function cancel(Request $request)
    {
        $user = auth()->user();

        if (!$user->stripe_customer_id) {
            return back()->with('error', 'No se encontró información de pago en tu cuenta.');
        }

        // Enviamos el correo de confirmación PRIMERO para asegurar que llega
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SubscriptionCancelledMail($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de cancelación: ' . $e->getMessage());
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Buscamos todas las suscripciones del cliente que no estén canceladas
            $subscriptions = Subscription::all([
                'customer' => $user->stripe_customer_id,
                'status' => 'active',
            ]);

            // Si no hay activas, buscamos en periodo de prueba
            if (count($subscriptions->data) === 0) {
                $subscriptions = Subscription::all([
                    'customer' => $user->stripe_customer_id,
                    'status' => 'trialing',
                ]);
            }

            if (count($subscriptions->data) > 0) {
                foreach ($subscriptions->data as $sub) {
                    $sub->cancel();
                }
            }

            // Actualizamos nuestra base de datos local (mantenemos trial_ends_at como registro)
            $user->update([
                'plan_id' => null,
            ]);

            // Cerramos la sesión
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Tu plan ha sido cancelado correctamente. Te hemos enviado un correo de confirmación.');

        } catch (\Exception $e) {
            Log::error('Stripe Cancel Error: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al comunicar con Stripe: ' . $e->getMessage());
        }
    }
}
