<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #475569; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; padding: 40px; border: 1px solid #e2e8f0; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; margin-bottom: 32px; }
        .header h1 { color: #1e293b; font-size: 24px; margin: 0; font-weight: 800; }
        .content { margin-bottom: 32px; }
        .content p { margin-bottom: 16px; }
        .features { background-color: #f1f5f9; padding: 24px; border-radius: 16px; margin: 24px 0; list-style: none; }
        .features li { margin-bottom: 12px; display: flex; align-items: center; color: #334155; font-size: 14px; }
        .footer { text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #f1f5f9; pt: 24px; }
        .button { display: inline-block; padding: 14px 28px; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 15px; transition: all 0.2s; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Suscripción cancelada</h1>
        </div>
        
        <div class="content">
            <p>Hola, <strong>{{ $user->name }}</strong>,</p>
            <p>Te confirmamos que tu suscripción a Citalify ha sido cancelada correctamente.</p>
            
            <div class="features">
                <div style="font-weight: bold; margin-bottom: 12px; color: #1e293b;">Estado de tu cuenta:</div>
                <div style="margin-bottom: 8px;">• Tu agenda ya no es visible para los clientes.</div>
                <div style="margin-bottom: 8px;">• No se realizarán más cargos automáticos.</div>
                <div>• Tus datos y configuración siguen guardados por si decides volver.</div>
            </div>

            <p>Si ha sido un error o simplemente quieres retomar tu actividad, puedes volver a activar tu cuenta en cualquier momento contratando un nuevo plan desde tu panel.</p>
            
            <div style="text-align: center; margin-top: 32px;">
                <a href="{{ config('app.url') }}/dashboard" class="button">Volver a activar mi cuenta</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Citalify. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
