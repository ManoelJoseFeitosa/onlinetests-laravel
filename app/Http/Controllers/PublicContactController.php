<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class PublicContactController extends Controller
{
    public function submit(Request $request)
    {
        // 1. Validar os dados recebidos do formulário
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'institution' => 'nullable|string|max:255', // Campo opcional
            'message' => 'required|string',
        ]);

        // 2. Tentar enviar o E-mail
        try {
            // Envia para o email configurado no método to() usando a classe Mailable criada
            Mail::to('contato@mafesistemas.com.br')->send(new ContactMail($validated));
        } catch (\Exception $e) {
            // Em caso de erro (ex: falha na conexão SMTP), retorna com mensagem de erro
            // O ideal é logar o erro: \Log::error($e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao enviar. Tente novamente mais tarde.');
        }

        // 3. Retornar com mensagem de sucesso se tudo der certo
        return back()->with('success', 'Sua mensagem foi enviada com sucesso! Entraremos em contato em breve.');
    }
}
