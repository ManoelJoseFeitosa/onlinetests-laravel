<x-guest-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                
                <div class="text-center mb-4">
                    <a href="/">
                        {{-- Coloque o nome da sua logo aqui se for diferente --}}
                        <img src="{{ asset('images/onlinetests.jpg') }}" alt="Logo OnlineTests" style="width: 180px; height: auto;" class="rounded">
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h4 class="card-title text-center mb-1">Acessar o Sistema</h4>
                        <p class="text-center text-muted mb-4">Use suas credenciais para entrar.</p>

                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <div>Ocorreu um erro:</div>
                                <ul class="mt-2 mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                    <label class="form-check-label" for="remember_me">Lembre de mim</label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="small" href="{{ route('password.request') }}">
                                        Esqueceu sua senha?
                                    </a>
                                @endif
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Fazer Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>