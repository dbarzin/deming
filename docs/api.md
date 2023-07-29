## API

Deming can be modified or updated via a REST API.

A REST API ([Representational State Transfer](https://fr.wikipedia.org/wiki/Representational_state_transfer)) 
is an application programming interface that respects the constraints of the REST 
architecture and enables interaction with RESTful web services. 

### Installing the API 

To install the API, you need to install Passport by running this command:

```bash
php artisan passport:install
```

- The Docker environment supports this functionality natively, via the entrypoint.

### The APIs

- /api/attributes

- /api/domains

- /api/measures

- /api/controls

- /api/documents

### Actions managed by the resource controller

Requests and URIs for each api are shown in the table below.

| Request | URI | Action 	
|-----------|--------------------|--------------------------------|      
| GET | /api/objects | returns the list of objects |
| GET /api/objets/{id} | returns object {id} |
| POST | /api/objects | save new object |
| PUT/PATCH | /api/objets/{id} | update object {id} |
| /api/objets/{id} | delete object {id} |


### Access rights

To access the APIs, you need to identify yourself as a Deming application user.
This user must have the "API" role. 

When authentication is successful, the API sends a "token" which must be passed in the "Authorization" header of the API request.

### Examples

Here are a few examples of how to use the API with PHP:

#### Authentication

```php
<?php
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://127.0.0.1:8000/api/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query(
            array("email" => "api@admin.com",
                  "password" => "12345678")),
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/x-www-form-urlencoded",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $info = curl_getinfo($curl);

    curl_close($curl);

    if ($err) {
        set_error_handler($err);
    } else {
        if ($info['http_code'] == 200) {
            $token = json_decode($response)->token;

        } else {
            set_error_handler("Login to api faild status 403");
            error_log($responseInfo['http_code']);
            error_log("No login api status 403");
        }
    }

    var_dump($response);
```

#### Liste des domaines

```php
<?php
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://127.0.0.1:8000/api/domains",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => null, // here you can send parameters
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "Authorization: " . "Bearer" . " " . $token . "",
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));


    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    var_dump($response);

```

#### Get a control

```php
<?php
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://127.0.0.1:8000/api/controls/1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => null, // here you can send parameters
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "Authorization: " . "Bearer" . " " . $token . "",
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    var_dump($response);
```

#### Update a control

```php
<?php
   $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://127.0.0.1:8000/api/controls/1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_POST => true,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => http_build_query(
            array(
...
            ),
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "Authorization: " . "Bearer" . " " . $token . "",
            "cache-control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    var_dump($response);
```

### Python

Voici un exemple d'utilisation de l'API en Python

```python
#!/usr/bin/python3

import requests

vheaders = {}
vheaders['accept'] = 'application/json'

print("Login")
response = requests.post("http://127.0.0.1:8000/api/login", 
    headers=vheaders, 
    data= {'email':'api@admin.localhost', 'password':'12345678'} )
print(response.status_code)

vheaders['Authorization'] = "Bearer " + response.json()['token']

print("Get domains")
response = requests.get("http://127.0.0.1:8000/api/domains", headers=vheaders)
print(response.status_code)
print(response.json())

```

### bash

Voici un exemple d'utilisation de l'API en ligne de commande avec [CURL](https://curl.se/docs/manpage.html) et [JQ](https://stedolan.github.io/jq/)
```
# valid login and password
data='{"email":"api@admin.localhost","password":"12345678"}'

# get a token after correct login
token=$(curl -s -d ${data} -H "Content-Type: application/json" http://localhost:8000/api/login | jq -r .token)

# query users and decode JSON data with JQ.
curl -s -H "Content-Type: application/json" -H "Authorization: Bearer ${token}" "http://127.0.0.1:8000/api/domains" | jq .

```

