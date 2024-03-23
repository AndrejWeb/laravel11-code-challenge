<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Laravel 11 Code Challenge

This is a code challenge that I solved during one of the tech interviews. I think it's an interesting one so I'm sharing it with you. If you are a company you can test your candidates with this task and hope they don't find this repository. :)

#### Code Challenge Requirements
1. Use PHP 8.2+,  Laravel 10 or 11 + Sail (Postgres as database)
2. Use sanctum authentication
3. Use backend as API (inertia can not be used)
4. Products seeder which creates 20 random products (price is random, but between 3 and 5 euros)
5. All the necessary order info is stored in database (including order total)
6. Upon order creation, validate that minimum order amount has to be at least 15 eur.
7. Order is created with status "pending" initially
8. Create a ProcessOrder job that dispatches an email to the admin (address stored in config) and changes the order status to processed. Email content does not matter, can be just a plain text. Dispatch the job with 3 min delay after order creation.

#### Solution
You will find all the necessary routes in routes/api.php. From there you can follow the code.

1. Rename .env.example to .env
2. Edit ADMIN_EMAIl in .env and set it to your email address. MAIL_MAILER=log so you will see the email content in storage/logs/laravel.log. If you want you can send an actual email.
3. Run `composer install`
4. Run `./vendor/bin/sail up`
5. If necessary edit Postgres credentials in .env
6. Create Postgres database
7. Enter sail bash via `./vendor/bin/sail bash` and run `php artisan migrate` and then `php artisan db:seed`
8. You are good to go

You can use Postman or other API tool to make API requests. Use route /api/register to register a user and obtain access token. Fields email and password are mandatory, field name is optional. Use /api/login to log in a user and get a new access token. /api/logout logs out the user and removes all access tokens. /api/orders is the endpoint for creating an order. Check out screenshots to see the JSON format of the data you need to send to each route.

If you find it difficult to run the project with Sail due to apache2 running, docker or whatever you can do the project in regular Laravel project installed via composer. However, the requirements were to be done with Sail hence Sail was used.

#### Screenshots

Register user
<img src="https://i.imgur.com/7hMOT6v.jpeg" />

Table products
<img src="https://i.imgur.com/9WfeEGn.jpeg" />

Set the necessary headers before sending requests to /api/orders
<img src="https://i.imgur.com/1ikx1Qp.jpeg" />

If order is less than 15 euros
<img src="https://i.imgur.com/U4yygiC.jpeg" />

Successful order
<img src="https://i.imgur.com/MinUwXY.jpeg" />

Order status is pending upon creation
<img src="https://i.imgur.com/hAqlEby.jpeg" />

Table order_product
<img src="https://i.imgur.com/SLEvFgb.jpeg" />

Job created after order creation 
<img src="https://i.imgur.com/XpoHFAV.jpeg" />

Inside sail bash run `php artisan queue:work` to execute the job. Check storage/logs/laravel.log to see the email assuming you use MAIL_MAILER=log in .env
<img src="https://i.imgur.com/HStqGr0.jpeg" />

The order's status after job execution is set to processed
<img src="https://i.imgur.com/MU1EVph.jpeg" />
