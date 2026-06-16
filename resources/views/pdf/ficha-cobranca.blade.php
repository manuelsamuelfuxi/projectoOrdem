<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota de Cobrança - {{ $pedido->process_number }}</title>
    <style>
        /* Remove todas as margens do corpo */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Container principal ocupando toda a largura */
        .ficha-cobranca {
            width: 100%;
            background: white;
            margin: 0;
            padding: 0;
        }

        /* Cabeçalho do Documento */
        .doc-header {
            background-color: #0c2d6b;
            color: white;
            padding: 20px 25px;
            overflow: hidden;
            border-bottom: 4px solid #d4af37;
        }

        .doc-logo-area {
            float: left;
            width: 60%;
        }

        .doc-emblema {
            float: left;
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }

        .doc-emblema img {
            width: 100%;
            height: auto;
        }

        .doc-instituicao {
            float: left;
            width: calc(100% - 75px);
        }

        .doc-instituicao h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            margin-left: 80px;
            font-weight: bold;
        }

        .doc-instituicao p {
            margin-left: 80px;
            font-size: 10px;
        }

        .doc-titulo-area {
            float: right;
            width: 35%;
            text-align: right;
        }

        .doc-titulo-area h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #d4af37;
        }

        .doc-ref {
            background: rgba(0, 0, 0, 0.2);
            padding: 5px 10px;
            font-size: 10px;
        }

        .doc-ref span {
            margin: 0 5px;
        }

        .clearfix {
            clear: both;
        }

        /* Corpo do Documento - sem margens laterais */
        .doc-corpo {
            padding: 20px 25px;
        }

        .doc-secao {
            margin-bottom: 20px;
        }

        .doc-secao-titulo {
            font-size: 13px;
            font-weight: bold;
            color: #0c2d6b;
            text-transform: uppercase;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #eff6ff;
        }

        /* Tabela compatível com DomPDF */
        .doc-tabela {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-tabela td {
            padding: 6px 10px 8px 0;
            vertical-align: top;
            width: 50%;
        }

        .doc-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }

        .doc-valor {
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            display: block;
        }

        .doc-valor.iban {
            font-family: monospace;
            font-size: 12px;
            letter-spacing: 1px;
        }

        /* Caixas de Destaque */
        .destaque-financeiro {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px 20px;
            margin: 0 25px 20px 25px;
        }

        .destaque-bancario {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            padding: 15px 20px;
            margin: 0 25px 20px 25px;
        }

        .destaque-bancario .doc-secao-titulo {
            border-bottom-color: #fde68a;
            color: #92400e;
        }

        .text-right {
            text-align: right;
        }

        .doc-valor-grande {
            font-size: 24px;
            font-weight: bold;
            color: #0c2d6b;
        }

        /* Status Badge */
        .doc-status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            font-size: 11px;
            font-weight: bold;
        }

        /* Notas / Observações */
        .doc-notas {
            margin: 0;
            padding-left: 30px;
        }

        .doc-notas li {
            margin-bottom: 6px;
            font-size: 11px;
            line-height: 1.4;
            color: #64748b;
        }

        /* Rodapé do Documento */
        .doc-rodape {
            background-color: #f1f5f9;
            padding: 10px 25px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .doc-rodape p {
            margin: 3px 0;
            font-size: 9px;
            color: #64748b;
        }
        
        /* Garante que o conteúdo ocupe a página inteira */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="ficha-cobranca">
        
        <!-- Cabeçalho do Documento -->
        <div class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-emblema">
    <img src="{{ $logoBase64 }}" alt="Logotipo ORDEPDITA">
</div>
                <div class="doc-instituicao">
                    <h1>ORDEPDITA</h1>
                    <p>Ordem dos Profissionais de Diagnóstico e Terapêutica de Angola</p>
                </div>
            </div>
            <div class="doc-titulo-area">
                <h2>NOTA DE COBRANÇA</h2>
                <div class="doc-ref">
                    <strong>Proc.:</strong> {{ $pedido->process_number }}<br>
                    <strong>Data:</strong> {{ $pedido->submitted_at ? $pedido->submitted_at->format('d/m/Y') : date('d/m/Y') }}
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- Dados Pessoais -->
        <div class="doc-corpo">
            <div class="doc-secao">
                <div class="doc-secao-titulo">
                    DADOS PESSOAIS
                </div>
                <table class="doc-tabela">
                    <tr>
                        <td>
                            <span class="doc-label">Nome Completo</span>
                            <span class="doc-valor">{{ $pedido->full_name ?? '---' }}</span>
                        </td>
                        <td>
                            <span class="doc-label">Nº do Bilhete</span>
                            <span class="doc-valor">{{ $pedido->bi_number ?? '---' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="doc-label">Email</span>
                            <span class="doc-valor">{{ $pedido->email ?? '---' }}</span>
                        </td>
                        <td>
                            <span class="doc-label">Telefone</span>
                            <span class="doc-valor">{{ $pedido->phone ?? '---' }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Dados da Candidatura -->
            <div class="doc-secao">
                <div class="doc-secao-titulo">
                    DADOS DA CANDIDATURA
                </div>
                <table class="doc-tabela">
                    <tr>
                        <td>
                            <span class="doc-label">Profissão</span>
                            <span class="doc-valor">{{ $pedido->profession ?? $pedido->specialty ?? '---' }}</span>
                        </td>
                        <td>
                            <span class="doc-label">Habilitações</span>
                            <span class="doc-valor">{{ $pedido->qualification ?? '---' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="doc-label">Curso</span>
                            <span class="doc-valor">{{ $pedido->course ?? '---' }}</span>
                        </td>
                        <td>
                            <span class="doc-label">Província</span>
                            <span class="doc-valor">{{ $pedido->province ?? '---' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Valor e Estado -->
        <div class="destaque-financeiro">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <span class="doc-label">Estado do Processo</span>
                        <span class="doc-status-badge">
                            @php
                                $statusLabels = [
                                    "nao_pago" => "Aguardando Pagamento",
                                    "aguarda_comprovativo" => "Comprovativo Pendente",
                                    "pagamento_confirmado" => "Pagamento Confirmado",
                                    "em_analise" => "Em Análise",
                                    "aprovado" => "Aprovado",
                                    "documento_emitido" => "Documento Emitido",
                                    "rejeitado" => "Rejeitado",
                                    "correcao_solicitada" => "Correcção Solicitada",
                                ];
                                $statusString = $pedido->status instanceof \UnitEnum ? $pedido->status->value : (string) $pedido->status;
                                echo $statusLabels[$statusString] ?? '---';
                            @endphp
                        </span>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <span class="doc-label">Total a Pagar</span>
                        <div class="doc-valor-grande">{{ number_format($pedido->amount ?? 25000, 2, ',', '.') }} Kz</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Instruções / Observações -->
        <div class="doc-corpo">
            <div class="doc-secao">
                <div class="doc-secao-titulo">
                    OBSERVAÇÕES
                </div>
                <div class="doc-notas">
                    <ol>
                        <li>A emissão do certificado só será realizada após a confirmação do pagamento.</li>
                        <li>O pagamento deve ser feito obrigatoriamente através da conta bancária indicada abaixo.</li>
                        <li>Após o pagamento, envie o comprovativo através do sistema para validação.</li>
                        <li>Guarde esta nota de cobrança como comprovativo da sua candidatura.</li>
                        <li>Para qualquer esclarecimento, contacte a secretaria da ORDEPDITA.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Dados Bancários -->
        <div class="destaque-bancario">
            <div class="doc-secao-titulo">
                DADOS PARA PAGAMENTO (TRANSFERÊNCIA BANCÁRIA)
            </div>
            <table class="doc-tabela">
                <tr>
                    <td>
                        <span class="doc-label">Banco</span>
                        <span class="doc-valor">Banco de Fomento de Angola (BFA)</span>
                    </td>
                    <td>
                        <span class="doc-label">IBAN</span>
                        <span class="doc-valor iban">AO06 0044 0000 0123 4567 8901</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="doc-label">Beneficiário</span>
                        <span class="doc-valor">ORDEPDITA</span>
                    </td>
                    <td>
                        <span class="doc-label">NIF</span>
                        <span class="doc-valor">5417256890</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Rodapé do Documento -->
        <div class="doc-rodape">
            <p>Documento gerado automaticamente pelo sistema da ORDEPDITA.</p>
            <p>Luanda - Angola | www.ordepdita.ao</p>
        </div>
    </div>
</body>
</html>