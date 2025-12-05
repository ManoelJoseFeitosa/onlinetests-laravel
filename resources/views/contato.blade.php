<x-guest-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold">Fale Conosco</h1>
                <p class="lead text-muted mb-0">Estamos prontos para tirar suas dúvidas ou agendar uma demonstração completa.</p>
            </div>
            <img src="{{ asset('images/Logo_mafe_provasonline.png') }}" alt="Logo Online Tests" style="width: 150px; height: auto;" class="d-none d-md-block rounded">
        </div>
        <hr class="mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Exibe mensagens de sucesso ou erro --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('contato.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="nome" class="form-label">Seu Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Seu E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="escola" class="form-label">Nome da Instituição (Opcional)</label>
                        <input type="text" class="form-control" id="escola" name="escola">
                    </div>
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Mensagem</label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>