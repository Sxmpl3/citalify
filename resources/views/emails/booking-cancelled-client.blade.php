<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Cancelada</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155;">
    <div style="max-w-xl; margin: 0 auto; padding: 40px 20px;">
        <div style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);">
            <!-- Header -->
            <div style="background-color: #10b981; padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em;">{{ $business->business_name }}</h1>
            </div>
            
            <!-- Content -->
            <div style="padding: 40px 30px;">
                <h2 style="margin-top: 0; color: #1e293b; font-size: 20px;">Hola {{ $booking->customer_name }},</h2>
                <p style="margin-bottom: 24px; line-height: 1.6; font-size: 16px;">
                    Lamentamos informarte que tu cita programada <strong>ha sido anulada</strong> debido a un imprevisto en nuestros horarios para ese día.
                </p>
                
                <div style="background-color: #f1f5f9; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #0f172a; font-size: 16px;">Detalles de la cita anulada:</h3>
                    <ul style="list-style-type: none; padding: 0; margin: 0; line-height: 1.8;">
                        <li><strong style="color: #475569;">Servicio:</strong> {{ $booking->service->name ?? 'Servicio' }}</li>
                        <li><strong style="color: #475569;">Fecha:</strong> {{ \Carbon\Carbon::parse($booking->starts_at)->format('d/m/Y') }}</li>
                        <li><strong style="color: #475569;">Hora:</strong> {{ \Carbon\Carbon::parse($booking->starts_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->ends_at)->format('H:i') }}</li>
                    </ul>
                </div>
                
                <p style="margin-bottom: 0; line-height: 1.6; font-size: 16px;">
                    Puedes volver a agendar una cita en nuestra <a href="{{ url($business->business_slug) }}" style="color: #10b981; text-decoration: none; font-weight: 600;">página de reservas</a> en otro horario disponible.
                </p>
            </div>
            
            <!-- Footer -->
            <div style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0; color: #94a3b8; font-size: 14px;">Gestionado de forma inteligente por <strong>citalify</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
