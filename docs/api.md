## /users ##

### `GET` /users.{_format} ###

_List all users from the database. Does not support paging!_

List all users from the database. Does not support paging!

#### Requirements ####

**_format**

  - Requirement: json


### `POST` /users.{_format} ###

_Creates a new user from the submitted JSON data._

Creates a new user from the submitted JSON data.

#### Requirements ####

**_format**

  - Requirement: json

#### Parameters ####

id:

  * type: integer
  * required: false

first_name:

  * type: string
  * required: false

last_name:

  * type: string
  * required: false

login_name:

  * type: string
  * required: false

time_stamp:

  * type: DateTime
  * required: false

reference:

  * type: string
  * required: false


## /users/{id} ##

### `GET` /users/{id}.{_format} ###

_Retrieve a single user from database by user ID._

Retrieve a single user from database by user ID.

#### Requirements ####

**id**

  - Requirement: \d+
  - Type: integer
  - Description: UserID
**_format**

  - Requirement: json

#### Response ####

id:

  * type: integer

first_name:

  * type: string

last_name:

  * type: string

login_name:

  * type: string

time_stamp:

  * type: DateTime

reference:

  * type: string


### `PUT` /users/{id}.{_format} ###

_Update a user. Make sure to include all properties!_

#### Requirements ####

**id**

  - Requirement: \d+
  - Type: integer
  - Description: UserID
**_format**

  - Requirement: json

#### Response ####

id:

  * type: integer

first_name:

  * type: string

last_name:

  * type: string

login_name:

  * type: string

time_stamp:

  * type: DateTime

reference:

  * type: string


### `DELETE` /users/{id}.{_format} ###

_Delete user from the database by user ID._

Delete user from the database by user ID.

#### Requirements ####

**id**

  - Requirement: \d+
  - Type: integer
  - Description: UserID
**_format**

  - Requirement: json
