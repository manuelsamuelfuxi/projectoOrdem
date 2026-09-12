<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 10mm; }
        body {
            font-family: 'DejaVu Serif', serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
        }

        .pagina {
            width: 60%;
            margin: 0 auto;
        }

        .cartao {
            width: 100%;
            height: 70mm;
            position: relative;
            border: 0.5pt solid black;
            box-sizing: border-box;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .cartao + .cartao { margin-top: 6mm; }

        .forma-azul {
            position: absolute;
            top: 0;
            left: 0;
            width: 32mm;
            height: 100%;
            background: rgb(12, 192, 223);
            z-index: 0;
        }

        .forma-azul::after {
            content: '';
            position: absolute;
            top: -2mm;
            left: -0.5mm;
            width: 100mm;
            height: 90mm;
            background: #ffffff;
            border-radius: 50%;
        }

        .conteudo {
            position: relative;
            z-index: 1;
            height: 100%;
            box-sizing: border-box;
            padding: 3mm 5mm 3mm 30mm;
        }

        /* ── Cabeçalho ────────────────────────────────────────────────── */
        .cabecalho { text-align: center; margin-bottom: 2mm; }
        .cabecalho .sigla {
            font-size: 8pt; font-weight: bold; margin: 0 0 0.5mm 0;
            letter-spacing: 0.3pt; margin-left: -22mm;
        }
        .cabecalho .nome-entidade {
            font-size: 5.5pt; margin: 0; text-transform: uppercase;
            line-height: 1.2; margin-left: -22mm;
        }
        .cabecalho .diploma {
            font-size: 5pt; font-weight: bold; margin: 1mm 0 0 0;
            margin-left: -22mm;
        }

        .selo {
            position: absolute; top: 1mm; left: 1mm;
            width: 25mm; height: 27mm;
            border: none; border-radius: 50%;
            z-index: 3; overflow: hidden;
        }
        .selo img { width: 100%; height: 100%; object-fit: fill; display: block; }

        /* ── Foto do candidato + código de barras por baixo ─────────────── */
        .foto-caixa {
            position: absolute;
            top: 3mm; right: 5mm;
            width: 20mm; height: 23mm;
            border: 0.5pt solid #94a3b8;
            text-align: center;
            font-size: 5pt;
            color: #94a3b8;
            z-index: 2;
            background: #fff;
        }
        .foto-caixa img { width: 100%; height: 100%; object-fit: cover; }
        .foto-caixa span { display: block; padding-top: 7mm; }

        .codigo-barras {
            position: absolute;
            top: 27mm; right: 5mm;
            width: 20mm; height: 6mm;
            z-index: 2;
            background: #fff;
        }
        .codigo-barras img { width: 100%; height: 100%; object-fit: fill; }

        .titulo-documento {
            font-size: 11pt; font-weight: bold; text-align: center;
            margin: 1mm 0 0mm 0; padding-bottom: 1mm; margin-left: -22mm;
        }

        .campos { font-size: 6pt; line-height: 1.55; margin-right: 10mm; }
        .campos .linha { margin-bottom: 0mm; margin-left: -22mm; font-size: 1.4em; }
        .campos .rotulo { font-weight: bold; display: inline; }
        .campos .valor { display: inline; }

        .info-foto {
            position: absolute; top: 19mm; right: 2mm;
            width: 26mm; text-align: center;
            font-size: 6pt; font-weight: bold;
            z-index: 2; white-space: nowrap;
        }

        .qr-caixa {
            position: absolute; bottom: 3mm; right: 5mm;
            width: 18mm; height: 18mm;
            text-align: center; font-size: 4pt;
            color: #94a3b8; z-index: 2; background: #fff;
        }
        .qr-caixa img { width: 100%; height: 100%; }

        /* ── Verso — texto legal + faixa de rodapé ──────────────────────── */
        .verso .diploma-artigo {
            text-align: center; font-size: 8pt;
            margin-bottom: 2mm; margin-left: -15mm;
        }
        .verso .diploma-artigo strong { display: block; margin-top: 0.5mm; }
        .verso .texto-legal {
            font-size: 7pt; text-align: justify; line-height: 1.4;
            margin-right: -3mm;
            margin-left: -29mm;
            margin-top: 20mm;
        }
        .verso .contactos {
            font-size: 6pt; font-weight: bold;
            margin-top: 13mm; margin-left: -21mm;
            text-align: center;
        }
        .verso .contactos a { color: #1d4ed8; text-decoration: none; }
        .verso .validade {
            font-size: 6pt; font-weight: bold;
            z-index: 2; margin-left: -30mm; margin-top: 1mm;
        }

        /* Cantoneira geométrica preto/azul no canto superior direito */
        .verso .cantoneira {
            position: absolute; top: 0; right: 0;
            width: 22mm; height: 14mm;
            z-index: 2; overflow: hidden;
        }
        .verso .cantoneira::before,
        .verso .cantoneira::after {
            content: '';
            position: absolute;
            width: 12mm; height: 12mm;
            transform: rotate(45deg);
        }
        .verso .cantoneira::before { top: -6mm; right: 4mm; background: #000; }
        .verso .cantoneira::after  { top: -6mm; right: -4mm; background: rgb(12, 192, 223); }

        /* Faixa azul de rodapé, a toda a largura do cartão */
        .verso .faixa-rodape {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 8mm;
            background: rgb(12, 192, 223);
            color: #fff;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            padding-left: 20mm;
            z-index: 2;
            box-sizing: border-box;
            padding-top: 2mm;
        }
        .verso .faixa-rodape .bandeira {
            position: absolute;
            right: 0; top: 0;
            width: 14mm; height: 100%;
        }
        .verso .faixa-rodape .bandeira img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
    </style>
</head>
<body>
<div class="pagina">

    {{-- ═══════════════ FRENTE ═══════════════ --}}
    <div class="cartao frente">
        <div class="forma-azul"></div>
        <div class="selo"><img src="{{ public_path('images/logotipo.png') }}" alt="Logo AATDSPA"></div>

        <div class="foto-caixa">
            @if($fotoBase64)
                <img src="{{ $fotoBase64 }}" alt="Foto">
            @else
                <span>Foto</span>
            @endif
        </div>
        <div class="codigo-barras">
            <img src="{{ $codigoBarrasDataUri }}" alt="Código de barras">
        </div>

        <div class="info-foto"><br><br>Nº {{ $numeroProcesso }}</div>
        <div class="qr-caixa"><img src="{{ $qrCodeDataUri }}" alt="QR"></div>

        <div class="conteudo">
            <div class="cabecalho">
                <p class="sigla">AATDSPA</p>
                <p class="nome-entidade">
                    Associação de Apoio dos Técnicos de<br>
                    Diagnóstico Terapêutica e S. P de Angola
                </p>
                <p class="diploma">(DR. III-nº 143 de 22 de Agosto/2019).</p>
            </div>

            <div class="titulo-documento">Licença de Estágio</div>

            <div class="campos">
                <div class="linha"><span class="rotulo">Nome:</span> <span class="valor">{{ $nomeCompleto }}</span></div>
                <div class="linha"><span class="rotulo">Curso:</span> <span class="valor">{{ $curso }}</span></div>
                <div class="linha"><span class="rotulo">Escola:</span> <span class="valor">{{ $escola }}</span></div>
                <div class="linha"><span class="rotulo">Nacionalidade:</span> <span class="valor">{{ $nacionalidade }}</span></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ VERSO ═══════════════ --}}
    <div class="cartao verso">
        <div class="cantoneira"></div>

        <div class="conteudo">

            <div class="texto-legal">
                ESTE PASSE PERTENCE À A.A.T.D.S.P.A, DISTRÍBUIDO PARA O USO DE ESTUDANTES EM  <strong>ESTÁGIO</strong>, É INTRASMISSÍVEL E DEVE-SE ACOMPANHAR POR UM OUTRO DOCUMENTO DE IDENTIFICAÇÃO SEMPRE QUE SOLICITADO.
            </div>

            <div class="contactos">
                Site: <a href="http://www.aatdspa.ao">www.aatdspa.ao</a> /
                <a href="mailto:aatdspa@gmail.com">aatdspa@gmail.com</a> / Página: AATDSPA<br>
                Contactos: 948607983 / 941860550 / 930819054
            </div>

            <div class="validade">Validade: {{ $validadeAte }}</div>
        </div>

        <div class="faixa-rodape">
            Profissional de Diagnóstico e Terapêutica
            <div class="bandeira">
                <img src="{{ public_path('images/angola.png') }}" alt="Bandeira de Angola">
            </div>
        </div>
    </div>

</div>
</body>
</html>