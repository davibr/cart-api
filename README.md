# cart-api

Setting up the environment:

1) After cloning the repository, use composer to install the dependencies
2) Utilize Laravel Sail to setup the environment using ./vendor/bin/sail up (use the parameter -d if you prefer to do this on background)
3) Use the command ./vendor/bin/sail artisan migrate to create the tables needed in the database
4) Use the command ./vendor/bin/sail artisan db:seed to create the test user needed for our application
5) Create an .env file based on the .env.example and fill the PRODUCTS_API_KEY with a valid key for the external API used ( https://rapidapi.com/apidojo/api/walmart )

- The diagrams are in the diagram folder, the postman and openapi files on the root folder
