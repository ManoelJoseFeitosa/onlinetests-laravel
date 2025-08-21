<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Editar Escola e Coordenador</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>
        <hr>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('superadmin.escolas.update', $escola) }}">
                    @csrf
                    @method('PUT') {{-- Informa ao Laravel que esta é uma requisição de ATUALIZAÇÃO --}}
                    
                    <fieldset class="mb-4">
                        <legend class="fs-5 border-bottom pb-2 mb-3">Dados da Escola</legend>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome_escola" class="form-label">Nome da Escola</label>
                                <input type="text" class="form-control" id="nome_escola" name="nome_escola" value="{{ old('nome_escola', $escola->nome) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cnpj_escola" class="form-label">CNPJ</label>
                                <input type="text" class="form-control" id="cnpj_escola" name="cnpj_escola" value="{{ old('cnpj_escola', $escola->cnpj) }}">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="mb-4">
                        <legend class="fs-5 border-bottom pb-2 mb-3">Configurações da Assinatura</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="plano_escola" class="form-label">Plano de Assinatura</label>
                                <select class="form-select" id="plano_escola" name="plano_escola" required>
                                    @foreach ($plans as $plan_key => $plan_details)
                                        <option value="{{ $plan_key }}" @if(old('plano_escola', $escola->plano) == $plan_key) selected @endif>
                                            {{ $plan_details['display_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Selecione o nível de acesso e os limites para esta escola.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="media_recuperacao" class="form-label">Média para Recuperação</label>
                                <input type="number" class="form-control" id="media_recuperacao" name="media_recuperacao" 
                                       step="0.1" min="0" max="10" value="{{ old('media_recuperacao', $escola->media_recuperacao) }}" required>
                                <div class="form-text">Nota mínima para aprovação.</div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="fs-5 border-bottom pb-2 mb-3">Dados do Coordenador</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome_coordenador" class="form-label">Nome do Coordenador</label>
                                <input type="text" class="form-control" id="nome_coordenador" name="nome_coordenador" value="{{ old('nome_coordenador', $coordenador->nome) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_coordenador" class="form-label">E-mail do Coordenador</label>
                                <input type="email" class="form-control" id="email_coordenador" name="email_coordenador" value="{{ old('email_coordenador', $coordenador->email) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="senha_coordenador" class="form-label">Redefinir Senha do Coordenador</label>
                            <input type="password" class="form-control" id="senha_coordenador" name="senha_coordenador" placeholder="Deixe em branco para não alterar">
                            <div class="form-text">Digite uma nova senha apenas se desejar redefini-la.</div>
                        </div>
                    </fieldset>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle-fill me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>