<x-guest-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold">Planos que se adaptam à sua realidade</h1>
                <p class="lead text-muted mb-0">Escolha a opção que melhor atende às necessidades da sua instituição.</p>
            </div>
            <img src="{{ asset('images/onlinetests.jpg') }}" alt="Logo Online Tests" style="width: 150px; height: auto;" class="d-none d-md-block rounded">
        </div>
        <hr class="mb-5">
        <div class="row row-cols-1 row-cols-md-3 g-4 text-center">

            @php
                $faixa_alunos = [
                    'essencial' => 'Até 500 alunos',
                    'profissional' => 'De 500 a 1000 alunos',
                    'enterprise' => 'Ilimitado'
                ];
            @endphp

            @foreach ($planos as $key => $plano)
            <div class="col">
                <div class="card h-100 {{ $key == 'profissional' ? 'rounded-3 shadow-lg border-primary' : '' }}">
                    <div class="card-header py-3 {{ $key == 'profissional' ? 'text-white bg-primary border-primary' : '' }}">
                        <h4 class="my-0 fw-normal">{{ $plano['display_name'] }}</h4>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title pricing-card-title">Consulte<small class="text-muted fw-light">/mês</small></h3>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>{{ $faixa_alunos[$key] }}</li>
                            <li>
                                @if($plano['questoes'] === INF)
                                    Criação, gerenciamento e aplicação de provas e simulados com questões, coordenadores, professores e alunos ilimitados
                                @else
                                    Criação, gerenciamento e aplicação de provas e simulados para {{ $plano['questoes'] }} questões, {{ $plano['professor'] }} professores e {{ $plano['coordenador'] }} coordenador(es)
                                @endif
                            </li>
                            <li>{{ $plano['preco'] }}</li>
                            <li>{{ $plano['suporte'] }}</li>
                        </ul>
                        <a href="{{ route('contato') }}" class="w-100 btn btn-lg {{ $key == 'profissional' ? 'btn-primary' : 'btn-outline-primary' }} mt-auto">Fale Conosco</a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</x-guest-layout>