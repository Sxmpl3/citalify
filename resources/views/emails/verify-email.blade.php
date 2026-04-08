<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            margin-top: 40px;
            margin-bottom: 40px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #059669; /* emerald-600 */
            color: #ffffff;
            text-align: center;
            padding: 32px 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 32px 24px;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 16px;
            color: #475569;
        }
        .button-wrap {
            text-align: center;
            margin: 32px 0;
        }
        .button-confirm {
            display: inline-block;
            background-color: #059669; /* emerald-600 */
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }
        .divider {
            border-top: 1px solid #e2e8f0;
            margin: 32px 0;
        }
        .subtext {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a Citalify!</h1>
        </div>
        
        <div class="content">
            <p>Hola, {{ $user->name }},</p>
            <p>Gracias por unirte a <strong>Citalify</strong>. Estamos encantados de ayudarte a gestionar tu negocio.</p>
            <p>Para empezar a configurar tu agenda y recibir reservas, por favor confirma tu dirección de correo electrónico haciendo clic en el botón de abajo:</p>
            
            <div class="button-wrap">
                <a href="{{ $url }}" class="button-confirm">Verificar correo electrónico</a>
            </div>
            
            <p>Si no has creado ninguna cuenta, no es necesario realizar ninguna acción adicional.</p>
            
            <p>Saludos,<br>El equipo de Citalify</p>

            <div class="divider"></div>

            <p class="subtext">
                Si tienes problemas para hacer clic en el botón "Verificar correo electrónico", copia y pega la siguiente URL en tu navegador:
                <br>
                <a href="{{ $url }}" style="color: #64748b; word-break: break-all;">{{ $url }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Citalify. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
