<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Cadastrar Nova Escola e Coordenador <i class="bi bi-building-fill-add"></i></h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>
        <hr>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('superadmin.escolas.store') }}">
                    @csrf
                    <fieldset class="mb-4">
                        <legend class="fs-5 border-bottom pb-2 mb-3">Dados da Escola</legend>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome_escola" class="form-label">Nome da Nova Escola</label>
                                <input type="text" class="form-control @error('nome_escola') is-invalid @enderror" id="nome_escola" name="nome_escola" value="{{ old('nome_escola') }}" required>
                                @error('nome_escola')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cnpj_escola" class="form-label">CNPJ (Opcional)</label>
                                <input type="text" class="form-control @error('cnpj_escola') is-invalid @enderror" id="cnpj_escola" name="cnpj_escola" value="{{ old('cnpj_escola') }}" placeholder="XX.XXX.XXX/XXXX-XX">
                                @error('cnpj_escola')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="plano_escola" class="form-label">Plano Inicial</label>
                                <select class="form-select @error('plano_escola') is-invalid @enderror" id="plano_escola" name="plano_escola" required>
                                    <option value="" disabled selected>Selecione um plano</option>
                                    @foreach ($plans as $plan_key => $plan_details)
                                        <option value="{{ $plan_key }}" {{ old('plano_escola') == $plan_key ? 'selected' : '' }}>
                                            {{ $plan_details['display_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plano_escola')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Selecione o plano de assinatura inicial para esta escola.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="media_recuperacao" class="form-label">Média para Recuperação</label>
                                <input type="text" class="form-control @error('media_recuperacao') is-invalid @enderror" id="media_recuperacao" name="media_recuperacao" value="{{ old('media_recuperacao', '6.0') }}" required>
                                @error('media_recuperacao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Nota mínima para aprovação (ex: 6.0).</div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="fs-5 border-bottom pb-2 mb-3">Dados do Coordenador Principal</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome_coordenador" class="form-label">Nome do Coordenador</label>
                                <input type="text" class="form-control @error('nome_coordenador') is-invalid @enderror" id="nome_coordenador" name="nome_coordenador" value="{{ old('nome_coordenador') }}" required>
                                @error('nome_coordenador')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_coordenador" class="form-label">E-mail do Coordenador</label>
                                <input type="email" class="form-control @error('email_coordenador') is-invalid @enderror" id="email_coordenador" name="email_coordenador" value="{{ old('email_coordenador') }}" required>
                                @error('email_coordenador')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="senha_provisoria" class="form-label">Senha Provisória</label>
                            <input type="password" class="form-control @error('senha_provisoria') is-invalid @enderror" id="senha_provisoria" name="senha_provisoria" required>
                            @error('senha_provisoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">O coordenador será solicitado a trocar esta senha no primeiro login.</div>
                        </div>
                    </fieldset>

                    <div class="text-center mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle-fill me-2"></i>Salvar Escola e Coordenador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>