<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de citas anuladas</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155;">
    <div style="max-w-2xl; margin: 0 auto; padding: 40px 20px;">
        <div style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);">
            <!-- Header -->
            <div style="background-color: #0f172a; padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em;">Citalify</h1>
            </div>
            
            <!-- Content -->
            <div style="padding: 40px 30px;">
                <h2 style="margin-top: 0; color: #1e293b; font-size: 20px;">Hola {{ $business->name ?? 'Propietario' }},</h2>
                <p style="margin-bottom: 24px; line-height: 1.6; font-size: 16px;">
                    Has cerrado el horario para el día <strong>{{ \Carbon\Carbon::parse($dateString)->format('d/m/Y') }}</strong>. 
                    En consecuencia, se han anulado <strong style="color: #ef4444;">{{ $bookings->count() }} cita(s)</strong> que tenías programadas. 
                    Hemos enviado un email a aquellos clientes de los cuales disponíamos de correo electrónico.
                </p>
                
                <h3 style="margin-top: 0; margin-bottom: 15px; color: #0f172a; font-size: 16px;">Resumen de citas anuladas:</h3>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                        <thead>
                            <tr>
                                <th style="background-color: #f1f5f9; padding: 12px; text-align: left; font-size: 14px; color: #475569; border-bottom: 2px solid #cbd5e1; border-top-left-radius: 8px;">Hora</th>
                                <th style="background-color: #f1f5f9; padding: 12px; text-align: left; font-size: 14px; color: #475569; border-bottom: 2px solid #cbd5e1;">Cliente</th>
                                <th style="background-color: #f1f5f9; padding: 12px; text-align: left; font-size: 14px; color: #475569; border-bottom: 2px solid #cbd5e1; border-top-right-radius: 8px;">Contacto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-variant-numeric: tabular-nums;">
                                    <strong>{{ \Carbon\Carbon::parse($booking->starts_at)->format('H:i') }}</strong>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                    <strong>{{ $booking->customer_name }}</strong><br>
                                    <span style="font-size: 12px; color: #64748b;">{{ $booking->service->name ?? 'Servicio' }}</span>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                    @if($booking->customer_phone)
                                        <div style="font-size: 14px;">☎️ {{ $booking->customer_phone }}</div>
                                    @endif
                                    @if($booking->customer_email)
                                        <div style="font-size: 14px; color: #64748b;">✉️ {{ $booking->customer_email }}</div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <p style="margin-bottom: 0; line-height: 1.6; font-size: 16px;">
                    Te recomendamos contactar a tus clientes si crees que no hayan recibido el aviso por email.
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
