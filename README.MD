## Project description
The project is used to fetch the data of RewardGateway API and render it in Linkedin style. It's handling the different API errors by showing message for data extraction error.<br>
There is also logic for fixing missing images and clearing HTML tags (most important javascript) in the bio. For the images the logic is inside the Twig because of the additional request needed in the PHP to check if the image exists. It's using 2<sup>nd</sup> background image. I also left commented another solution with img tag and onerror event, but it seems to be deprecated.<br>
Initially the plan was to load the API data with AJAX request so there is 2<sup>nd</sup> route and logic for this but the API data changes each time, so I decided to go with the basic PHP/Twig rendering. 

## Used technologies
- Symfony 5.3
- PHP 7.2
- Bootstrap 5.1
- jQuery 3.6

## Instalation
- Clone the project
- Create .env.local file and add the DATABASE_URL parameter with correct DB setup
- composer install
- php bin/console server:run
- Load http://127.0.0.1:8000/