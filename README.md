# Dashboard de BI - Integração Mercado Livre API

Este projeto é um Dashboard de Business Intelligence que consome dados em tempo real da API do Mercado Livre para análise de tendências, especificações técnicas e mapeamento de catálogo.

## 🚀 Tecnologias Utilizadas
* **PHP 8.x**: Back-end e integração com API.
* **OAuth 2.0**: Autenticação segura com o Mercado Livre.
* **JavaScript / Chart.js**: Visualização de dados e gráficos dinâmicos.
* **CSS3 (Grid/Flexbox)**: Interface responsiva.

## 📊 Funcionalidades
* Busca dinâmica de produtos via API de Catálogo.
* Galeria de imagens técnica.
* Gráficos de comparação de atributos.
* Sistema de renovação automática de Token (Refresh Token).

## ⚠️ Nota Técnica (Limitação de Escopo)
O projeto utiliza a API pública oficial do Mercado Livre. Por diretrizes de segurança da plataforma (Status 403 Forbidden), os dados de **Preços Reais** de anúncios e **Buy Box** são restritos a contas comerciais homologadas. Por este motivo, a ferramenta foca na inteligência de **Tendências e Atributos Técnicos**.

## ⚙️ Como Rodar o Projeto
1. Clone o repositório.
2. Crie um arquivo `.env` na raiz do projeto.
3. Adicione suas credenciais do Mercado Livre no `.env`:
   ```text
   ML_CLIENT_ID=seu_id_aqui
   ML_CLIENT_SECRET=sua_chave_aqui