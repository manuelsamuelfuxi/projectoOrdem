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
            overflow: hidden; /* recorta a elipse que sai do cartão, deixando só a curva */
            page-break-inside: avoid;
        }
        .cartao + .cartao { margin-top: 6mm; }

        /* ── Faixa azul em meia-lua, a toda a altura do cartão ──────────
           Elipse bem mais alta do que o cartão (140mm vs 70mm), centrada
           verticalmente e deslocada para a esquerda — só a "fatia" direita
           da elipse fica visível dentro do cartão, formando uma curva
           suave que percorre toda a altura, tal como na foto de referência.
           Valores em mm (não %) para seres mais previsível de afinar:
           - aumenta "width" da elipse → curva mais larga/bulbosa
           - aumenta "height" da elipse → curva mais suave/menos pronunciada
           - ajusta "left" (mais negativo = menos curva visível) */
                /* ── Estrutura da barra azul (Base reta à esquerda) ────────── */
        .forma-azul {
            position: absolute;
            top: 0;
            left: 0;
            width: 32mm; /* Largura total que a barra azul terá no topo e na base */
            height: 100%;
            background: rgb(31, 84, 145);
            z-index: 0;
        }

        /* ── O "Cortador" que esculpe a concavidade para dentro (Eixo X) ──
           Este elemento é um círculo branco gigante posicionado à direita da barra, 
           empurrando a cor azul para dentro exatamente no meio do cartão. */
        .forma-azul::after {
            content: '';
            position: absolute;
            top: -2mm;       /* Centraliza o círculo verticalmente */
            left: -0.5mm;       /* Controla a profundidade da curva (maior = curva mais rasa) */
            width: 100mm;     /* Tamanho do círculo cortador */
            height: 90mm;
            background: #ffffff; /* Cor de fundo do cartão para fazer o recorte */
            border-radius: 50%;
        }


        .conteudo {
            position: relative;
            z-index: 1;
            height: 100%;
            box-sizing: border-box;
            padding: 3mm 5mm 3mm 30mm; /* clear da curva — ajusta se mudares a largura acima */
        }

        /* ── Cabeçalho ────────────────────────────────────────────────── */
        .cabecalho {
            text-align: center;
            margin-bottom: 2mm;
        }
        .cabecalho .sigla {
            font-size: 8pt;
            font-weight: bold;
            margin: 0 0 0.5mm 0;
            letter-spacing: 0.3pt;
            margin-left: -22mm;
        }
        .cabecalho .nome-entidade { font-size: 5.5pt;
            margin: 0;
            text-transform: uppercase;
            line-height: 1.2;
            margin-left: -22mm;
        }
        .cabecalho .diploma { font-size: 5pt; font-weight: bold; margin: 1mm 0 0 0;
        margin-left: -22mm;
        }

        /* ── Selo/logo — imagem real, recortada em círculo ──────────────── */
                /* ── Estilização Oval Transparente para o Logotipo ────────────── */
                /* ── Selo com Fundo Branco Recortado em Elipse Perfeita ── */
        .selo {
            position: absolute;
            top: 1mm; 
            left: 1mm;
            width: 25mm;          /* Ajustado para casar com a proporção da imagem */
            height: 27mm;        /* Ajustado para casar com a proporção da imagem */  /* O fundo branco interno que você pediu */
            border: none;         /* Sem borda extra para não chocar com a linha preta do logo */
            border-radius: 50%;   /* Recorta o fundo branco em elipse perfeita */
            z-index: 3;           /* Fica por cima da faixa azul */
            overflow: hidden;     /* Força o corte oval perfeito */
        }

        .selo img {
            width: 100%;
            height: 100%;
            object-fit: fill;     /* Força a imagem a colar milimetricamente nas bordas brancas */
            display: block;
        }



        /* ── Foto do candidato ────────────────────────────────────────── */
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

        /* ── Título do documento ──────────────────────────────────────── */
        .titulo-documento {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin: 1mm 0 0mm 0;
            padding-bottom: 1mm;
            margin-left: -22mm;
        }

        /* ── Campos dinâmicos ─────────────────────────────────────────── */
        .campos { font-size: 6pt; line-height: 1.55; margin-right: 10mm; }
        .campos .linha { 
            margin-bottom: 0mm;
            margin-left: -22mm;
            font-size: 1.4em;
        }
        .campos .rotulo { font-weight: bold; display: inline; }
        .campos .valor { display: inline; }

        /* ── Categoria + número, por baixo da foto ────────────────────── */
        .info-foto {
    position: absolute;
    top: 19mm;
    right: 2mm;           /* ajustado para manter centrado com a caixa mais larga */
    width: 26mm;          /* alargado para caber "Nº 2026/00005" numa linha */
    text-align: center;
    font-size: 6pt;
    font-weight: bold;
    z-index: 2;
    white-space: nowrap;  /* impede a quebra de linha */
}

        /* ── QR code ──────────────────────────────────────────────────── */
        .qr-caixa {
            position: absolute;
            bottom: 3mm; right: 5mm;
            width: 18mm; height: 18mm;
            text-align: center;
            font-size: 4pt;
            color: #94a3b8;
            z-index: 2;
            background: #fff;
        }
        .qr-caixa img { width: 100%; height: 100%; }

        /* ── Verso — texto legal fixo ─────────────────────────────────── */
        .verso .diploma-artigo {
            text-align: center;
            font-size: 8pt;
            margin-bottom:2mm;
            margin-left: -15mm;
        }
        .verso .diploma-artigo strong { display: block; margin-top: 0.5mm; }
        .verso .texto-legal {
            font-size: 8pt;
            text-align: justify;
            line-height: 1.4;
            margin-right: 1mm;
            margin-left: -21mm;
        }
        .verso .contactos {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 0mm;
            margin-left: -21mm;
        }
        .verso .contactos a { color: #1d4ed8; text-decoration: none; }
        .verso .validade {
            left: 30mm;
            font-size: 8.4pt;
            font-weight: bold;
            z-index: 2;
            margin-left: -21mm;
        }
        .verso .assinatura {
            position: absolute;
            bottom: 3mm;
            left: 50%;
            margin-left: -13mm;
            width: 26mm;
            text-align: center;
            font-size: 8pt;
            z-index: 2;
            font-weight: bold;
        }
        .verso .assinatura .assinatura-img {
            display: block;
            margin: 0 auto;
            height: 18mm;
            max-width: 32mm;
            object-fit: contain;
        }

        .verso .bandeira {
            position: absolute; top: -1mm; right: -1mm;
            width: 15mm; height: 10mm;
            border: 0.5pt solid #cbd5e1;
            z-index: 2;
            background: #fff;
        }

        .verso .bandeira img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
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

            <div class="titulo-documento">Identidade Profissional</div>

            <div class="campos">
                <div class="linha"><span class="rotulo">Nome:</span> <span class="valor">{{ $nomeCompleto }}</span></div>
                <div class="linha"><span class="rotulo">Categoria:</span> <span class="valor">{{ $categoria }}</span></div>
                <div class="linha"><span class="rotulo">Título:</span> <span class="valor">Técnico Médio de {{ $curso }}</span></div>
                <div class="linha"><span class="rotulo">B.I. nº:</span> <span class="valor">{{ $biNumero }}</span></div>
                <div class="linha"><span class="rotulo">Função:</span> <span class="valor">{{ $funcao }}</span></div>
                <div class="linha"><span class="rotulo">Nacionalidade:</span> <span class="valor">{{ $nacionalidade }}</span></div>
                <div class="linha"><span class="rotulo">Província:</span> <span class="valor">{{ $provincia }}</span></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ VERSO ═══════════════ --}}
    <div class="cartao verso">
        <div class="forma-azul"></div>
        <div class="bandeira"><img src="{{ public_path('images/angola.png') }}" alt="Bandeira de Angola"></div>
        
        <div class="assinatura">
            <img src="{{ public_path('images/assinatura.jpeg') }}" alt="Assinatura do Presidente" class="assinatura-img">
        </div>
        <div class="qr-caixa"><img src="{{ $qrCodeDataUri }}" alt="QR"></div>

        <div class="conteudo">
            <div class="diploma-artigo">
                (DR. III-nº 143 de 22 de Agosto/2019)
                <strong>Artº. 4º do 4,5,6,7,8 dos Estatutos</strong>
            </div>

            <div class="texto-legal">
                Este cartão é pessoal e intransmissível, distribuído para o uso de identificação em exercício de profissão e deve-se acompanhar por um outro documento de identificação sempre que solicitado, e pedimos às autoridades militar, civil e tradicional que não haja impedimento nas suas actividades.
            </div>

            <div class="contactos">
                948607983 / 941860550 / 930819054<br>
                Site: <a href="http://www.aatdspa.ao">www.aatdspa.ao</a> /
                <a href="mailto:aatdspa@gmail.com">aatdspa@gmail.com</a> / Página: AATDSPA
            </div>

            <div class="validade">Validade: {{ $validadeAte }}</div>

        </div>
    </div>

</div>
</body>
</html>