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
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>Citalify</h1></div>
        <div class="content">
            <h2>Acceso a tus reservas</h2>
            <p>Has solicitado acceder a tu historial de reservas.</p>
            <p>Introduce el siguiente código en la página para ver y gestionar tus citas:</p>
            
            <div class="otp-code">{{ $otp }}</div>
            
            <p>Este código caduca en 15 minutos.</p>
            <p>Si no has solicitado este acceso, puedes ignorar este correo.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Citalify. Gestión de reservas inteligente.</p>
        </div>
    </div>
</body>
</html>
