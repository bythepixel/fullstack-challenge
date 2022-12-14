# Fullstack Challenge

## Instructions
Using Laravel and VueJS, create an application which shows the weather for a set of users.
- Create a fork of this repository. Once completed, send link to interviewer.
- Update landing page to show a list of users and their current weather.
- Clicking a user row opens a modal or screen which shows that users detailed weather report.
- Weather update should be no older than 1 hour.
- Internal API request(s) to retrieve weather data should take no longer than 500ms.
- We are looking for attention to detail!
- Instructions are purposely left somewhat open-ended to allow the developer to make some of their own decisions on implementation and design. To note, this is not a designer test so this does not have to look "good".  

## Things to consider:
- Chose your own weather api such as https://openweathermap.org/api, https://www.weather.gov/documentation/services-web-api etc
- Testability
- Best practices
- Design patterns
- Availability of external APIs is not guaranteed and should not cause page to crash
- Twenty randomized users are added via the seeder process, each having their own unique location (longitude and latitude)
- Anything else you want to do to show off your coding chops!

## To run the local dev environment:

### API
- Ensure php 8.1 and the latest version docker installed is active on host
- Install php dependencies: `composer install`
- Setup app key: `php artisan key:generate`
- Migrate database: `php artisan migrate` 
- Seed database: `php artisan db:seed`
- Start docker containers `docker compose up`
- Visit api: `http://localhost`

### Frontend
- Ensure nodejs v18 is active on host
- Install javascript dependencies: `npm install`
- Run frontend: `npm run dev`
- Visit frontend: `http://localhost:5173`
