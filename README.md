# PHP-Currency
## Endpoints
default localhost/project/public
- /entry
- /entry/new
- /entry/{id}
- /entry/{id}/edit

import db - currency.sql

or use doctrine

-change yours .env file
-DATABASE_URL=mysql://root:@127.0.0.1:3306/currency
-php bin/console doctrine:migrations:migrate

