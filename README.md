1. Create .env file 

```bash
php -r "file_exists('.env') || copy('.env.example', '.env');"
```

2. Install packages

```bash
composer install
```
```bash
npm install
```

3. Generate APP_KEY

```bash
php artisan key:generate
```

4. Fill these fields in .env file to connect your DB 

DB_CONNECTION=mysql<br>
DB_HOST=127.0.0.1<br>
DB_PORT=3306<br>
DB_DATABASE=<br>
DB_USERNAME=<br>
DB_PASSWORD=<br>

5. Run migrations and seeders to fill DB with tables

```bash
php artisan migrate --seed
```


6. Now run the following command to install shield:

```bash
php artisan shield:install
```
Choose yes to create super admin user

7. Run project

```bash
php artisan serve
```