<!DOCTYPE html>
<html>
<head>
    <title>Novo Contato - MaFe Provas Online</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="background-color: #f8f9fa; padding: 20px;">
        <h2 style="color: #0d6efd;">Novo Contato Recebido</h2>
        <p>Você recebeu uma nova mensagem através do site <strong>MaFe Provas Online</strong>.</p>
        
        <div style="background: #fff; padding: 20px; border-radius: 5px; border: 1px solid #ddd;">
            <p><strong>Nome:</strong> {{ $data['name'] }}</p>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            <p><strong>Instituição:</strong> {{ $data['institution'] ?? 'Não informada' }}</p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
            <p><strong>Mensagem:</strong></p>
            <p style="white-space: pre-line;">{{ $data['message'] }}</p>
        </div>
        
        <p style="font-size: 12px; color: #777; margin-top: 20px;">
            Este e-mail foi enviado automaticamente pelo formulário de contato.
        </p>
    </div>
</body>
</html>
