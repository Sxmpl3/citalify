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
        .header p {
            margin: 8px 0 0 0;
            font-size: 15px;
            opacity: 0.9;
        }
        .content {
            padding: 32px 24px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 32px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 12px;
        }
        .badge-confirmed { background-color: #dcfce7; color: #166534; }
        .badge-pending { background-color: #fef9c3; color: #854d0e; }
        
        .booking-card {
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        .booking-time {
            font-weight: 700;
            font-size: 16px;
            color: #475569;
            width: 70px;
            border-right: 2px solid #f1f5f9;
            margin-right: 16px;
        }
        .booking-info {
            flex: 1;
        }
        .booking-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 15px;
            margin: 0;
        }
        .booking-phone {
            font-size: 13px;
            color: #64748b;
            margin: 4px 0 0 0;
        }
        .empty-state {
            text-align: center;
            padding: 32px;
            background-color: #f8fafc;
            border-radius: 12px;
            color: #94a3b8;
            font-style: italic;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Agenda de Mañana</h1>
            <p>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d \d\e F') }}</p>
        </div>
        
        <div class="content">
            <p>Hola, <strong>{{ $user->business_name }}</strong>. Aquí tienes el resumen de tus citas para mañana.</p>
            
            <!-- Confirmed Section -->
            <div class="section-title">
                Citas Confirmadas <span class="badge badge-confirmed">{{ count($confirmed) }}</span>
            </div>
            
            @if(count($confirmed) > 0)
                @foreach($confirmed as $b)
                    <div class="booking-card">
                        <div class="booking-time">{{ \Carbon\Carbon::parse($b->starts_at)->timezone($user->timezone ?? 'Europe/Madrid')->format('H:i') }}</div>
                        <div class="booking-info">
                            <p class="booking-name">{{ $b->customer_name }}</p>
                            <p class="booking-phone">{{ $b->customer_phone ?: 'Sin teléfono' }} • {{ $b->service->name ?? 'Servicio' }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">No tienes citas confirmadas para mañana.</div>
            @endif

            <!-- Pending Section -->
            <div class="section-title">
                Pendientes de Confirmación <span class="badge badge-pending">{{ count($pending) }}</span>
            </div>
            
            @if(count($pending) > 0)
                <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">
                    Te recomendamos contactar con estos clientes para asegurar su asistencia:
                </p>
                @foreach($pending as $b)
                    <div class="booking-card" style="border-left: 4px solid #eab308;">
                        <div class="booking-time">{{ \Carbon\Carbon::parse($b->starts_at)->timezone($user->timezone ?? 'Europe/Madrid')->format('H:i') }}</div>
                        <div class="booking-info">
                            <p class="booking-name">{{ $b->customer_name }}</p>
                            <p class="booking-phone">{{ $b->customer_phone ?: 'Sin teléfono' }} • {{ $b->service->name ?? 'Servicio' }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">No tienes citas pendientes para mañana.</div>
            @endif
            
            <p style="margin-top: 32px;">¡Mucha suerte con tu jornada de mañana!</p>
        </div>
        
        <div class="footer">
            <p>Este es un resumen automático enviado por <a href="{{ config('app.url') }}">Citalify</a></p>
        </div>
    </div>
</body>
</html>
