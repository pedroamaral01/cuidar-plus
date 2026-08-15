<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=black" alt="React 19">
  <img src="https://img.shields.io/badge/Inertia.js-2-9553E9?logo=inertia&logoColor=white" alt="Inertia.js">
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
| Backend | PHP 8.3 + Laravel 13 |
| Frontend | React 19 + Tailwind CSS |
| Integração | Inertia.js 2 |
| Banco de dados | MySQL 8 |
| Tempo real | Laravel Reverb (WebSocket) |
| Autenticação | Laravel Breeze (stack Inertia/React) |
| Testes | Pest 4 |
| Ambiente | Docker + Docker Compose |

Arquitetura: monólito em camadas (Controller → Form Request → Service → Repository → Model), com nomenclatura de domínio em português.

## Como rodar o projeto

Pré-requisitos: Docker e Docker Compose instalados.

```bash
# clonar o repositório
git clone <url-do-repositorio>
cd cuidar-plus

# copiar o .env de exemplo
cp .env.example .env

# subir os containers (app, MySQL, Reverb)
docker compose build
docker compose up -d

# instalar dependências, gerar chave, migrar e popular o banco
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

# instalar dependências do front e compilar
docker compose exec app npm install
docker compose exec app npm run dev
```

A aplicação fica disponível em `http://localhost` (porta configurável no `.env`).

### Rodando os testes

```bash
docker compose exec app php artisan test
```

## Estrutura do projeto

```
cuidar-plus/
├── README.md
├── app/                       # Controllers, Services, Repositories, Models...
│   ├── Http/
│   ├── Services/
│   ├── Repositories/
│   ├── Events/
│   ├── Notifications/
│   └── Policies/
├── resources/js/              # páginas e componentes React/Inertia
│   ├── Pages/
│   ├── Components/
│   └── Layouts/
├── routes/
│   ├── web.php
│   └── channels.php
├── database/
│   ├── migrations/
│   └── seeders/
└── docker-compose.yml
```

## Status

Em desenvolvimento.

## Autor

Pedro