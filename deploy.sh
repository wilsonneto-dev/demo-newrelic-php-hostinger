#!/bin/bash

# this is not a good practice for deployment, it will cause downtime
# this deployment process if just for the sake of the demo, as it is focused on New Relic and Hostinger

# Navigate to the target directory
cd domains/taskpulse.tech/ || exit

# Remove all contents in the current directory
rm -rf *

# Clone the GitHub repository
git clone https://github.com/wilsonneto-dev/demo-newrelic-php-hostinger

# Move the contents of the cloned repository to the current directory
mv ./demo-newrelic-php-hostinger/* ./
mv ./demo-newrelic-php-hostinger/.* ./

# Remove the cloned repository directory
rm -rf ./demo-newrelic-php-hostinger/

# Move the environment file to the current directory
mv ../../demo-twitter.env ./.env

# List the contents to verify the deployment
ls -la
