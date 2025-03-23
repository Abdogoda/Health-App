# Teradevelopers

We Are Health App !

## Setting Up local development

1. Clone the repository by running `git clone https://github.com/{your backend system repo url}`
2. Go to the project directory by running `cd your-project-directory-name`
3. RUN `cp .env.example .env`
4. Setup email configurations, and api key in the `.env` file
5. RUN `php artisan key:generate`
6. Run `php artisan migrate` to run the migrations
7. Run `php artisan db:seed` to seed the database data
8. Run `php artisan storage:link` to create a symbolic link to the storage folder
9. Server should be running on `http://localhost:8000`
10. ... enjoy!


## Work with schedule

- To work with the default schedule (for example: for authentication mails, progress mail) you have to run `php artisan schedule:work`

## Issue an api key from https://spoonacular.com/

- Sign Up / Log In
- Access the API Section, navigate to the Spoonacular API page by visiting: https://spoonacular.com/food-api.
- Click on "Pricing" to review available plans (Free & Paid options). Select a plan that suits your needs.
- Get Your API Key. After subscribing, go to your Profile Dashboard or API section. Copy the key to use it in your applications.
- Past the API Key to .env variable:
  ```php 
    SPOONACULAR_API_KEY=
  ```

## Use Google Email to send emails 

1. Enable 2-Step Verification

    - Go to Google My Account.
    - Under the "Signing in to Google" section, enable 2-Step Verification if not already enabled.

2. Generate an App Password
    - In the same security settings, find "App passwords" (it appears after enabling 2-Step Verification).
    - Select "Mail" as the app and "Other (Custom)" as the device.
    - Enter a name (e.g., "Laravel Mail") and generate the password.
    - Copy the generated password (it’s a 16-character string).

3. Configure Laravel .env File
    - Open your Laravel project and update the .env file with the following settings:
    ```php 
      MAIL_MAILER=smtp
      MAIL_HOST=smtp.gmail.com
      MAIL_PORT=587
      MAIL_USERNAME=your-email@gmail.com
      MAIL_PASSWORD=your-app-password  # Use the generated App Password
      MAIL_ENCRYPTION=tls
      MAIL_FROM_ADDRESS=your-email@gmail.com
      MAIL_FROM_NAME="Your Name"
    ```
