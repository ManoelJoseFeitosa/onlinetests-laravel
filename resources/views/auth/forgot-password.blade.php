<x-guest-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-3">Redefinir Senha</h4>
                        <p class="text-muted text-center mb-4">
                            Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail e enviaremos um link para você escolher uma nova.
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus />
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Enviar Link de Redefinição
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>