<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            margin-top: 40px;
            margin-bottom: 40px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #10b981; /* emerald-500 */
            color: #ffffff;
            text-align: center;
            padding: 32px 24px;
        }
        .header img {
            height: 48px;
            margin-bottom: 16px;
            border-radius: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 32px 24px;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 16px;
            color: #475569;
        }
        .invoice-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: center;
        }
        .amount {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin: 16px 0;
        }
        .invoice-details {
            text-align: left;
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .invoice-details th, .invoice-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .invoice-details th {
            text-align: left;
            color: #64748b;
            font-weight: 600;
        }
        .invoice-details td {
            text-align: right;
            color: #334155;
            font-weight: 500;
        }
        .button-wrap {
            text-align: center;
            margin: 32px 0;
        }
        .button-primary {
            display: inline-block;
            background-color: #10b981; /* emerald-500 */
            color: #ffffff !important;
            font-weight: 600;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Gracias por tu pago!</h1>
        </div>
        
        <div class="content">
            <p>Hola, {{ $user->name }},</p>
            @if(isset($invoiceData['is_trial']) && $invoiceData['is_trial'])
                <p>Tu periodo de prueba de 30 días ha comenzado con éxito. No se ha realizado ningún cargo en tu tarjeta. A continuación tienes los detalles de esta transacción:</p>
            @else
                <p>Hemos procesado correctamente tu pago para la suscripción de Citalify. A continuación tienes los detalles de tu factura:</p>
            @endif
            
            <div class="invoice-box">
                <div style="font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total pagado</div>
                <div class="amount">{{ number_format($invoiceData['amount'], 2, ',', '.') }} €</div>
                
                <table class="invoice-details">
                    <tr>
                        <th>Fecha</th>
                        <td>{{ $invoiceData['date'] }}</td>
                    </tr>
                    <tr>
                        <th>Concepto</th>
                        <td>Suscripción Citalify {{ isset($invoiceData['is_trial']) && $invoiceData['is_trial'] ? '(Prueba gratuita)' : 'Plan Básico' }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Hemos adjuntado el recibo en formato PDF a este correo electrónico.</p>
            
            <p>Si tienes alguna duda o problema, puedes responder a este correo y te ayudaremos lo antes posible.</p>
            
            <p>Saludos,<br>El equipo de Citalify</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Citalify. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
