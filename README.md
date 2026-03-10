# Pré-Consulta Neurológica — Dr. Igor Campana

Sistema de triagem e pré-consulta médica desenvolvido para otimizar o atendimento neurológico, permitindo que pacientes enviem seus dados clínicos, queixas e exames de forma segura antes da consulta presencial.

---

## 🚀 Visão Geral

Esta aplicação é uma Single Page Application (SPA) robusta que serve como ponte de comunicação entre o paciente e o médico. Ela automatiza o fluxo de coleta de dados, validação de endereços e armazenamento seguro de documentos clínicos.

### Principais Funcionalidades
- **Formulário Inteligente**: Coleta de dados pessoais, epidemiológicos e clínicos com validação em tempo real.
- **Integração ViaCEP**: Preenchimento automático de endereço baseado no CEP.
- **Gestão de Documentos**: Upload múltiplo de exames (PDF, JPG, PNG) com limite de 10MB por arquivo.
- **Painel Administrativo**: Acesso exclusivo para o médico, com autenticação segura, visualização de pacientes e cópia rápida de dados para prontuários eletrônicos.
- **Design Médico Premium**: Interface elegante seguindo o manual de marca do Dr. Igor Campana.

---

## 🛠️ Stack Tecnológica

- **Frontend**: HTML5, CSS3 (Vanilla), JavaScript (ES6+).
- **Backend-as-a-Service**: [Supabase](https://supabase.com/)
  - **Database**: PostgreSQL para armazenamento de registros.
  - **Storage**: Buckets para armazenamento dos arquivos de exames.
  - **Auth**: Gerenciamento de sessões e login médico.
- **APIs Externas**: [ViaCEP](https://viacep.com.br/) para consulta de endereços.
- **Tipografia**: Google Fonts (League Spartan & Merriweather).

---

## 🎨 Design System (Manual de Marca)

A interface utiliza uma paleta de cores e tipografia rigorosamente selecionadas para transmitir profissionalismo e conforto visual:

### Paleta de Cores
- **Primária**: `#20515F` (Cabeçalhos, Botões de Ação, Destaques)
- **Fundo Principal**: `#E5EBEA` (Superfícies claras)
- **Secundária**: `#DDD0C6` (Cards, Elementos de apoio)
- **Texto**: `#737271` (Elementos neutros e descrições)

### Tipografia
- **Títulos e Ações**: `League Spartan` (Hierarquia profunda do Thin ao Black).
- **Corpo de Texto**: `Merriweather` (Foco em legibilidade clínica).

---

## 📊 Estrutura de Dados (Supabase)

### Tabela: `pacientes_preconsulta`
| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | uuid | Chave primária automática. |
| `timestamp_cliente` | bigint | Timestamp gerado no frontend para unicidade. |
| `dados` | jsonb | Objeto contendo todas as respostas do formulário. |
| `exames` | json | JSON contendo nomes e caminhos (paths) dos arquivos no Storage. |
| `created_at` | timestamptz | Data de criação automática no banco. |

### Bucket: `exames`
Os arquivos são organizados em pastas nomeadas pelo `timestamp_cliente` para garantir que documentos de pacientes diferentes não se misturem.

---

## 🔐 Segurança e LGPD

- **Privacidade**: O arquivo `robots.txt` impede a indexação da página por motores de busca.
- **Conformidade**: Formulário inclui checkbox explícito de consentimento para tratamento de dados sensíveis conforme a LGPD.
- **Segurança**: Acesso aos dados dos pacientes é protegido por RLS (Row Level Security) no Supabase e autenticação JWT. Os links de exames são gerados em tempo real como `SignedURLs` com validade de 24h.

---

## 👨‍💻 Onboarding para Desenvolvedores / IA

Se você está assumindo este projeto ou é uma IA ajudando no desenvolvimento:

1.  **SPA Única**: Todo o código reside em `index.html`. Não há processo de build complexo.
2.  **Lógica de Estado**: O estado de login e a alternância de telas (Form -> Sucesso -> Painel) são controlados via DOM Manipulation puro.
3.  **Configuração**: As chaves do Supabase estão no objeto `CONFIGURAÇÕES` no início da tag `<script>`.
4.  **Extensibilidade**: Para adicionar novos campos, siga a estrutura da classe `.campo` e adicione a chave correspondente no objeto `dados` dentro da lógica de `submit`.

---

## 🛠️ Deploy

O projeto foi desenhado para ser hospedado em plataformas de arquivos estáticos como **Vercel**, **Netlify** ou **GitHub Pages**.

1.  Conecte o repositório à plataforma.
2.  Garanta que o `index.html` esteja na raiz.
3.  No Supabase, certifique-se de que o domínio de produção está na lista de `External Redirect URLs` e `CORS Allowed Origins`.

---
*Documentação gerada por Antigravity AI em Março/2026. Mantido por Dr. Igor Campana.*
