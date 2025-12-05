<x-guest-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-4 fw-bold">MaFe Provas Online</h1>
                <p class="lead text-muted mb-0">Plataforma Web + App Android para Avaliações</p>
            </div>
            <img src="{{ asset('images/Logo_mafe_provasonline.png') }}" alt="Logo Online Tests" style="width: 180px; height: auto;" class="d-none d-md-block rounded">
        </div>

        <hr class="mb-5">

        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Uma Solução Completa, Pensada para a Educação</h2>
            <p class="lead text-muted col-lg-8 mx-auto">Do planejamento na plataforma web à aplicação segura no App Android, descubra as ferramentas que fazem do MaFe Provas Online a solução definitiva para a gestão de avaliações da sua escola.</p>
        </div>

        {{-- Seção App Android --}}
        <div class="row align-items-center mb-5 p-4 rounded-3 shadow-sm" style="background: linear-gradient(45deg, #198754, #146c43); color: white;">
            <div class="col-md-4 text-center d-none d-md-block">
                <i class="bi bi-tablet-landscape-fill display-1"></i>
            </div>
            <div class="col-md-8">
                <h3><i class="bi bi-google-play me-2"></i>Aplicativo Android para Provas na Escola</h3>
                <p>Leve a segurança do Mafe Provas Online para o ambiente presencial com nosso aplicativo exclusivo para tablets e celulares Android.</p>
                <ul>
                    <li><strong>Ambiente Controlado e Seguro:</strong> O aplicativo opera em modo de tela cheia, bloqueando notificações e o acesso a outros apps para evitar fraudes.</li>
                    <li><strong>Aplicação Offline:</strong> As provas podem ser baixadas previamente, permitindo que os alunos respondam mesmo com internet instável.</li>
                    <li><strong>Ideal para Dispositivos da Escola:</strong> Transforme os tablets da sua instituição em estações de avaliação seguras.</li>
                    <li><strong>Integração Total:</strong> Avaliações criadas na web ficam automaticamente disponíveis no app.</li>
                </ul>
            </div>
        </div>
        
        {{-- Seção Coordenador --}}
        <div class="row align-items-center mb-5 bg-white p-4 rounded-3 shadow-sm">
            <div class="col-md-8">
                <h3><i class="bi bi-building-gear me-2 text-primary"></i>Painel do Coordenador (Web e App)</h3>
                <p class="text-muted">Visão estratégica e controle total sobre o ecossistema de avaliações da sua instituição.</p>
                <ul>
                    <li><strong>Gestão Acadêmica Centralizada:</strong> Crie e gerencie anos letivos, séries e disciplinas.</li>
                    <li><strong>Controle Total de Usuários:</strong> Cadastre, edite e gerencie o ciclo de vida de alunos e professores.</li>
                    <li><strong>Criação de Modelos de Avaliação:</strong> Padronize a qualidade das avaliações com regras claras.</li>
                    <li><strong>Relatórios de Inteligência Pedagógica:</strong> Gere relatórios em PDF para análises profundas.</li>
                    <li><strong><i class="bi bi-shield-check me-1"></i>Auditoria Completa e Segurança:</strong> Monitore todas as ações críticas no sistema.</li>
                </ul>
            </div>
            <div class="col-md-4 text-center d-none d-md-block">
                <i class="bi bi-clipboard2-data-fill display-1 text-primary opacity-75"></i>
            </div>
        </div>

        {{-- Seção Professor --}}
        <div class="row align-items-center mb-5 p-4">
            <div class="col-md-4 text-center order-md-2 d-none d-md-block">
                <i class="bi bi-pencil-ruler display-1 text-success opacity-75"></i>
            </div>
            <div class="col-md-8 order-md-1">
                <h3><i class="bi bi-person-workspace me-2 text-success"></i>Painel do Professor (Web e App)</h3>
                <p class="text-muted">Ferramentas poderosas para otimizar o tempo e focar no que realmente importa: o ensino.</p>
                <ul>
                    <li><strong>Banco de Questões Estratégico:</strong> Crie um acervo ilimitado, classificando por disciplina, assunto e nível.</li>
                    <li><strong>Criação de Avaliações Flexíveis:</strong> Elabore provas dinâmicas ou recuperações específicas.</li>
                    <li><strong>Acessibilidade Integrada:</strong> Adicione descrições em imagens para alunos com deficiência visual.</li>
                    <li><strong>Correção Automatizada e Feedback:</strong> Economize tempo com a correção automática e forneça feedbacks detalhados.</li>
                </ul>
            </div>
        </div>
        
        {{-- Seção Aluno --}}
        <div class="row align-items-center mb-5 bg-white p-4 rounded-3 shadow-sm">
            <div class="col-md-8">
                <h3><i class="bi bi-backpack2-fill me-2 text-info"></i>Painel do Aluno (Web e App)</h3>
                <p class="text-muted">Uma experiência de avaliação justa, focada e projetada para o sucesso do estudante.</p>
                <ul>
                    <li><strong>Ambiente de Prova Seguro e Focado:</strong> Realize avaliações em um design limpo e sem distrações.</li>
                    <li><strong>Avaliações Justas e Individuais:</strong> Responda a provas geradas dinamicamente, garantindo um teste único.</li>
                    <li><strong>Acessibilidade por Padrão:</strong> Utilize leitores de tela para navegar e responder às questões.</li>
                    <li><strong>Acompanhamento de Desempenho:</strong> Visualize gráficos intuitivos com sua evolução de notas.</li>
                </ul>
            </div>
            <div class="col-md-4 text-center d-none d-md-block">
                <i class="bi bi-graph-up-arrow display-1 text-info opacity-75"></i>
            </div>
        </div>
    </div>
</x-guest-layout>