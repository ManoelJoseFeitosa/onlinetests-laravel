<?php

namespace App\Http\Controllers;

use App\Models\Documento; // Certifique-se de que o caminho para o seu model Documento está correto
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentoController extends Controller
{
    /**
     * Mostra a página pública de documentos para todos os usuários.
     */
    public function indexPublico(): View
    {
        $documentos = Documento::orderBy('created_at', 'desc')->get();

        // Supondo que você tenha um arquivo de view em:
        // resources/views/documentos_publicos.blade.php
        // Se o nome for diferente, ajuste aqui.
        return view('documentos', compact('documentos'));
    }
}