MicroMessage
============

Abaixo a lista de métodos disponíveis nesse WebService:

# Métodos

<table>
  <tr>
    <th>Caminho</th>
    <th>Método</th>
    <th>Descrição</th>
  </tr>
  <tr>
    <td>/messages/</td>
    <td>GET</td>
    <td>Retorna uma lista com todas as mensagens publicadas</td>
  </tr>
  <tr>
    <td>/messages/</td>
    <td>POST</td>
    <td>Adiciona uma nova mensagem (ver parâmetros abaixo)</td>
  </tr>
  <tr>
    <td>/messages/{id}</td>
    <td>GET</td>
    <td>Busca uma mensagem específica</td>
  </tr>
  <tr>
    <td>/messages/{id}</td>
    <td>DELETE</td>
    <td>Remove uma mensagem específica</td>
  </tr>
  <tr>
    <td>/messages/{id}</td>
    <td>PUT</td>
    <td>Atualiza uma mensagem existente (não permite criação de novas mensagens, nesse WebService os ids devem ser gerados automaticamente)</td>
  </tr>
</table>

Parâmetros para a mensagem:

<table>
  <tr>
    <th>Nome</th>
    <th>Tipo</th>
    <th>Obrigatório<th>
    <th>Tamanho Máximo</th>
  </tr>
  <tr>
    <td>author</td>
    <td>string</td>
    <td>Sim</td>
    <td>32</td>
  </tr>
  <tr>
    <td>message</td>
    <td>string</td>
    <td>Sim</td>
    <td>140</td>
  </tr>
</table>

# Instalação

Para que o WebService funcione será necessário:

- Criar um banco de dados chamado `micromessage` no container do postgres
- Criar o schema no novo banco de dados rodando o comando: php bin/console.php orm:schema-tool:update --force
