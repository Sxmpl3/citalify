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
            background-color: #059669; /* emerald-600 */
            color: #ffffff;
            text-align: center;
            padding: 32px 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 15px;
            opacity: 0.9;
        }
        .content {
            padding: 32px 24px;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 16px;
            line-height: 1.6;
            font-size: 16px;
        }
        .details-box {
            background-color: #ecfdf5; /* emerald-50 */
            border: 1px solid #a7f3d0; /* emerald-200 */
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .details-box p {
            margin: 0 0 8px 0;
            font-size: 15px;
            color: #065f46; /* emerald-800 */
        }
        .details-box p:last-child {
            margin-bottom: 0;
        }
        .details-box strong {
            color: #047857; /* emerald-700 */
        }
        .button-wrap {
            text-align: center;
            margin: 32px 0;
        }
        .button-confirm {
            display: inline-block;
            background-color: #059669; /* emerald-600 */
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            margin: 0 8px 16px 8px;
        }
        .button-cancel {
            display: inline-block;
            background-color: #ffffff;
            color: #dc2626; /* red-600 */
            font-weight: 600;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            border: 2px solid #dc2626;
            margin: 0 8px 16px 8px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }
        .warning-text {
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
            margin-top: -16px;
            margin-bottom: 32px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Hola, {{ $booking->customer_name }}!</h1>
            <p>Este es un recordatorio de tu cita de mañana.</p>
        </div>
        
        <div class="content">
            <p>Te escribimos de parte de <strong>{{ $business->business_name }}</strong> para recordarte que mañana tienes una reserva con nosotros.</p>
            
            <div class="details-box">
                <p><strong>Día:</strong> Mañana, {{ \Carbon\Carbon::parse($booking->starts_at)->timezone($business->timezone ?? 'Europe/Madrid')->translatedFormat('d \d\e F \d\e Y') }}</p>
                <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($booking->starts_at)->timezone($business->timezone ?? 'Europe/Madrid')->format('H:i') }}</p>
                <p><strong>Servicio:</strong> {{ $booking->service->name }}</p>
                <p><strong>Lugar:</strong> {{ $business->address ?: 'Consultar con el negocio' }}</p>
            </div>
            
            <p>Por favor, confirma tu asistencia o cancela si no puedes asistir:</p>
            
            <div class="button-wrap">
                <a href="{{ $confirmationUrl }}" class="button-confirm">Confirmar cita</a>
                <a href="{{ $cancellationUrl }}" class="button-cancel">Cancelar cita</a>
            </div>
            <p class="warning-text">La cancelación es definitiva y liberará tu hueco para otro cliente.</p>
            
            <p>Si tienes cualquier duda, puedes contactar con nosotros en el número <strong>{{ $business->phone }}</strong>.</p>
            
            <p>¡Gracias por confiar en nosotros!</p>
        </div>
        
        <div class="footer">
            <p>Este correo electrónico ha sido generado automáticamente por el sistema de reservas.</p>
            <p>Reservado a través de <a href="{{ config('app.url') }}">Citalify</a></p>
        </div>
    </div>
</body>
</html>
