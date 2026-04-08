<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .header { background-color: #059669; color: #ffffff; text-align: center; padding: 32px 24px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 32px 24px; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0; }
        .info-item { margin-bottom: 12px; font-size: 15px; }
        .info-label { font-weight: 700; color: #64748b; width: 80px; display: inline-block; }
        .reminder-box { background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 16px; border-radius: 0 8px 8px 0; margin-top: 24px; font-size: 14px; }
        .footer { background-color: #f1f5f9; padding: 24px; text-align: center; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>¡Cita Confirmada!</h1></div>
        <div class="content">
            <h2>Todo listo, {{ $booking->customer_name }}</h2>
            <p>Tu cita en <strong>{{ $booking->user->business_name }}</strong> ha sido agendada correctamente.</p>
            
            <div class="info-box">
                <div class="info-item"><span class="info-label">Día:</span> {{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->user->timezone ?? 'Europe/Madrid')->translatedFormat('l, d \d\e F') }}</div>
                <div class="info-item"><span class="info-label">Hora:</span> {{ \Carbon\Carbon::parse($booking->starts_at)->timezone($booking->user->timezone ?? 'Europe/Madrid')->format('H:i') }}</div>
                <div class="info-item"><span class="info-label">Servicio:</span> {{ $booking->service->name }}</div>
                <div class="info-item"><span class="info-label">Lugar:</span> {{ $booking->user->address ?: 'Consultar con el negocio' }}</div>
            </div>

            <div class="reminder-box">
                <strong>💡 Recordatorio importante:</strong>
                <p style="margin: 8px 0 0 0;">El día anterior a tu cita, a las <strong>15:00</strong>, recibirás un nuevo correo con un enlace para confirmar tu asistencia o cancelar si no puedes venir.</p>
            </div>
            
            <p style="margin-top: 32px;">¡Gracias por reservar con Citalify!</p>
        </div>
        <div class="footer">
            <p>Gestionado por <a href="{{ config('app.url') }}" style="color: #10b981; text-decoration: none;">Citalify</a></p>
        </div>
    </div>
</body>
</html>
