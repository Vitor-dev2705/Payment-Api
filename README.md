# 🚀 SmartRoute Payment API

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 10" />
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2" />
  <img src="https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker" alt="Docker Sail" />
  <img src="https://img.shields.io/badge/Swagger-OpenAPI_3-85EA2D?style=for-the-badge&logo=swagger" alt="Swagger" />
</p>

> **Infraestrutura de pagamentos resiliente.** Uma API de alta disponibilidade que gerencia transações através de múltiplos gateways com um motor de **failover automático** integrado.

---

## 💎 Diferenciais Técnicos

Este projeto foi arquitetado para suportar falhas críticas de infraestrutura utilizando padrões de design modernos:

| Recurso | Descrição |
| :--- | :--- |
| **Pattern Strategy** | Gateways desacoplados, permitindo trocar provedores sem alterar o core do sistema. |
| **Failover Engine** | Retry automático em gateways secundários caso o principal retorne erro ou timeout. |
| **Atomicidade** | Uso de `DB::transaction` para garantir consistência entre o banco local e o gateway externo. |
| **Segurança ACL** | Controle de acesso baseado em funções (Admin vs Vendedor) via Sanctum. |

---

## 🛠️ Instalação e Setup (Docker)

Siga os passos abaixo para subir o ambiente completo em poucos minutos:

### 1. Preparação do Ambiente
```bash
# Clonar o repositório
git clone https://github.com/Vitor-dev2705/Payment-Api.git
cd Payment-Api

# Configurar variáveis de ambiente
cp .env.example .env
```

### 2. Execução via Laravel Sail

### Instalar dependências sem PHP local
`
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
`
### Iniciar os containers
`./vendor/bin/sail up -d`

### Finalizar configuração da aplicação
```
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

📖 ### Documentação Interativa (Swagger)

A API possui documentação auto-gerada que permite testar os endpoints em tempo real.

```
    🔗 Acesso: http://localhost/api/documentation

    🔑 Credenciais de Teste:

    Login: admin@betalent.tech

    Senha: password123
```

### Como Autenticar:


Execute o endpoint `POST /api/login.`

Copie o campo `token` da resposta.

Clique no botão Authorize (topo da página) e cole o token.

## 🛣️ Arquitetura de Endpoints

| Método | Endpoint | Acesso | Descrição |
| :--- | :--- | :--- | :--- |
| <img src="https://img.shields.io/badge/POST-49CC90?style=flat-square&logoColor=white" alt="POST"> | `/api/login` | **Público** | Autenticação e emissão de Bearer Token. |
| <img src="https://img.shields.io/badge/GET-61AFFE?style=flat-square&logoColor=white" alt="GET"> | `/api/products` | **Livre** | Catálogo de produtos disponíveis. |
| <img src="https://img.shields.io/badge/GET-61AFFE?style=flat-square&logoColor=white" alt="GET"> | `/api/clients` | <span style="color: #ffca28">**Admin**</span> | Listagem de clientes e histórico de compras. |
| <img src="https://img.shields.io/badge/POST-49CC90?style=flat-square&logoColor=white" alt="POST"> | `/api/purchase` | **Auth** | Processamento de checkout com failover automático. |

⚙️ ### Configurações de Gateway

Você pode gerenciar o comportamento do motor de pagamento diretamente no seu arquivo .env:

### Definir ordem de tentativa dos gateways
`GATEWAY_PRIMARY=pagseguro`
`GATEWAY_SECONDARY=pagarme`

### Configurações de Timeout (em segundos)
`PAYMENT_TIMEOUT=30`

## 🧪 Comandos de Manutenção

| Objetivo | Comando |
| :--- | :--- |
| 📘 **Regerar Swagger** | `./vendor/bin/sail artisan l5-swagger:generate` |
| 🧹 **Limpar Cache** | `./vendor/bin/sail artisan config:clear` |
| 🧪 **Rodar Testes** | `./vendor/bin/sail artisan test` |
| 🔄 **Reiniciar Containers** | `./vendor/bin/sail down && ./vendor/bin/sail up -d` |
