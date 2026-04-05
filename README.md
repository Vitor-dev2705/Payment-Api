# 💳 SmartRoute - Payment Gateway API

Esta é uma API de roteamento de pagamentos desenvolvida em **Laravel 10** e **PHP 8.3**, focada em resiliência e alta disponibilidade. O projeto utiliza **Docker** para garantir um ambiente idêntico ao de produção, com configuração totalmente automatizada.

## 🚀 Como Executar (Quick Start)

O projeto foi desenhado para ser **Zero Config**. Não é necessário criar arquivos manualmente ou configurar o banco de dados.

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/seu-usuario/Payment-Api.git](https://github.com/seu-usuario/Payment-Api.git)
    cd Payment-Api
    ```

2.  **Suba o ambiente completo:**
    ```bash
    docker-compose up -d --build
    ```

> **Nota:** Ao rodar este comando, o Docker irá automaticamente:
> * Criar o arquivo `.env` a partir do `.env.example`.
> * Gerar a chave única de criptografia (`APP_KEY`).
> * Executar as migrações do banco de dados e alimentar as tabelas (`Seeds`).
> * Gerar a documentação interativa do Swagger.

---

## 🔗 Endpoints e Acesso

Após o término do build (cerca de 30 a 60 segundos), acesse:

* **Documentação Swagger (UI):** [http://localhost/api/documentation](http://localhost/api/documentation)
* **API Health Check:** [http://localhost](http://localhost)

---

## 🛠️ Stack Tecnológica

* **Linguagem:** PHP 8.3
* **Framework:** Laravel 10
* **Banco de Dados:** MySQL 8.4
* **Documentação:** Swagger / OpenApi 3.0
* **Infraestrutura:** Docker & Docker Compose
* **Mock Service:** Gateways-mock (Simulação de adquirentes externas)

---

## 🏗️ Diferenciais do Projeto

* **Arquitetura Plug-and-Play:** O uso de `entrypoint` customizado no Dockerfile elimina a necessidade de scripts `.bat` ou comandos manuais.
* **Segurança:** Implementação de boas práticas com `.env.example` para proteção de dados sensíveis.
* **Resiliência:** Configuração de `healthcheck` no MySQL para garantir que a API só tente conectar quando o banco estiver pronto.
* **Foco em Dados:** Estrutura preparada para logs de transações e análise de performance de pagamentos.

---
