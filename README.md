WP API + WP-CLI
===============

Interact with a WordPress site remotely using [WP API][] and [WP-CLI][].

[WP API]: https://github.com/WP-API/WP-API
[WP-CLI]: http://wp-cli.org/

## Requirements
On the server:
* [WP API][] - Version 1.0 or newer
* [OAuth 1.0a Server][] - Latest development version
* [WP-CLI][] (for initial setup)

On the client:
* [WP-CLI][]
* This repository!

[OAuth 1.0a Server]: https://github.com/WP-API/OAuth1

## Setting Up
### Step 1: Creating a Consumer
Once you have WP API and the OAuth server plugins activated on your server,
you'll need to create a "consumer". This is an identifier for the application,
and includes a "key" and "secret", both needed to link to your site.

To create the consumer, run the following **on your server**:
```bash
$ wp oauth1 add

# ID: 4
# Key: sDc51JgH2mFu
# Secret: LnUdIsyhPFnURkatekRIAUfYV7nmP4iF3AVxkS5PRHPXxgOW
```

Note the key and secret returned here. You'll need those in a moment.

### Step 2: Linking the Client
Time to link the client to your site. These should always be run from the same
directory this readme is in, unless you have the client installed globally.

Replace `http://example.com/` with the site you're running WP API on. You can
specify any URL on your site and the API will be automatically discovered from
there. However, you should try and keep this URL the same across all commands,
as it will help WP CLI run faster.

To link the client, run the following **on the client**:
```bash
$ wp --require=client.php api oauth1 connect http://example.com/ --key=sDc51JgH2mFu --secret=LnUdIsyhPFnURkatekRIAUfYV7nmP4iF3AVxkS5PRHPXxgOW
Open in your browser: http://example.com/oauth1/authorize?oauth_token=xCvteGTWqgYjPdQrCU1bXDv9
Enter the verification code:
```

Open this up in your browser, log in if needed, and authorize the account.
You'll then see an authorization code:
![Authorization code](https://www.dropbox.com/s/nj5b4kixzpj1wwp/Screenshot%202014-05-27%2000.00.10.png)

Copy and paste this into your terminal, hit enter and you're good to go:
```bash
$ wp --require=client.php api oauth1 connect http://example.com/ --key=sDc51JgH2mFu --secret=LnUdIsyhPFnURkatekRIAUfYV7nmP4iF3AVxkS5PRHPXxgOW
Open in your browser: http://example.com/oauth1/authorize?oauth_token=xCvteGTWqgYjPdQrCU1bXDv9
Enter the verification code: BEBMyxKTEKOXCsNqS9Q0r8pC
Authorized!
Key: kJPiCdhE8kIcIUCFGta1oNLE
Secret: FzcSbdsIC0Amuw2ZQuaAQCxVUxQ9X5qytWBbXQF7QzUADnr0
```

Your client is now linked to your site and account! The key and secret will be
displayed if you need them, however WP-CLI will remember your credentials
automatically.

### Step 3: Doing the Fun Stuff
Time to mess around with the CLI commands! All commands start with `wp api`, and
take the site you want to interact with as the first parameter.

```bash
$ wp --require=client.php api user list http://example.com/
+----+----------+----------+-------------------+---------------+
| ID | username | name     | email             | roles         |
+----+----------+----------+-------------------+---------------+
| 1  | admin    | admin    | admin@example.com | administrator |
| 2  | testuser | testuser | test@example.com  | subscriber    |
+----+----------+----------+-------------------+---------------+

$ wp --require=client.php api user get-current http://example.com/
+----+----------+-------+-------------------+---------------+
| ID | username | name  | email             | roles         |
+----+----------+-------+-------------------+---------------+
| 1  | admin    | admin | admin@example.com | administrator |
+----+----------+-------+-------------------+---------------+

```