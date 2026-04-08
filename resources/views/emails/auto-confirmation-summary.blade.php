<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .header { background-color: #059669; color: #ffffff; text-align: center; padding: 32px 24px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .content { padding: 32px 24px; }
        .content p { line-height: 1.6; font-size: 15px; margin-bottom: 20px; }
        .booking-item { border-bottom: 1px solid #f1f5f9; padding: 12px 0; }
        .booking-item:last-child { border-bottom: none; }
        .booking-time { font-weight: 700; color: #059669; font-size: 14px; }
        .booking-name { font-weight: 600; color: #1e293b; display: block; }
        .booking-service { font-size: 13px; color: #64748b; }
        .footer { background-color: #f1f5f9; padding: 24px; text-align: center; font-size: 13px; color: #64748b; }
        .badge { display: inline-block; padding: 2px 8px; background-color: #ecfdf5; color: #065f46; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Resumen Diario de Citas</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->business_name ?? $user->name }}</strong>,</p>
            <p>Se han confirmado automáticamente las citas que quedaron marcadas como "Pendiente" durante el día de hoy ({{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}).</p>
            
            <div style="margin-top: 10px;">
                @foreach($bookings as $booking)
                    <div class="booking-item" style="padding: 16px; background-color: #f8fafc; border-radius: 12px; margin-bottom: 12px; border: 1px solid #f1f5f9;">
                        <p style="margin: 0 0 8px 0; font-size: 14px;"><strong>Nombre del cliente:</strong> <span style="color: #1e293b;">{{ $booking->customer_name }}</span></p>
                        <p style="margin: 0 0 8px 0; font-size: 14px;"><strong>Hora:</strong> <span style="color: #1e293b;">{{ \Carbon\Carbon::parse($booking->starts_at)->timezone($user->timezone ?? 'Europe/Madrid')->format('H:i') }}</span></p>
                        <p style="margin: 0; font-size: 14px;"><strong>Servicio:</strong> <span class="badge">AUTO-CONFIRMADA</span> <span style="color: #64748b; font-size: 13px;">({{ $booking->service->name }})</span></p>
                    </div>
                @endforeach
            </div>

            <p style="margin-top: 24px; color: #64748b; font-size: 13px;">
                Este proceso se ejecuta cada noche para asegurar que tu agenda quede cerrada correctamente y las estadísticas de tu negocio sean precisas.
            </p>
        </div>
        
        <div class="footer">
            <p>Gestionado por <a href="{{ config('app.url') }}" style="color: #10b981; font-weight: 600; text-decoration: none;">Citalify</a></p>
        </div>
    </div>
</body>
</html>
