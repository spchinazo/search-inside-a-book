## Goals
The ultimate goal of this exercise is to simulate the day-to-day working environment at publica.la, as in during the exercise you'll be a member of the team.

We will evaluate:
- Team interaction
- User experience of the developed solution
- Attention to detail
- Time management and self leadership
- Coding skills, clarity
- Creativity
- Ability to understand the context and evaluate trade-offs

## Scenario
As a programmer, I remember reading about "the DOM" in a book and I want to be able to search inside the book so that I can clarify some doubts.

## Exercise

1. **Hands on**:  
Develop a feature that would allow a user to search inside a book, displaying a list of matches while showing snippets and information about where in the book the match was found.
    - The user should then be able to choose a particular match and retrieve it's whole page.
    - You can find the book in `./storage/exercise-files/Eloquent_JavaScript.pdf` and, for convenience, there's also a `Eloquent_JavaScript.json` file with the text content of each page.
    - There's a demo of this kind of functionality [here](https://alephdigital.publica.la/reader/eloquent-javascript).
    - You can use any tool, strategy, library or template for any part of your solution. Keep in mind you're now a member of the team, so go ahead and ask for feedback if you need it.
    - The solution has to be sent as a Merge Request before the presentation. It doesn't need to be hosted.
    - The solution will be presented online during another interview.

2. **Think big**:  
Imagine you have 2 to 3 entire months to develop the solution, would you take a different approach? Think about how to increase the relevance of the results, scalability, performance and content security. Describe it in as many details as you see fit.
    - The solution has to be presented in written form, a markdown file in the repo is more than enough. You may include a diagram or anything you see useful.

3. **Presentation**:  
After you finish, we'll have a call where you present your solution for point **1** and your plan for point **2**. We'll be very interested in every little detail, complication or blocker you had, compromises you made, how you would improve what you've done, if you found something interesting, if you are particularly happy with something in the solution, etc.

## What's included
This repository contains a blank [Laravel](https://laravel.com) project, to be used as a container for the exercise solution. To run it, you'll need:
- PHP 8.3+ and [Composer](https://getcomposer.org/) installed locally. We recommend using [php.new](http://php.new/) to install both in a single step if you don't already have them.
- [Docker](https://www.docker.com/products/docker-desktop)

The project uses [Sail](https://laravel.com/docs/9.x/sail), a simple and easy to use Docker based Laravel development environment.

#### Step by step instructions:
1. Clone the repo `git clone git@gitlab.com:publicala_exercises/search-inside-a-book.git` and `cd search-inside-a-book`
2. Copy the .env.example file into .env, `cp .env.example .env`
3. Install PHP dependencies: `composer install`
4. Start the Docker environment: `./vendor/bin/sail up -d` (the first time it'll take a while, as it has to download the container images)
5. Copy your environment file if you haven’t yet and generate the key: `./vendor/bin/sail artisan key:generate`
6. Install JavaScript dependencies inside Sail: `./vendor/bin/sail yarn install`
7. Start the dev asset server: `./vendor/bin/sail yarn dev` (for production builds use `./vendor/bin/sail yarn build`)
8. Run migrations or other setup when needed (none are shipped by default): `./vendor/bin/sail artisan migrate`
9. Create the storage symlink if your solution needs it: `./vendor/bin/sail artisan storage:link`
10. Access the project from `http://localhost` (or the port you configure in `.env`)

\* Regarding Sail:  
To start or stop the environment use `./vendor/bin/sail up` and `./vendor/bin/sail down`.  
You have more detailed info [here](https://laravel.com/docs/9.x/sail) 
After starting the environment the project is accessible from `http://localhost` (update the port in `.env` if you need 8888 or another value).  
You can access the included PostgreSQL database from outside the container using `127.0.0.1:5432` with username `publicala_user` and password `publicala_password`.   
The database itself is called `publicala_db`. For example, in TablePlus you may use [this config](PostgreSQL_config_example.png).

## Take into account:
- Focus on the **search feature** itself, there's no need to design and implement a fancy UI as long as it's clear and easy to use. Feel free to use a template or any UI library.
- There are many correct ways of solving this exercise. Don't stress too much about implementing the perfect one, that's where the **Think big** part comes in.
- You'll be given access to a Slack channel, where you may ask any question or share ideas in order to complete the exercise. We'll be waiting for you, come say hello!
- The exercise is designed to take approximately 4 to 6 days, but it ultimately depends on the solution itself.
- Create a Merge Request in this repo with your own solution.
