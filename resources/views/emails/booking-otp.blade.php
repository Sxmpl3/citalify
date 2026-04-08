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
        .content { padding: 32px 24px; text-align: center; }
        .otp-code { font-size: 48px; font-weight: 800; color: #059669; letter-spacing: 8px; margin: 32px 0; padding: 16px; background-color: #ecfdf5; border-radius: 12px; display: inline-block; }
        .footer { background-color: #f1f5f9; padding: 24px; text-align: center; font-size: 13px; color: #64748b; }
        .spam-note { background-color: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; font-size: 14px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>Citalify</h1></div>
        <div class="content">
            <h2>Verifica tu reserva</h2>
            <p>Hola, <strong>{{ $pendingBooking->customer_name }}</strong>. Casi hemos terminado.</p>
            <p>Introduce el siguiente código en la página de reserva para confirmar tu cita en <strong>{{ $pendingBooking->user->business_name }}</strong>:</p>
            
            <div class="otp-code">{{ $pendingBooking->verification_code }}</div>
            
            <p>Este código caduca en 20 minutos.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Citalify. Gestión de reservas inteligente.</p>
        </div>
    </div>
</body>
</html>
