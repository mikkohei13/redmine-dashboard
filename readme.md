
Simple dashboard that shows issues from a Redmine instance, using the Redmine JSON API.

Setup:

1) Clone this repository
1) Add your Redmine API key and base URL to the docker-compose-TEMPLATE.yml file
1) Rename the file to docker-compose.yml
1) `make setup`
1) `docker-compose up -d`

Access the service, setting project id number and users id number and names as get-parameters:

    http://example.com:85/?projectnumber={project_number}&users={user_number}:{user_name}/{user_number}:{user_name}

Todo
====

- Testing...
- `make setup` to change the docker-compose.yml filename