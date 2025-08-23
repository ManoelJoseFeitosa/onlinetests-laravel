<x-app-layout>
    <div class="container mt-4">
        <h1>Alunos com Provas Bloqueadas</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($resultadosBloqueados->isEmpty())
            <div class="alert alert-info">Nenhum aluno com prova bloqueada no momento.</div>
        @else
            <ul class="list-group">
                @foreach($resultadosBloqueados as $resultado)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            Aluno: **{{ $resultado->aluno->nome }}**
                            <br>
                            Prova: *{{ $resultado->avaliacao->nome }}*
                        </div>
                        <form action="{{ route('professor.bloqueios.desbloquear', $resultado) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Desbloquear</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>