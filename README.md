# Fullstack Challenge

## Goal
Using Laravel and Vue.js, create an application which shows the weather for a set of users to demonstrate your coding chops.

## Acceptance Criteria
Instructions are purposely left somewhat open-ended to allow the developer to make some of their own decisions on implementation and design. We have provided some initial scaffolding structure/examples, however feel free to make it your own and remove anything unnecessary.

1. Fork this repo and work off of your forked version.
2. Chose your own weather API, such as:
   - https://openweathermap.org/api
   - https://www.weather.gov/documentation/services-web-api
3. Show a list of users and their current weather.
   - Use the twenty randomized users generated from the seeder process, each having their own unique location (longitude and latitude).
   - The current weather conditions shown here should be no older than 1 hour.
4. Clicking a user should open a modal or screen, which shows that user's detailed weather report.
   - The current weather conditions shown here should be no older than 1 hour.
5. Internal API request(s) to retrieve weather data should take no longer than 500ms.
   - Consider that external APIs could and will take longer than this from time to time and should be accounted for.
6. The availability of external APIs is not guaranteed and should not cause the page to crash.

As you complete this challenge, please add comments or TODOs in your code to highlight any improvements or additional functionality you would have implemented if you had more time. This could include things like performance optimizations, additional test coverage, refactoring opportunities, or feature enhancements.

This helps us understand your thought process, prioritization, and awareness of best practices.

Once completed:
1. Open a PR to merge the branch you did your work on into the `main` branch so our team can provide code review comments.
2. Send a link of your repository to the interviewer and let them know how long the exercise took.

## Things to consider
- Redis is available (Docker service) if you wish to use it.
- Queues, workers, websockets could be useful.
- Feel free to use a frontend UI library such as PrimeVue, Vuetify, Bootstrap, Tailwind, etc. 
- Include anything else you desire to show off your coding chops!

## What we are looking for
- Attention to detail
- Testability
- Best practices
- Design patterns
- This is not a designer test so the frontend does not have to look "good," but of course bonus points if you can make it look appealing.

## To run the local dev environment

### API
- Navigate to `/api` folder
- Ensure version docker installed is active on host
- Copy .env.example: `cp .env.example .env`
- Start docker containers `docker compose up` (add `-d` to run detached)
- Connect to container to run commands: `docker exec -it fullstack-challenge-app-1 bash`
  - Make sure you are in the `/var/www/html` path
  - Install php dependencies: `composer install`
  - Setup app key: `php artisan key:generate`
  - Migrate database: `php artisan migrate` 
  - Seed database: `php artisan db:seed`
  - Run tests: `php artisan test`
- Visit api: `http://localhost`

### Frontend
- Navigate to `/frontend` folder
- Ensure nodejs v18 is active on host
- Install javascript dependencies: `npm install`
- Run frontend: `npm run dev`
- Visit frontend: `http://localhost:5173`
