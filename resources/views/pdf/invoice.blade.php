<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo de Citalify</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #059669;
            margin: 0;
            font-size: 28px;
        }
        .company-info {
            text-align: left;
            margin-bottom: 40px;
            font-size: 14px;
            color: #666;
        }
        .customer-info {
            margin-bottom: 40px;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 8px;
        }
        .customer-info h3 {
            margin-top: 0;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            font-size: 18px;
            border-bottom: 2px solid #333;
            border-top: 2px solid #333;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Citalify</h1>
        <p>Recibo de pago</p>
    </div>

    <div class="company-info">
        <strong>Citalify</strong><br>
        Recibo generado el: {{ $date }}<br>
    </div>

    <div class="customer-info">
        <h3>Cliente</h3>
        <strong>Nombre:</strong> {{ $user->name }}<br>
        <strong>Email:</strong> {{ $user->email }}<br>
    </div>

    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Suscripción Citalify 
                    {{ $is_trial ? '(Prueba gratuita)' : 'Plan Básico' }}
                </td>
                <td class="text-right">{{ number_format($amount, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td class="text-right">Total Pagado</td>
                <td class="text-right">{{ number_format($amount, 2, ',', '.') }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Gracias por confiar en Citalify.<br>
        Este es un comprobante de pago generado automáticamente.
    </div>

</body>
</html>
