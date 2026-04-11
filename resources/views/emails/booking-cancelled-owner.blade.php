<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background-color: #dc2626; color: #ffffff; text-align: center; padding: 32px 24px; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { padding: 32px 24px; }
        .details-box { background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .footer { background-color: #f1f5f9; padding: 24px; text-align: center; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cita Cancelada</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $booking->user->name }}</strong>,</p>
            <p>Un cliente ha cancelado una cita:</p>
            
            <div class="details-box">
                <p><strong>Cliente:</strong> {{ $booking->customer_name }}</p>
                <p><strong>Servicio:</strong> {{ $booking->service->name }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->user->timezone ?? 'Europe/Madrid')->translatedFormat('l, d \d\e F') }}</p>
                <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->user->timezone ?? 'Europe/Madrid')->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->ends_at)->timezone($booking->user->timezone ?? 'Europe/Madrid')->format('H:i') }}</p>
            </div>
            
            <p>El hueco ya está disponible de nuevo en tu calendario para otros clientes.</p>
        </div>
        <div class="footer">
            <p>Enviado automáticamente por Citalify</p>
        </div>
    </div>
</body>
</html>
