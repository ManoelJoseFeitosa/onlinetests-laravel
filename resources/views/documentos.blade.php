<x-guest-layout>
    <section class="py-5">
        <div class="container px-5 my-5">
            <div class="text-center mb-5">
                <h1 class="fw-bolder">Central de Documentos</h1>
                <p class="lead fw-normal text-muted mb-0">Encontre aqui todos os manuais, guias e documentos oficiais da plataforma.</p>
            </div>
            <div class="row gx-5 justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    
                    @if (count($documentos) > 0)
                        <div class="list-group shadow-sm">
                            @foreach ($documentos as $doc)
                                {{-- ## CORREÇÃO APLICADA AQUI ## --}}
                                <a href="{{ Storage::url($doc->caminho_arquivo) }}" ... download>
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-primary"><i class="bi bi-file-earmark-arrow-down-fill me-2"></i>{{ $doc->titulo }}</h5>
                                        <small class="text-muted">{{ $doc->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    @if ($doc->descricao)
                                        <p class="mb-1 mt-2">{{ $doc->descricao }}</p>
                                    @endif
                                    <small class="text-success fw-bold">Clique para baixar o arquivo.</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center" role="alert">
                            Nenhum documento disponível no momento.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</x-guest-layout>