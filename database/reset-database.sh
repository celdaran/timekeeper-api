#!/bin/bash

read -p "This will reset the database, destroying all data. Continue? (y/N) " -r

if [[ $REPLY =~ ^[Yy]$ ]]
then
    # Run the SQL to drop and rebuild database from scratch
    echo "Resetting the database..."
    time mysql < reset-database.sql

    # Generate sysadmin user on the fly
    echo "Generating sysadmin user"
    cd ..
    bin/console app:create-sysadmin

    # Additional instructions
    echo "If this is a development environment, you'll want to create additional testing accounts."
    echo "In the testing directory are PhpStorm .http and .json files. Here's what you need to do next:"
    echo "1. Run login1.http (either through the PhpStorm IDE or the ijhttp CLI command (requires Java runtime))"
    echo "2. Update http-client.private.env.json's tokenAccount1 value with token returned from login1.http"
    echo "3. Run account-create2.http, account-create3.http, account-create4.http"
    echo "4. Optionally run login2.http, login3.http, login4.http to get additional tokens"
    echo "5. Optionally copy those tokens into http-client.private.env.json"
else
    echo "Database not reset. No changes made..."
fi
