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

## Choose your track
This challenge flexes around the kind of work you thrive on. Feel free to lean into one (or combine them) as you shape your solution:
- **Frontend / Livewire mindset** – Focus on the interaction model, component architecture and how the experience feels to a reader. It's ok to simulate latency or canned responses—just call out what a production API would return.
- **Fullstack backend mindset** – Dive deep into data modeling, indexing and APIs that make search accurate, resilient and observable. A minimal UI (or even a documented Postman collection) is fine if your endpoints and tests tell the story.
- **Mobile TypeScript mindset** – Explore how a mobile or cross-platform client would surface search, share UI state and ship to devices (React Native, Expo, Electron or plain web are all welcome). Mocking the backend with fixtures or a tiny local server is totally fine—just let us know what the real integration would look like and where to find the client (e.g. `apps/mobile`).

## Exercise

1. **Hands on**:
Develop a feature that would allow a user to search inside a book, displaying a list of matches while showing snippets and information about where in the book the match was found.
    - The user should then be able to choose a particular match and retrieve its whole page.
    - You can find the book in `./storage/exercise-files/Eloquent_JavaScript.pdf` and, for convenience, there's also a `Eloquent_JavaScript.json` file with the text content of each page.
    - There's a demo of this kind of functionality [here](https://alephdigital.publica.la/reader/eloquent-javascript).
    - You can use any tool, strategy, library or template for any part of your solution. Keep in mind you're now a member of the team, so go ahead and ask for feedback if you need it.
    - The solution has to be sent as a Merge Request before the presentation. It doesn't need to be hosted online, but it does need to be fully working on your local environment.
    - The solution will be presented online during the final interview.

This feature may be developed in 1h, 1 day, 1 week or take months worth of work. Take this as an opportunity to cut scope, get creative and focus on your strengths. You may even fake parts of the feature and focus on what matters to you.
We're not looking for a "perfect solution", we want to understand your skills and see where your expertise guides you.

For this part of the exercise, you are encouraged to use any AI tool you are comfortable with. Go ahead and take advantage of them!
We'll want to know what tools you used, how and why you used them in those specific ways.

Pick the angle that feels closest to the work you love doing:
- **Frontend / Livewire mindset** – Prioritize user flows, interactions and how the experience comes together. Livewire, Blade, and reactive UI patterns are fair game—note any mocked behaviours so we understand the constraints you assumed.
- **Fullstack backend mindset** – Lean into the search domain, data modeling, API design and how you validate relevance, performance or resilience. It's fine to keep the UI thin if you supply API docs, tests or sample clients.
- **Mobile TypeScript mindset** – Show how a mobile or cross-platform client would consume the search experience. React Native, Expo, Electron or web-first stacks are welcome, and faking the API with static data is fair game if you document the intended contract and how to run the client.

You're free to mix approaches—just tell us what you chose and why.

### Deliverables
When you open your Merge Request, include:
- A short note about which track(s) you leaned into and why.
- Clear run instructions for anything outside the default Laravel app (e.g. Expo commands, npm scripts, Postman collections).
- Tests, scripts or manual steps we should run to validate your work (even if they're as simple as “`./vendor/bin/sail artisan test`” or “`yarn build`”).
- Any assumptions, trade-offs or mocked pieces we should keep in mind while reviewing.

2. **Think big**:
Imagine you have 2 to 3 entire months to develop the solution, would you take a different approach? Think about how to increase the relevance of the results, scalability, performance, content security or maintainability. Describe it in as many details as you see fit.
If you followed a specific focus in the **Hands on** stage, outline how that stream would evolve:
- Frontend folks: How would you harden component architecture, UX testing, accessibility, or Livewire/server-driven UI patterns at scale? What would you need from backend or mobile peers to make it happen?
- Backend folks: What would the roadmap look like for better ranking, async pipelines, observability, or multi-tenant considerations? How would you partner with frontend/mobile teams to evolve the contracts?
- Mobile folks: How would you evolve the client experience, offline support, packaging and distribution, or shared TypeScript domain logic? What expectations would you set for backend capabilities as the app grows?
    - The solution has to be presented in written form, a markdown file in the repo is more than enough. You may include a diagram or anything you see useful.

3. **Presentation**:
After you finish, we'll have a call where you present your solution for point **1** and your plan for point **2**. We'll be very interested in every little detail, complication or blocker you had, compromises you made, how you would improve what you've done, if you found something interesting, if you are particularly happy with something in the solution, etc.

## What's included
This repository contains a blank [Laravel](https://laravel.com) project, to be used as a container for the exercise solution. To run it, you'll need:
- PHP 8.3+ and [Composer](https://getcomposer.org/) installed locally. We recommend using [php.new](http://php.new/) to install both in a single step if you don't already have them.
- [Docker](https://www.docker.com/products/docker-desktop)

The project uses [Sail](https://laravel.com/docs/12.x/sail), a simple and easy to use Docker based Laravel development environment.

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
You have more detailed info [here](https://laravel.com/docs/12.x/sail)
After starting the environment the project is accessible from `http://localhost` (update the port in `.env` if you need 8888 or another value).
You can access the included PostgreSQL database from outside the container using `127.0.0.1:5432` with username `publicala_user` and password `publicala_password`.
The database itself is called `publicala_db`. For example, in TablePlus you may use [this config](PostgreSQL_config_example.png).

## Take into account:
- Focus on the **search feature** itself, there's no need to design and implement a fancy UI as long as it's clear and easy to use. Feel free to use a template or any UI library.
- There are many correct ways of solving this exercise. Don't stress too much about implementing the perfect one, that's where the **Think big** part comes in.
- You'll be given access to a Slack channel, where you may ask any question or share ideas in order to complete the exercise. We'll be waiting for you, come say hello!
- Create a Merge Request in this repo with your own solution.
