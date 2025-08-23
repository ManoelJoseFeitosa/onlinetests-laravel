<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Resend; // Importa a classe do Resend

class ContatoController extends Controller
{
    /**
     * Mostra o formulário de contato.
     */
    public function create()
    {
        return view('contato');
    }

    /**
     * Processa o envio do formulário e envia o email.
     */
    public function store(Request $request)
    {
        // 1. Valida os dados do formulário
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email',
            'mensagem' => 'required|string|max:5000',
        ]);

        $apiKey = env('RESEND_API_KEY');
        if (!$apiKey) {
            // Se a chave não estiver configurada, retorna com erro.
            return back()->with('error', 'O serviço de e-mail não está configurado.');
        }

        try {
            $resend = Resend::client($apiKey);

            // 2. Monta e envia o email
            $resend->emails->send([
                // Usa o endereço configurado no .env como remetente
                'from' => config('mail.from.name') . ' <' . config('mail.from.address') . '>',

                // Alterado o destinatário para um email fora da Locaweb (ex: Gmail)
                'to' => ['seu-email-pessoal@gmail.com'], 
                'subject' => 'Nova Mensagem de Contato - ' . $validated['nome'],
                'html' => view('emails.contato', $validated)->render(), // Usaremos uma view para o corpo do email
                'reply_to' => $validated['email'],
            ]);

        } catch (\Exception $e) {
            // Altere temporariamente para mostrar o erro completo para debug
            return back()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }

        // 3. Se tudo deu certo, redireciona de volta com sucesso
        return redirect()->route('contato')->with('success', 'Sua mensagem foi enviada com sucesso! Entraremos em contato em breve.');
    }
}