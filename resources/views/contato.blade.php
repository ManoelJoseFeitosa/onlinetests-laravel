<x-guest-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold">Fale Conosco</h1>
                <p class="lead text-muted mb-0">Estamos prontos para tirar suas dúvidas ou agendar uma demonstração completa.</p>
            </div>
            {{-- Garanta que o caminho da logo existe --}}
            <img src="{{ asset('images/Logo_mafe_provasonline.png') }}" alt="Logo Online Tests" style="width: 150px; height: auto;" class="d-none d-md-block rounded">
        </div>
        <hr class="mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Exibe mensagens de Sucesso --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Exibe mensagens de Erro do Sistema (Email falhou) --}}
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Exibe Erros de Validação (Campos obrigatórios, etc) --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contato.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Seu Nome</label>
                        {{-- name corrigido para 'name' e adicionado value="{{ old() }}" --}}
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Seu E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="institution" class="form-label">Nome da Instituição (Opcional)</label>
                        {{-- name corrigido para 'institution' --}}
                        <input type="text" class="form-control" id="institution" name="institution" value="{{ old('institution') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Mensagem</label>
                        {{-- name corrigido para 'message' --}}
                        <textarea class="form-control" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
