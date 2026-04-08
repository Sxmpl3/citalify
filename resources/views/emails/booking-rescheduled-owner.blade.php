<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .header { background-color: #059669; color: #ffffff; text-align: center; padding: 32px 24px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .content { padding: 32px 24px; }
        .details-box { background-color: #f0fdf4; border: 1px solid #d1fae5; border-radius: 16px; padding: 24px; margin-bottom: 24px; }
        .old-time { text-decoration: line-through; color: #94a3b8; font-size: 13px; margin-bottom: 4px; display: block; }
        .new-time { color: #059669; font-weight: 700; font-size: 16px; }
        .footer { background-color: #f8fafc; padding: 24px; text-align: center; font-size: 13px; color: #94a3b8; border-t: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cita Reprogramada</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $booking->user->name }}</strong>,</p>
            <p>El cliente <strong>{{ $booking->customer_name }}</strong> ha cambiado la fecha/hora de su cita desde el portal de autogestión.</p>
            
            <div class="details-box">
                <p style="margin-top:0"><strong>Servicio:</strong> {{ $booking->service->name }}</p>
                
                <div style="margin-bottom: 16px;">
                    <span class="old-time">Antes: {{ \Carbon\Carbon::parse($oldStartsAt)->timezone($booking->user->timezone ?? 'Europe/Madrid')->translatedFormat('l d \d\e F \a \l\a\s H:i') }}</span>
                    <span class="new-time">Ahora: {{ $booking->starts_at->setTimezone($booking->user->timezone ?? 'Europe/Madrid')->translatedFormat('l d \d\e F \a \l\a\s H:i') }}</span>
                </div>
            </div>
            
            <p>Tu agenda se ha actualizado automáticamente con este cambio.</p>
        </div>
        <div class="footer">
            <p>Enviado automáticamente por Citalify</p>
        </div>
    </div>
</body>
</html>
