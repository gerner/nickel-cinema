Nickel Cinema TODO
==================

Done
----

* Register nickelcinema.*
 
TODO
----

* movie store (with showtimes?)
* user login
* experience around auth external services (netflix, twitter, facebook)
* basic UI
 
Biz
---

* ticket sales
* formal way of getting showtimes
* ticket discounts
 
Data Model
----------

### Per-User External Services
* Netflix auth info
* Twitter auth info
* Facebook auth info

### Per-User Internal stuff
* visible user name
* email (login)
* password (salted, hashed)
* points

### Friends
relationship between users

### Movies
* include Netflix movie id and name (lazy fetch these)

### Badges
* list of badges and how to get them (data driven!)
* badges acquired per user

