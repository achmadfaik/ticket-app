# TICKET APP
> PHP CLI and API
### Requirement
    php 7.4 >
    MySql Database
    composer

### Database Configuration
    change databas config here:

    database/Connection/Config.php

### Installation & Database Migration
    run "composer dumpautoload"
    run "php migrate-and-seed.php"
    run "php -S localhost:8000"

### Usage CLI Example

> php generate-ticket.php 2 10000

### API Usage Example

#### Get Tickets
    GET /tickets HTTP/1.1

#### Params
    event_id
    ticket_code
    page
    per_page

#### Response
```json
Status 200
Content-Type: application/json
[
  {
  "event_id": 1,
  "ticket_code": "DTK48A086A",
  "status": "claimed"
  },
  {
  "event_id": 1,
  "ticket_code": "DTK2F09384",
  "status": "available"
  },
  {
  "event_id": 1,
  "ticket_code": "DTK97C895C",
  "status": "available"
  }
]
```

#### Update Tickets
    Post /tickets-update HTTP/1.1

#### Body
```json
{
"event_id": "1",
"ticket_code": "DTK48A086A",
"status": "claimed"
}
```

#### Response
```json
Status 200
Content-Type: application/json
{
"event_id": "1",
"ticket_code": "DTK48A086A",
"status": "claimed"
}
```

    postman collection located at root postman forlder

### Version History

* 1.0
    * Version 1.0 

### Meta

Achmad Faik Farqui  –
achmadfaik.alfaruqi@gmail.com
