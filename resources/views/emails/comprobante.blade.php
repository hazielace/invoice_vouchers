<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Subidos</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    <h2>Comprobantes Exitosos</h2>
    @if(count($vouchers) > 0)
        @foreach ($vouchers as $comprobante)
        <ul>
            <li>Nombre del Emisor: {{ $comprobante->issuer_name }}</li>
            <li>Tipo de Documento del Emisor: {{ $comprobante->issuer_document_type }}</li>
            <li>Número de Documento del Emisor: {{ $comprobante->issuer_document_number }}</li>
            <li>Nombre del Receptor: {{ $comprobante->receiver_name }}</li>
            <li>Tipo de Documento del Receptor: {{ $comprobante->receiver_document_type }}</li>
            <li>Número de Documento del Receptor: {{ $comprobante->receiver_document_number }}</li>
            <li>Monto Total: {{ $comprobante->total_amount }}</li>
        </ul>
        @endforeach
    @else
        <p>No se encontraron comprobantes exitosos.</p>
    @endif

    <h2>Comprobantes Fallidos</h2>
    @if(count($failedVouchers) > 0)
        @foreach ($failedVouchers as $failed)
        <ul>
            <li>Contenido XML: {{ $failed['xml_content'] }}</li>
            <li>Motivo del Error: {{ $failed['error'] }}</li>
        </ul>
        @endforeach
    @else
        <p>No se encontraron comprobantes fallidos.</p>
    @endif
    <p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
