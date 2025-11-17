# **PaymentControl**
## **1. Introdução**
Este documento apresenta o modelo de projeto do sistema `PaymentControl`", teste ténico para medir as habilidades de um indivíduo;
Este documento está composto em tópicos, respectivamente:  
2: Objetivo  
3: Stack Técnica  
4: Segurança  
5: Diagrama da Arquitetura  
6: Principais Rotas  
7: Camada de Interface do Usuário  
8: Camada de Regra de Negócio e Padrão de Projeto  
9: Explicando decisões arquiteturais  

## 2. Objetivo
Em uma era voltada em quase 100% para a tecnologia, existem diversos problemas e dificuldades para o continuamento dessa engrenagem, um deles se reside nos pagamentos e consultas bancárias online onde existem muitas agências que não possuem assistências físicas, forçando o usuário e as empresas a se adequarem ao "novo". Tendo em vista tais afirmações, nós desenvolvemos uma aplicação multi-gateway para solucionar alguns problemas: Consultas onlines e pagamentos, Os usuários não possuirão registro apenas em um banco, podendo ser multíplas as integrações com as intituições e entre outros problemas. 

## 3. Stack Técnica
- Linguagem de programação: PHP
- Banco de Dados: PostgreSQL
- Conteinerização: Docker
- Curl e Insomnia

## 4. Segurança
A aplicação possui segurança em seus dados,sendo imune a SQL injection, e outros ataques que podem corromper o sistema, como multiplas requisições instantaneamente. Também é utilizado
tokens e senhas de acesso aos dados privados.

## 5. Diagrama de Arquitetura 
![Diagrama](image.png)

## 6. Principais Rotas
[...]
Siga para a camada "extra" abaixo neste documento!

## 7. Camada de Interface do Usuário
Não existe layouts, apenas o cliente API (Insomnia, Postman, CURL)

## 8. Camada de Regra de Negócio e Padrão de Projeto
Uma camada em específico para validações de rotas, (Campos obrigatórios), outra para as regras de negócios (Pagamento sem saldo), outra camada para conexão ao banco de dados (Consultas e Escritas SQL) e uma última camada para receber as requisições e redirecionar para a camada correspondente.

## 9. Explicando decisões arquiteturais  

Foi definido a linguagem PHP por ser uma linguagem robusta para criar uma API RESTful, que se comunica muito bem com o Nginx. Nginx é um WebServer que ficará responsável por renderizar o projeto em uma camada web. Para expor as portas de forma dinâmica e containerização, usaremos docker e php-fpm. O PHP-FPM será responsável por integrar o php ao servidor web, além de melhorar os processamentos de scripts, tornando os mesmo mais rápidos e eficiente.
  
o patrão de Arquitetura: **MVC** (Model, View, Controller), composto pelos respectivos componentes:
- Model: Responsável pelas regras de negócios  
- View: Não existe layout, a view seria os clientes API
- Controller: Responsável por controlar as rotas e devolver as respostas para o requisitante. Dispacha as requisições para as services.
-  Service: Validações de rotas, quais campos são necessários e chamadas de model e repositories.
- Repository: Conexão, escritas e consultas no banco de dados.

# **Extra**
Passo a passo para utilizar a aplicação.

## 1 - inicialize o container do docker
comando: "docker compose up --build -d",
Deverá receber uma mensagem de sucesso na criação dos containers.

## 2 - Crie o .env no inicio da aplicação (na / do projeto)
Banco de dados exemplo:
DB_NAME ="payment_control"
DB_HOST ="192.168.1.109"
DB_PORT ="5334"
DB_USERNAME ="postgres"
DB_PASSWORD ="root"

Chave secreta para autorização de acesso:
SECRET_KEY_JWT = "LOBISOMEN1234567890aaaaaaaa"

Credenciais do mercado pago
MERCADOPAGO_ACCESS_TOKEN=""
CLIENT_ID_MERCADOPAGO =""
CLIENT_SECRET_MERCADOPAGO =""
PUBLIC_KEY_MERCADOPAGO =""
MERCADOPAGO_BASEURL ="https://api.mercadopago.com"

## 3 - Tabelas necessárias no banco de dados:
Adicione essas tabelas ao banco de dados, será necessária acessão pois não temos uma VPS ou algo do tipo para anexar a conexão.
```sql
create table client(
	id serial primary key,
    uuid UUID default gen_random_uuid(),
	name text,
	password text,
	email text
);
CREATE TABLE mercado_pago (
    id SERIAL PRIMARY KEY,
    uuid UUID default gen_random_uuid(),
    client_id INTEGER NOT NULL,
    payment_id BIGINT NOT NULL,
    status VARCHAR(50),
    status_detail VARCHAR(255),
    transaction_amount NUMERIC(12,2),
    external_reference VARCHAR(255),
    date_created TIMESTAMPTZ,
    date_approved TIMESTAMPTZ,
    date_last_updated TIMESTAMPTZ,
    date_of_expiration TIMESTAMPTZ,
    qr_code_base64 TEXT,
    qr_code TEXT,
    ticket_url TEXT,
    CONSTRAINT fk_pixpayment_client
        FOREIGN KEY (client_id)
        REFERENCES client(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);	
``` 

## 4. Crie seu usuário por um client API (insomnia, postman etc)
POST localhost:8080/client  

Request:  
```json
{
	"name": "teste",
	"password": "123",
	"email": "teste@gmail.com"
}
``` 
Insira um email válido para que seja possível gerar uma cobrança para este email.  

Response:
```json
	"data": "User created successfully"
``` 
 ## 5. Faça login na aplicação
 Nada será feito sem login, é importante que o usuário o faça.

  POST localhost:8080/login

  Request: 
  ```json
{
	"name": "teste",
	"password": "123"
}
  ```

  Response:
  ```json
  {
	"error": false,
	"success": true,
	"jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwibmFtZSI6ImNoZWZlIiwiZXhwIjoxNzYzMzQ1MjE5fQ.i56KlX2KR4afSdIlLFx4E6HxOT0qr6WivQQZmeBbUxM"
}
```

Copie o JWT, será o token para autenticar o acesso, sem ele o usuário não haverá permissão para realizar outras ações.  
Lembre sempre de adicionar o jwt no header das requisições

## 6. Fazer uma cobrança no mercado pago
É necessário que a pessoa responsável pela aplicação tenha uma conta no mercado pago, pois a cobrança será feita em PIX, então registre-se e adicione uma chave PIX para si.

[Clique Aqui para se registrar!](https://mercadopago.com.br/)

acesse esta rota para utilizar o serviço:

POST http://localhost:8080/mercadopago

Request:
```json
{
	"email": "teste@gmail.com",
	"amount": "10"
}
```

O valor no código está definido como 1 real, porém é necessário que você ainda defina o valor.

Response:

```json
{
	"error": false,
	"success": true,
	"data": {
		"0": {
			"id": 20,
			"client_id": 1,
			"payment_id": 134115215490,
			"status": "pending",
			"status_detail": "pending_waiting_transfer",
			"transaction_amount": "1.00",
			"external_reference": null,
			"date_created": "2025-11-16 21:41:19+00",
			"date_approved": null,
			"date_last_updated": "2025-11-16 21:41:21+00",
			"date_of_expiration": "2025-11-17 21:41:19+00",
			"qr_code_base64": "iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX///8AAABVwtN+AAAK1UlEQVR42uzdQXJiuw4A0EtlwJAlsBSWliwtS8kSGDKg8K/mYSzZhpDu/xpe1dEklf9zr8/tmZ5kaRFCCCGEEEIIIYQQQgghhBBCCCGEEEIIIYQQQgghhBBCCCGEEP9urMsQn8vq8rPGqpT9srz/+h/3v35/K+VrWUo5Lsu2lPNLzr//E+dHdtc//ifqw++/Htq0w87/53ZE0NLS0tLS0tLS0tLS0tL+H7Rf3e9VW+Pjoj0rqzpo2wHryyeu46cvF93p/PPj10vOL10unxp+1tjR0tLS0tLS0tLS0tLSvrK2O7Dqws9yOaiqz3/81qWn/2hLObScd3dVhpjkvO0TD7S0tLS0tLS0tLS0tLT/WW3KfUMRtD701XLcMefdN/X79TW1cnrscl5aWlpaWlpaWlpaWlra/7g2R2qh3cYD12PrbMp5zw+fmnIzq5zS0tLS0tLS0tLS0tLS0v4b2r5U2xp8l5Zct1Jtru+2TD00+u6GDL2+7Ni6hutDf9bbTEtLS0tLS0tLS0tLS/s3tdPJRanRd5WGDn3cnFxU09X1z1/yZ3OWaGlpaWlpaWlpaWlpaf+Odhqr6XXR80E15/1que+NnPefh1MZdjMmzuX60t8OWlpaWlpaWlpaWlpa2r+p3cau13WrmLZFKHn+bTpokq52q1jqxdW+clrLrneaeGlpaWlpaWlpaWlpaWlfTxv6bi/qoO37bvfxE8PVz3oV9DO+dNMmGN3NeaerWGhpaWlpaWlpaWlpaWlp/0wbGn2X4cC662WVMvOPWYl2G++aHtqnfnYvSd3CqUh865NpaWlpaWlpaWlpaWlpX02bYtelqW3ny1tb3xKG5tZP7dLV1fiSfvfL9rJ+NPx7tQUytLS0tLS0tLS0tLS0tK+nzcrWLZyUYYNo0h67xHmyRWW5XlgtKVHuf//x5CJaWlpaWlpaWlpaWlra52i3res1FT3rNdGxlfate3Vfdl3PHs6/3yq/3q+c0tLS0tLS0tLS0tLS0tL+VNs1+C7di2uSnRt90wFtvtKS0vnWclzVk27h/mW0tLS0tLS0tLS0tLS0L61ts3IPTfkZtZvuwG27Lnp+aBtz30NTtqLxqSlD4pz+vbYPzFmipaWlpaWlpaWlpaWlfaK2nxcU/nYXi51hc+h739kbK6clpq2bUZsusH61nS+lu8hKS0tLS0tLS0tLS0tL++Lacq2gnm4MGwqRXrzulJO+2/HO6U+UtLS0tLS0tLS0tLS0tLQ/14ZIa1r2s1FJx275Z/jkpA13TndRO935ckyvelhNS0tLS0tLS0tLS0tL+zRtl66uugT31LqEQ/raHk5dwkG/j9rTqKw7X9Ld0296m2lpaWlpaWlpaWlpaWmfqF2n4ueYrm4uO1/qNdHJ8s8y6Ptu4V67H3Pch7uFaWlpaWlpaWlpaWlpaV9AO62k5oNKCVtUwubQelA3/ihMLkraPFS3Xz96V0RLS0tLS0tLS0tLS0v7YtpU9Kwts5ODwgXMPnFOZdhxsefbZXJRr53WcGlpaWlpaWlpaWlpaWlpf1vbL/88tMx8N2TmS+oaTg2+X13XcH24Hvw+u3uaPvkne05paWlpaWlpaWlpaWlpn6MNOe+uu2PaNoeGO6ipzhsae7fXT52/7G7Ou+0+efdAnZeWlpaWlpaWlpaWlpb2Cdrl8rf1gJTzlvbimutuuqFDk2ui3SqWSa6bxh+9jV3CtLS0tLS0tLS0tLS0tC+qXXfXRUvXf1tmW1MmI2unv/dNvGEVSz+pqP1+uFPkpaWlpaWlpaWlpaWlpaX9qbZLslfTg5Zr3fdtbPD9indMD7G1ePKS1Kd87CrO3+x8oaWlpaWlpaWlpaWlpX2iNpVm69/2m0NziTYVh8O03hsvOaXpvO9dIj12CxdaWlpaWlpaWlpaWlraF9d2c3Brznt/c2ivbY2+Qft5eThNLiolrB093su+aWlpaWlpaWlpaWlpaV9Kux67YNuEolW3RWWi3Y79t21z6D6eFD65/H/6b2lpaWlpaWlpaWlpaWlpD9+VTft1LSVVW99L3vnyERt9t7E7OI1Kmo/8rZ/cPjW0Hi+P/fcEWlpaWlpaWlpaWlpa2mdp803PVppNJdrSDrqTrk7vnH7GT0xTe+tLjmOx+NtqNC0tLS0tLS0tLS0tLe1ztJOdL/WgEl/c57zpgPWYtn5G7dI9vOk+MSTO3945paWlpaWlpaWlpaWlpX2idrJF5fP6wvmd03ZA1qaya4tT05XYxFtz3Z/fOaWlpaWlpaWlpaWlpaV9orZ0/bbLtXK6dAfV4uexacNtzvrQeBW03H5J7b9NTbyf9+u8tLS0tLS0tLS0tLS0tLQPasOmlRtJ9dJNLgoHlbiu5TDWeXtlKyIfbxeLdw9M66WlpaWlpaWlpaWlpaV9grbPfQ+XoUMh9/2Iaes+rm0ps7m3S5t/Gy6uLt2d05YoH8chug9uDqWlpaWlpaWlpaWlpaV9gnYbM8x1y3n7Smpr7O0fDuOP2oGT8mtNmDdxDNKxE3yT89LS0tLS0tLS0tLS0tI+UdvfOQ1rNHcxPQ1Fz4+4+CT14dbW2XXsv51vUXmfKZfHNofS0tLS0tLS0tLS0tLS0j6u7Zd/jvOWli65/uoOCCN/W5q/jEXjvlic0vwwrImWlpaWlpaWlpaWlpb2FbXhwPY3Nec9pTUtH7OcN2hT1/CuTBbF9PXdc7ylIvH9yUW0tLS0tLS0tLS0tLS0T9eG+uWNXDenqW1z6DHtfum0IefdxLm34WVpx0uo4dLS0tLS0tLS0tLS0tK+rnaMVbo2WmZ9uEl7aD+X7sBdN0y3XMqvbe7tkh5Oq1loaWlpaWlpaWlpaWlpaf9Y2+q84bro7uZU3lyiHa+JrtuU3l1M80vcAVM3hh7HIvHyaFWalpaWlpaWlpaWlpaW9jnaujk0lWhrSfY0Dtyd5rzhJctsWm/KecP60TA76bHeZlpaWlpaWlpaWlpaWtrnaPPkonZAqKCmNHW/hGuib93D6SWr9LL3ofyac94mKbS0tLS0tLS0tLS0tLSvrC1xEUpomW39t6eWpvaJchphO2nibXFqyl5dWuLccl9aWlpaWlpaWlpaWlra19Mus9bZcAGzP3h/o+yaPnl6i/P+J/+k/5aWlpaWlpaWlpaWlpaW9ne0y5InF+3HEu17bPRNg3aXbunnJtZ5T129t9/98nhvMy0tLS0tLS0tLS0tLe2ztOsx6azKsVQ7n2BUR9mm8Ufj5KJ8cTVpf7NbmJaWlpaWlpaWlpaWlvYJ2q9Z5TRNKgp3TmtMl34ubWhu/8kfw0vD+KMk+aa3mZaWlpaWlpaWlpaWlvbp2q51NhyUfi6t+BleOG2hbUN08xaVj+tr3tLJ4z8BLS0tLS0tLS0tLS0tLe2/oA2NvptfB6y6g5a246VPqnfx2mjfLbwZL66mT9w+uvOFlpaWlpaWlpaWlpaW9oW0odF305TvcennskwG7faA/ZjzjpOLar03LI7Z/UaGTktLS0tLS0tLS0tLS/vXtF23cNaWq3qyOTRouw2ik7JreEkbf7S0h7bf7XqhpaWlpaWlpaWlpaWlfbp2nFwUroeWEhaglG7Y0Fv71FG7dJ+6ag9v4tijYyf5JuelpaWlpaWlpaWlpaWlpX1QK4QQQgghhBBCCCGEEEIIIYQQQgghhBBCCCGEEEIIIYQQQgjx0vG/AAAA//+h2dILKeVKaAAAAABJRU5ErkJggg==",
			"qr_code": "00020126580014br.gov.bcb.pix013638cf42a1-bf0a-4a03-9c43-435ececf773152040000530398654041.005802BR5914GBCFGDHAE250166009Sao Paulo62250521mpqrinter13411521549063045D67",
			"ticket_url": "https://www.mercadopago.com.br/payments/134115215490/ticket?caller_id=2392056797&hash=6fdcfaba-b15b-45ac-aa33-169974406c82",
			"uuid": "a9c5e1cc-0c47-4124-aeb1-0f07ff4c6482"
		},
		"payment_id": 134115215490,
		"status": "pending",
		"ticket_url": "https://www.mercadopago.com.br/payments/134115215490/ticket?caller_id=2392056797&hash=6fdcfaba-b15b-45ac-aa33-169974406c82"
	}
}
```

ATENÇÃO! Guarde bem o UUID gerado, pois só terá acesso a ele nesta requisição,caso o perca, será necessário emitir outra cobrança PIX para que o tenha em mãos novamente.

## 7. Após o pagamento da cobrança

Após efetuar o pagamento, faça o seguinte requisição para verificar se o status do pagamento foi atualizado:

PATCH http://localhost:8080/mercadopago/uuid

Response:
```json
{
	"error": false,
	"success": true,
	"data": [
		{
			"payment_id": 134130865776,
			"status": "approved",
			"ticket_url": "https://www.mercadopago.com.br/payments/134130865776/ticket?caller_id=1810758300&hash=406f23f9-02b9-463f-afe7-aca82dd9e3f9",
			"uuid": "b823ea40-933d-4de1-9b5b-99ec04283c52",
			"name": "teste",
			"email": "teste@gmail.com"
		}
	]
}
```

## 8. Simulador de Cobrança:
existe um simulador de pagamentos, pois pela escassez de tempo, não foi possível implementar mais de um gateway de pagamento. Gostaríamos de ter utilizado hosts diferentes para cada gateway, convencidos de que esta seja a melhor decisão para um padrão de uma api multi-gateway, e infelizmente sofremos pelo mesmo motivo de ter sido criado um simulador.

POST http://localhost:8080/simuladorpago

Response:
```json
{
	"error": false,
	"success": true,
	"data": "Acesso autorizado ao Simulador Pago"
}
``` 

## 9. É possível atualizar um usuário adicionado ao banco de dados:
Siga esta rota...

PUT http://localhost:8080/client

Request:
```json
{
	"uuid": "87380f95-995c-443f-8745-f33d2ee7e8ea",
	"name": "chefer",
	"email": "1thalisgabriel1@gmail.com",
	"password": "123"
}
``` 
Response:

```Json
{
	"error": false,
	"success": true,
	"data": "User updated successfully"
}
``` 

## 10. Observacações:
Não visualizamos a documentação que estava no formulários, devido ao mal entendido, pensamos que haveria apenas uma página, porém existia a documentação após o submit, por isso há algumas discordâncias, que serão descritas nesta camada;

O banco utilizado foi o postgres, o escolhemos pela familiaridade e por ser um banco relacional. Deixando claro que o autor possui experiência em MySql.
Não há frameworks, o autor possui experiência em laravel, mas como mecionado, não tinha a informação de que era permitido, pois não visualizou a documentação inicial. Não foi utizado TDD pelo mesmo motivo.

## 11. Observações Pessoais:
O conteúdo sobre api multi-gateway é um pouco vago na internet, baseei em minhas próprias interpretações para o desenvolvimento desse projeto, como mencionado no tópico 8 da camada "extra", a ideia incial era utilizar os agentes de pagamentos em hosts separados, pois seria simples de gerenciar a atividade de todos os gateways presentes no projeto. Me diverti bastante com este projeto, fui desatento ao não perceber que havia uma documentação e ainda consigo me sentir orgulhoso pelo que eu fiz em poucos dias. O que havia na documentação facilitaria muito o meu desenvolvimento, porém coisas foras de nossas previsões acontecem.

**FIM DA APLICAÇÃO** 

Autor: Thalis Gabriel