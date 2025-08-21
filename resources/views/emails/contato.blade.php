<!DOCTYPE html>
<html>
<head>
    <title>Nova Mensagem de Contato</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6;">
    <h2>Nova Mensagem Recebida do Site OnlineTests</h2>
    <p><strong>Nome:</strong> {{ $nome }}</p>
    <p><strong>E-mail para Resposta:</strong> {{ $email }}</p>
    <hr>
    <p><strong>Mensagem:</strong></p>
    <p>{!! nl2br(e($mensagem)) !!}</p>
</body>
</html>