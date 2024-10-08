# ITVB23OWS Development Pipelines starter code

This repository contains starter code for the course ITVB23OWS Development pipelines,
which is part of the HBO-ICT Software Engineering program at Hanze University of
Applied Sciences in Groningen.

This is a deliberately poor software project, containing bugs and missing features. It
is not intended as a demonstration of proper software engineering techniques.

The application contains PHP 8.3 code and should run using the built-in PHP server,
which can be started using the following command.

```
php -S localhost:8000 -t public/
```

In addition to PHP 8.3 or higher, the code requires the mysqli extension and a MySQL
or compatible server. The connection parameters are set using environment variables;
these can be configured using a `.env` file in the project root directory. An example
`.env.example` file is provided which assumes the database is running on localhost, has
a root user using the password `password` and a database schema named `hive`. This
file can be renamed to `.env` and modified to match the desired connection parameters.
A file `hive.sql` is also provided which contains the database schema.

This application is licensed under the MIT license, see `LICENSE.md`. Questions
and comments can be directed to
[Ralf van den Broek](https://github.com/ralfvandenbroek).