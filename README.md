<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=black" alt="React 19">
  <img src="https://img.shields.io/badge/Inertia.js-2-9553E9?logo=inertia&logoColor=white" alt="Inertia.js">
  <img src="https://img.shields.io/badge/Tailwind-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white" alt="MySQL 8">
  <img src="https://img.shields.io/badge/Reverb-WebSocket-FF2D20?logo=laravel&logoColor=white" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/status-em%20desenvolvimento-yellow" alt="Status">
</p>

# Cuidar+

**Seu cuidado continua em casa.**

Plataforma web responsiva de apoio ao paciente e à família no período pós-alta hospitalar. Centraliza orientações de cuidado, lembretes de rotina, sinais de alerta, conteúdo educativo e notificações em tempo real — para quem utiliza dispositivos de saúde como colostomia, ileostomia, sondas e afins.

> ⚠️ O Cuidar+ é uma ferramenta de apoio e informação. **Não substitui atendimento médico ou de enfermagem.** Em situações de emergência ou sinais de alerta, a orientação do sistema é sempre buscar a equipe de saúde.

---

## Sobre o projeto

O Cuidar+ ajuda o paciente a entender rapidamente **o que precisa fazer, como fazer, e quando procurar ajuda** depois da alta hospitalar — reduzindo dúvidas e dando mais segurança para paciente e família nos primeiros dias em casa.

O sistema tem dois perfis:
- **Paciente** — acompanha seu dispositivo, orientações, lembretes, diário de cuidados e recebe notificações.
- **Administrador/profissional** — mantém todo o conteúdo (dispositivos, orientações, alertas, conteúdos educativos) sem precisar alterar código.

## Funcionalidades

**Paciente**
- Cadastro em 3 passos (dados pessoais, dispositivo, plano de cuidados)
- Painel inicial com resumo semanal de cuidados
- Orientações por dispositivo, com passo a passo detalhado por tipo de cuidado
- Sinais de alerta com explicação de quando procurar ajuda
- Lembretes com calendário semanal (troca, higiene, medicamentos)
- Diário/histórico de cuidados realizados
- Conteúdos educativos com favoritos
- Notificações em tempo real (lembrete próximo do horário, novo alerta cadastrado)
- Perfil e cuidador/familiar vinculado

**Administrador**
- Dashboard com estatísticas gerais
- Gestão de pacientes, dispositivos, orientações, sinais de alerta e conteúdos educativos
- Auditoria de notificações enviadas

> O botão **"Falar com a equipe"** está presente na interface, mas o mecanismo real de contato (chat, e-mail, ticket, etc.) ainda **não foi definido**.

## Tecnologias

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.4 + Laravel 13 |
| Frontend | React 19 + Tailwind CSS 4 |
| Integração | Inertia.js 2 |
| Banco de dados | MySQL 8 |
| Tempo real | Laravel Reverb (WebSocket) |
| Autenticação | Laravel Breeze (stack Inertia/React) |
| Testes — backend | PHPUnit 12 (unitário, integração, Feature) |
| Testes — frontend | Cypress (E2E + Component Testing) |
| Ambiente | Docker + Docker Compose |

Arquitetura: monólito em camadas (Controller → Form Request → Service → Repository → Model), com nomenclatura de domínio em português (classe, arquivo **e** método).

## Como rodar o projeto

Pré-requisitos: Docker e Docker Compose instalados.

```bash
# clonar o repositório
git clone <url-do-repositorio>
cd cuidar-plus

# copiar o .env de exemplo
cp .env.example .env

# subir os containers (app, nginx, MySQL, Reverb, worker de fila, Vite)
docker compose build
docker compose up -d

# instalar dependências PHP, gerar chave e preparar o banco
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

O serviço `node` instala as dependências de frontend e sobe o Vite automaticamente na primeira subida.

| O quê | Endereço |
|---|---|
| Aplicação | http://localhost |
| Vite (dev server) | http://localhost:5173 |
| MySQL | `localhost:3306` |
| Reverb (diagnóstico direto) | `localhost:8080` |

Todas são as portas padrão de cada serviço, e são configuráveis no `.env` (`APP_PORT`, `VITE_PORT`, `DB_PORT_EXTERNO`, `REVERB_PORT_EXTERNO`). Se outro projeto local estiver ocupando alguma delas, pare aquela stack (`docker compose stop`) — os projetos não precisam rodar ao mesmo tempo.

### Rodando os testes

```bash
# backend (PHPUnit) — usa o banco separado cuidar_plus_teste
docker compose exec app php artisan test
docker compose exec app php artisan test --testsuite=Unit

# frontend (Cypress, end-to-end)
docker compose stop node                              # desliga o Vite dev server
docker compose run --rm node npm run build            # gera os assets de produção
docker compose --profile testes run --rm cypress npx cypress run --e2e

# voltar ao modo de desenvolvimento
docker compose up -d node

# frontend (Cypress) — component testing, não precisa da aplicação no ar
docker compose --profile testes run --rm cypress npx cypress run --component
```

> O Cypress roda **dentro da rede do compose** e acessa a aplicação como `http://nginx`. Nesse contexto ele não alcança o Vite dev server (que responde em `localhost:5173` na máquina do desenvolvedor), então os testes E2E rodam contra os assets compilados. Por isso os dois passos antes do `cypress run`.

Os testes E2E usam as contas do seeder e criam e-mails com timestamp no cadastro, então podem ser repetidos sem precisar recriar o banco.

### Contas de demonstração

Criadas pelo `php artisan migrate --seed` (senha `senha1234` para todas):

| Perfil | E-mail | Dispositivo |
|---|---|---|
| Administrador | `admin@cuidarplus.local` | — |
| Paciente | `maria@cuidarplus.local` | Colostomia |
| Paciente | `joao@cuidarplus.local` | Sonda vesical |

> São dois pacientes de propósito: a regra de isolamento (paciente A nunca acessa dado do paciente B) fica demonstrável.

## Estrutura do projeto

```
cuidar-plus/
├── README.md
├── CLAUDE.md                  # convenções obrigatórias do projeto
├── docs/                      # especificação (visão, arquitetura, testes, plano)
├── prototipo/                 # referência visual e de UX validada
├── app/
│   ├── Http/                  # Controllers, Requests, Middleware
│   ├── Models/
│   ├── Services/
│   ├── Repositories/          # Contracts/ + Eloquent/
│   ├── Events/ Listeners/ Notifications/
│   ├── Enums/ DTOs/ Policies/
│   └── Providers/
├── resources/
│   ├── css/app.css            # tokens de marca (Tailwind 4 @theme)
│   └── js/                    # Pages/, Components/, Layouts/ (React + Inertia)
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── channels.php           # canais privados do Reverb
├── database/
│   ├── migrations/
│   └── seeders/
├── tests/                     # Unit/, Integration/, Feature/ (PHPUnit)
├── cypress/                   # e2e/, component/
├── docker/                    # php/, nginx/, mysql/
└── docker-compose.yml
```

## Status

Em desenvolvimento. Etapas concluídas:

- [x] **1. Fundação** — Laravel 13, React 19, Inertia 2, Tailwind 4, Breeze, MySQL, Reverb e Docker funcionando
- [x] **2. Banco** — migrations, models, relacionamentos, enums e seeders de demonstração
- [x] **3. Arquitetura em camadas** — repositories + interfaces, services, DTOs, policies e middleware de perfil
- [x] **4. Autenticação e perfis** — acesso, cadastro em 3 passos, recuperação de senha, Shell responsivo e separação paciente/administrador
- [x] **5. Administração** — painel, CRUDs de dispositivos, orientações, alertas e conteúdos, lista de pacientes e auditoria de notificações
- [x] **6. Paciente — núcleo** — dispositivo, orientações com abas, sinais de alerta, lembretes com calendário semanal e diário
- [x] **6b. Paciente — complementares** — conteúdos educativos com favoritos, perfil e o ponto de entrada de "Falar com a equipe"
- [x] **7. Notificações** — Events, Listeners, Notifications (`database` + `broadcast`), canal privado do Reverb, agendador e central do paciente
- [ ] 8. Testes
- [ ] 9. Visual e responsividade
- [ ] 10. Seed de demonstração
- [ ] 11. Deploy

## Autor

Pedro
