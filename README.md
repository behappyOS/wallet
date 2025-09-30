# Wallet App – Documentação

## Sobre o Projeto
O **Wallet App** é uma aplicação financeira desenvolvida em **Laravel 12** que simula uma **carteira digital**.  
O sistema permite que usuários realizem operações de **depósito, transferência, recebimento e reversão de transações**, garantindo consistência dos saldos.

Foi implementado seguindo boas práticas de **arquitetura limpa, segurança e padrões de código**, além de rodar com **Docker** para facilitar a execução em qualquer ambiente.

---

## Tecnologias Utilizadas
- **PHP 8.x**
- **Laravel 12**
- **MySQL** (banco de dados relacional)
- **Docker & Docker Compose**
- **Blade Templates (Bootstrap 5 + Bootstrap Icons)** para o frontend
- **Padrões SOLID e boas práticas de arquitetura**
- **Testes (Unitários e de Feature)** com PHPUnit

---

## Funcionalidades
- [x] **Cadastro de usuários**
- [x] **Autenticação de login**
- [x] **Depósito em conta** (com validação e ajuste de saldo negativo)
- [x] **Transferência de saldo entre usuários** (validação de saldo antes da operação)
- [x] **Recebimento de transferências** (conta de destino)
- [x] **Reversão de transações** (depósitos e transferências podem ser desfeitos)
- [x] **Histórico de transações**
- [x] **Frontend responsivo e moderno** com Blade/Bootstrap
- [x] **Testes unitários e de integração** para garantir a consistência

---

## Como Executar o Projeto

### Pré-requisitos
- **Docker** e **Docker Compose** instalados
- Porta **8000** disponível

### Clonar o repositório
```bash
git clone https://github.com/behappyOS/wallet.git
cd wallet
```

### Subir os containers
```bash
docker-compose up -d --build
```

### Instalar dependências
```bash
docker-compose exec app composer install
```

### Configurar variáveis de ambiente
Copie o arquivo de exemplo:
```bash
cp .env.example .env
```

Gere a chave da aplicação:
```bash
docker-compose exec app php artisan key:generate
```

Edite o arquivo `.env` e configure o banco de dados conforme o `docker-compose.yml`:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=wallet
DB_USERNAME=root
DB_PASSWORD=root
```

### Executar migrações
```bash
docker-compose exec app php artisan migrate
```

### Acessar a aplicação
Abra no navegador:
```
http://localhost:8000
```

---

## Executar Testes
O projeto possui testes unitários e de feature. Para rodar os testes:
```bash
docker-compose exec app php artisan test
```

---

## Estrutura do Projeto
- `app/Http/Controllers/AuthController.php` → Autenticação (login/logout/register)
- `app/Http/Controllers/WalletController.php` → Lógica de depósito, transferência e reversão
- `app/Models/User.php` → Usuário e saldo
- `app/Models/Transaction.php` → Registro de transações
- `resources/views/` → Telas Blade (login, registro, dashboard, depósito, transferência, histórico)
- `tests/Feature/` → Testes de integração
- `tests/Unit/` → Testes unitários

---

## Segurança Implementada
- **Autenticação protegida por sessão**
- **Validação de inputs** no backend
- **Proteção CSRF** em formulários
- **Regras de autorização** (usuário só pode reverter suas próprias transações)
- **Transações no banco (DB::transaction)** garantem atomicidade
