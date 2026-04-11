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
        .btn { display: inline-block; background-color: #059669; color: #ffffff !important; padding: 14px 28px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 16px; margin: 32px 0; }
        .btn:hover { background-color: #047857; }
        .footer { background-color: #f1f5f9; padding: 24px; text-align: center; font-size: 13px; color: #64748b; }
        .note { margin-top: 24px; font-size: 14px; color: #64748b; line-height: 1.5; }
        .link-break { word-break: break-all; color: #059669; font-size: 12px; margin-top: 16px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>Citalify</h1></div>
        <div class="content">
            <h2>Restablece tu contraseña</h2>
            <p>Hola.</p>
            <p>Has recibido este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta de <strong>Citalify</strong>.</p>
            
            <a href="{{ $url }}" class="btn">Restablecer contraseña</a>
            
            <p>Este enlace de restablecimiento de contraseña caducará en 60 minutos.</p>
            
            <p class="note">Si no has solicitado un restablecimiento de contraseña, no es necesario realizar ninguna otra acción.</p>
            
            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 32px 0;">
            
            <p class="note">Si tienes problemas para hacer clic en el botón "Restablecer contraseña", copia y pega la siguiente URL en tu navegador:</p>
            <span class="link-break">{{ $url }}</span>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Citalify. Gestión de reservas inteligente.</p>
        </div>
    </div>
</body>
</html>
