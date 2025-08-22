<x-app-layout>
    <x-slot name="title">Gerenciar Documentos</x-slot>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gerenciar Documentos</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>

        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white"><h5 class="mb-0">Upload de Novo Documento</h5></div>
            <div class="card-body">
                <form action="{{ route('superadmin.documentos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3"><label for="titulo" class="form-label">Título</label><input type="text" class="form-control" name="titulo" required></div>
                    <div class="mb-3"><label for="descricao" class="form-label">Descrição</label><textarea class="form-control" name="descricao" rows="2"></textarea></div>
                    <div class="mb-3"><label for="arquivo" class="form-label">Arquivo (PDF, DOC, DOCX)</label><input type="file" class="form-control" name="arquivo" required></div>
                    <button type="submit" class="btn btn-primary w-100">Enviar Documento</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0">Documentos Existentes</h5></div>
            <div class="card-body">
                <ul class="list-group">
                    @forelse ($documentos as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $doc->titulo }}</h6>
                                <p class="small text-muted mb-1">{{ $doc->descricao }}</p>
                                <small>{{ $doc->created_at->format('d/m/Y') }}</small>
                            </div>
                            <div>
                                <a href="{{ Storage::url($doc->caminho_arquivo) }}" class="btn btn-sm btn-outline-info me-2" target="_blank">Ver</a>
                                <form action="{{ route('superadmin.documentos.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">Nenhum documento cadastrado.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>