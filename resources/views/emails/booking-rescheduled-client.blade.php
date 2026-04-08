<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .header { background-color: #10b981; color: #ffffff; text-align: center; padding: 40px 24px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .content { padding: 32px 24px; }
        .details-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin: 24px 0; }
        .detail-item { margin-bottom: 12px; font-size: 15px; }
        .detail-item strong { color: #1e293b; }
        .button { display: inline-block; background-color: #10b981; color: #ffffff; padding: 14px 28px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px; margin-top: 16px; transition: background-color 0.2s; }
        .footer { background-color: #f8fafc; padding: 24px; text-align: center; font-size: 13px; color: #94a3b8; border-t: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Cita Actualizada!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $booking->customer_name }}</strong>,</p>
            <p>Tu cita en <strong>{{ $booking->user->business_name }}</strong> ha sido reprogramada correctamente.</p>
            
            <div class="details-card">
                <div class="detail-item">
                    <strong>Servicio:</strong> {{ $booking->service->name }}
                </div>
                <div class="detail-item">
                    <strong>Nueva Fecha:</strong> {{ $booking->starts_at->setTimezone($booking->user->timezone ?? 'Europe/Madrid')->translatedFormat('l, d \d\e F') }}
                </div>
                <div class="detail-item">
                    <strong>Nueva Hora:</strong> {{ $booking->starts_at->setTimezone($booking->user->timezone ?? 'Europe/Madrid')->format('H:i') }}
                </div>
                <div class="detail-item">
                    <strong>Lugar:</strong> {{ $booking->user->address ?? 'Consultar con el negocio' }}
                </div>
            </div>
            
            <p>Si necesitas realizar más cambios o cancelar, puedes hacerlo en cualquier momento desde tu panel de gestión.</p>
            
            <a href="{{ route('customer.login') }}" class="button">Gestionar mis reservas</a>
        </div>
        <div class="footer">
            <p>Gracias por confiar en {{ $booking->user->business_name }} y Citalify</p>
        </div>
    </div>
</body>
</html>
