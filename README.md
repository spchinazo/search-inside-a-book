## Goals
The ultimate goal of this exercise is to simulate the day to day working environment at publica.la, as in during the exercise you'll be a member of the team.

We will evaluate:
- Team interaction
- User experience of the developed solution
- Attention to detail
- Time management and self leadership
- Coding skills, clarity
- Creativity
- Ability to understand the context and evaluate trade offs

## Scenario
As a programmer, I remember reading about "the DOM" in a book and I want to be able to search inside the book so that I can clarify some doubts.

## Exercise

1. **Hands on**:  
Develop a feature that would allow a user to search inside a book, displaying a list of matches while showing snippets and information about where in the book the match was found.
    - The user should then be able to choose a particular match and retrieve it's whole page.
    - You can find the book in `./storage/exercise-files/Eloquent_JavaScript.pdf` and, for convenience, there's also a `Eloquent_JavaScript.json` file with the text content of each page.
    - There's a demo of this kind of functionality [here](https://alephdigital.publica.la/reader/eloquent-javascript).
    - You can use any tool or strategy, for any part of your solution. Keep in mind you're now a member of the team, so go ahead and ask for feedback if you need it.
    - The solution has to be presented hosted online so that it can be tried before the presentation. It should also be kept there and working for up to 15 days after the presentation.

2. **Think big**:  
Imagine you have 2 to 3 entire months to develop the solution, would you take a different approach? Think about how to increase the relevance of the results, scalability, performance and content security. Describe it in as many details as you see fit.
    - The solution has to be presented in written form, a markdown file in the repo is more than enough. You may include a diagram or anything you see useful.

3. **Presentation**:  
After you finish, we'll have a call where you present your solution for point **1** and your plan for point **2**. We'll be very interested in every little detail, complication or blocker you had, compromises you made, how you would improve what you've done, if you found something interesting, if you are particularly happy with something in the solution, etc.

## What's included
This repository contains a blank [Laravel](https://laravel.com) project, to be used as a container for the exercise solution. To run it, you'll need PHP 8.1, [Composer](https://getcomposer.org/) and [Docker](https://www.docker.com/products/docker-desktop) installed.
The project uses [Sail](https://laravel.com/docs/9.x/sail), a simple and easy to use Docker based Laravel development environment.

#### Step by step instructions:
1. Clone the repo `git clone git@gitlab.com:publicala_exercises/search-inside-a-book.git` and `cd exercise`
2. Copy the .env.example file into .env, `mv .env.example .env`
3. Run `composer install`
4. `./vendor/bin/sail up`, keep in mind that the first time it'll take a while, as it has to download the containers images
5. That's it, the project is now accessible from `https://127.0.0.1`

\* Regarding Vessel:  
To start or stop the environment use `./vendor/bin/sail up` and `./vendor/bin/sail down`.  
After starting the environment the project is accessible from `https://127.0.0.1`.  
You can access the included PostgreSQL database from outside the container using `127.0.0.1:5432` with username `publicala_user` and password `publicala_password`. The database itself is called `publicala_db`. For example, in TablePlus you may use [this config](PostgreSQL_config_example.png).

## Take into account:
- You'll be given access to a Slack channel, where you may ask any question or share ideas in order to complete the exercise. We'll be waiting for you, come say hello!
- The exercise is designed to take approximately 4 to 6 days, but it ultimately depends on the solution itself.
- There are many correct ways of solving this exercise. Don't stress too much about implementing the perfect one, that's where the **Think big** part comes in.
- There's no need to design and implement a fancy UI, but do keep in mind that the solution has to be easy to use and slightly visually appealing. Feel free to use a template or any UI library.
- Clone this repository and develop the solution in a private one, then give us access to that repository so that we can follow along. Do not push your branch to this repo.
