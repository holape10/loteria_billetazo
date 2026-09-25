<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #111; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #b8860b; margin: 0; }
        .info p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0d98a; }
        .total { text-align: right; font-size: 16px; font-weight: bold; margin-top: 10px; }
        .qr { text-align: center; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💰 El Billetazo</h1>
        <p>Comprobante de compra #{{ $compra->id }}</p>
    </div>

    <div class="info">
        <p><strong>Cliente:</strong> {{ $compra->cliente->nombre }} — DNI: {{ $compra->cliente->dni }}</p>
        <p><strong>Sorteo:</strong> {{ $compra->sorteo->fecha->format('d/m/Y') }} — {{ $compra->sorteo->hora }}</p>
        <p><strong>Fecha de compra:</strong> {{ $compra->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Método de pago:</strong> {{ $compra->metodo_pago ? ucfirst($compra->metodo_pago) : '—' }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($compra->estado_pago) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Jugada</th>
                <th>Números</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($compra->boletos as $i => $boleto)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ collect([$boleto->numero_1,$boleto->numero_2,$boleto->numero_3,$boleto->numero_4,$boleto->numero_5,$boleto->numero_6])->join(' - ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total pagado: S/ {{ number_format($compra->monto_total, 2) }}</p>

    <div class="qr">
        <img src="data:image/svg+xml;base64,{{ $qr }}" width="140" height="140">
        <p>Escanea para ver este comprobante en línea</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} El Billetazo — loteriabilletazo@gmail.com
    </div>
</body>
</html>