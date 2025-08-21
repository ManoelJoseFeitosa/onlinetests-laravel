<x-guest-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Primeiro Acesso</h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('info'))
                            <div class="alert alert-info">{{ session('info') }}</div>
                        @endif

                        <form method="POST" action="{{ route('primeiro-acesso.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha</label>
                                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirme a Nova Senha</label>
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <hr class="my-4">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="aceite_termos" name="aceite_termos" required>
                                <label class="form-check-label" for="aceite_termos">
                                    Eu li e concordo com os 
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTermosDeUso" class="text-primary fw-bold">
                                        Termos de Uso e Política de Privacidade
                                    </a>.
                                </label>
                                @error('aceite_termos') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Atualizar Senha e Acessar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- O HTML do modal com o conteúdo COMPLETO da política de privacidade --}}
    <div class="modal fade" id="modalTermosDeUso" tabindex="-1" aria-labelledby="modalTermosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTermosLabel">Termos de Uso e Política de Privacidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Última atualização: 18 de agosto de 2025</p>
                    <hr>

                    <p class="lead">Bem-vindo(a) à plataforma OnlineTests!</p>
                    <p>Este documento estabelece os Termos de Uso e a Política de Privacidade da plataforma OnlineTests ("Plataforma"), desenvolvida e mantida por MANOEL JOSE FEITOSA NETO, pessoa jurídica de direito privado, inscrita no CNPJ sob o nº 61.777.358/0001-10, doravante denominada "Controladora".</p>
                    <p>A leitura atenta e a aceitação destes termos são indispensáveis para a utilização dos nossos serviços.</p>

                    <h5 class="mt-4">1. DEFINIÇÕES IMPORTANTES (LGPD)</h5>
                    <p><strong>Dado Pessoal:</strong> Qualquer informação relacionada a uma pessoa natural identificada ou identificável.</p>
                    <p><strong>Titular:</strong> Pessoa natural a quem se referem os dados pessoais que são objeto de tratamento.</p>
                    <p><strong>Controlador:</strong> Pessoa natural ou jurídica a quem competem as decisões referentes ao tratamento de dados pessoais. Neste caso, a MANOEL JOSE FEITOSA NETO.</p>
                    <p><strong>Operador:</strong> Pessoa natural ou jurídica que realiza o tratamento de dados pessoais em nome do Controlador. Neste caso, a Instituição de Ensino contratante.</p>
                    <p><strong>Tratamento:</strong> Toda operação realizada com dados pessoais, como coleta, produção, recepção, classificação, utilização, acesso, reprodução, transmissão, distribuição, processamento, arquivamento, armazenamento, eliminação, avaliação ou controle da informação, modificação, comunicação, transferência, difusão ou extração.</p>
                    <p><strong>LGPD:</strong> Lei Geral de Proteção de Dados (Lei nº 13.709/2018).</p>

                    <h5 class="mt-4">2. ACEITAÇÃO DOS TERMOS</h5>
                    <p>Ao acessar e utilizar a Plataforma OnlineTests, seja como Coordenador, Professor ou Aluno, você declara ter lido, compreendido e concordado integralmente com as disposições destes Termos de Uso e Política de Privacidade. Caso não concorde com qualquer um dos termos, você não deverá utilizar a Plataforma.</p>

                    <h5 class="mt-4">3. DADOS PESSOAIS COLETADOS E FINALIDADE</h5>
                    <p>A Plataforma coleta e trata os seguintes dados pessoais, com as respectivas finalidades:</p>
                    <h6>Dados de Cadastro (Coordenadores, Professores e Alunos):</h6>
                    <ul>
                        <li><strong>Dados Coletados:</strong> Nome completo, e-mail, senha (criptografada).</li>
                        <li><strong>Finalidade:</strong> Identificar o usuário, permitir o acesso seguro à Plataforma, gerir as permissões de cada perfil (criar provas, responder avaliações, visualizar resultados) e garantir a comunicação entre a Plataforma e o usuário.</li>
                    </ul>
                    <h6>Dados de Desempenho Acadêmico (Alunos):</h6>
                    <ul>
                        <li><strong>Dados Coletados:</strong> Respostas fornecidas em avaliações, notas, data e hora de realização, status (pendente, finalizado).</li>
                        <li><strong>Finalidade:</strong> Processar e calcular os resultados das avaliações, gerar relatórios de desempenho individuais e por turma, e fornecer aos Professores e Coordenadores as ferramentas necessárias para a análise pedagógica.</li>
                    </ul>
                    <h6>Dados de Navegação (Todos os Usuários):</h6>
                    <ul>
                        <li><strong>Dados Coletados:</strong> Endereço de IP, tipo de navegador, data e hora dos acessos.</li>
                        <li><strong>Finalidade:</strong> Garantir a segurança da Plataforma, auditar acessos (conforme descrito na Seção 4), prevenir fraudes e otimizar a experiência do usuário.</li>
                    </ul>
                    <p>A base legal para o tratamento destes dados é a execução de contrato (Art. 7º, V, da LGPD), uma vez que o tratamento é indispensável para a prestação dos serviços educacionais contratados pela Instituição de Ensino.</p>

                    <h5 class="mt-4">4. SEGURANÇA E AUDITORIA</h5>
                    <p>A Plataforma conta com mecanismos de segurança para proteger os dados e a integridade do processo avaliativo, incluindo:</p>
                    <ul>
                        <li><strong>Acesso Seguro:</strong> Acesso mediante e-mail e senha, com a senha armazenada de forma criptografada.</li>
                        <li><strong>Prevenção de Fraudes:</strong> O sistema monitoriza tentativas de saída do modo de tela cheia durante as avaliações, bloqueando a prova para garantir a lisura do processo.</li>
                        <li><strong>Auditoria de Acessos:</strong> Todos os acessos e ações relevantes (criação de questões, realização de provas, correções) são registados com data, hora e endereço de IP, estando disponíveis para a Coordenação para fins de auditoria e segurança.</li>
                    </ul>

                    <h5 class="mt-4">5. COMPARTILHAMENTO DE DADOS</h5>
                    <p>A Controladora não compartilha os dados pessoais dos usuários com terceiros para fins de marketing ou publicidade. O compartilhamento de dados ocorre estritamente nos seguintes cenários:</p>
                    <ul>
                        <li><strong>Com a Instituição de Ensino (Operadora):</strong> Os dados de cadastro e desempenho dos alunos e professores são inerentemente visíveis e gerenciados pela própria instituição de ensino contratante, que atua como Operadora dos dados no contexto educacional.</li>
                        <li><strong>Por Obrigação Legal:</strong> Os dados poderão ser compartilhados com autoridades judiciais, administrativas ou governamentais competentes, sempre que houver requerimento, requisição ou ordem judicial.</li>
                    </ul>

                    <h5 class="mt-4">6. DIREITOS DOS TITULARES DE DADOS</h5>
                    <p>Conforme a LGPD, os titulares de dados (você) têm o direito de solicitar, a qualquer momento, acesso, correção, anonimização, bloqueio ou eliminação de dados desnecessários ou excessivos, entre outros direitos previstos em lei. Para exercer os seus direitos, o titular deverá entrar em contato através dos canais disponibilizados na Seção 9.</p>

                    <h5 class="mt-4">7. SEGURANÇA E ARMAZENAMENTO DOS DADOS</h5>
                    <p>A Controladora adota medidas de segurança técnicas e administrativas aptas a proteger os dados pessoais de acessos não autorizados e de situações acidentais ou ilícitas de destruição, perda, alteração, comunicação ou difusão.</p>

                    <h5 class="mt-4">8. PROPRIEDADE INTELECTUAL</h5>
                    <p>Todo o conteúdo da Plataforma OnlineTests, incluindo software, textos, gráficos e logotipos, é de propriedade exclusiva da MANOEL JOSE FEITOSA NETO ou de seus licenciadores e é protegido pelas leis de propriedade intelectual.</p>

                    <h5 class="mt-4">9. ALTERAÇÕES NESTES TERMOS</h5>
                    <p>A Controladora reserva-se o direito de alterar estes Termos a qualquer momento. A versão atualizada estará sempre disponível na Plataforma.</p>

                    <h5 class="mt-4">10. CONTATO</h5>
                    <p>Para esclarecer quaisquer dúvidas sobre estes Termos, entre em contato com o nosso Encarregado pela Proteção de Dados (DPO) através do e-mail: <a href="mailto:contato@onlinetests.com.br">contato@onlinetests.com.br</a>.</p>

                    <h5 class="mt-4">11. FORO</h5>
                    <p>Para a resolução de quaisquer controvérsias decorrentes destes Termos, será competente o foro da comarca de Teresina, Estado do Piauí, Brasil.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>