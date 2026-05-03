<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        // Optional: Verificación de firma del webhook
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // Añadir al .env si deseas verificar la firma.

        $event = null;

        try {
            if ($endpoint_secret && $sig_header) {
                $event = Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } else {
                $event = \Stripe\Event::constructFrom(
                    json_decode($payload, true)
                );
            }
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // Fulfill the purchase...
                $this->handleCheckoutSessionCompleted($session);
                break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleInvoicePaymentSucceeded($invoice);
                break;
            default:
                Log::info('Received unknown event type ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        $userId = $session->client_reference_id;
        $customerId = $session->customer;

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'stripe_customer_id' => $customerId,
                    'stripe_subscription_id' => $session->subscription,
                    'trial_ends_at' => now()->addDays(30), // La prueba configurada en Stripe o en local
                    'plan_id' => \App\Models\Plan::where('slug', 'basico')->first()?->id ?? \App\Models\Plan::first()?->id ?? null,
                ]);
            }
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $customerId = $invoice->customer;
        
        if ($customerId) {
            $user = User::where('stripe_customer_id', $customerId)->first();
            
            if ($user) {
                $amount = $invoice->amount_paid / 100;
                
                // Si el importe es 0, es la prueba gratuita inicial. 
                // Ese correo ya lo enviamos en CheckoutController al volver a la web, así evitamos duplicados.
                if ($amount > 0) {
                    $invoiceData = [
                        'amount' => $amount,
                        'date' => \Carbon\Carbon::createFromTimestamp($invoice->created)->format('d/m/Y'),
                        'is_trial' => false,
                    ];
                    
                    // Generar el PDF
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', [
                        'user' => $user,
                        'amount' => $amount,
                        'date' => $invoiceData['date'],
                        'is_trial' => false,
                    ]);
                    $pdfContent = $pdf->output();
                    
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\InvoiceMail($user, $invoiceData, $pdfContent));
                }
            }
        }
    }
}
