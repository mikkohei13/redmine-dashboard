
Simple dashboard that shows issues from a Redmine instance, using the Redmine JSON API.

Setup:

1) Clone this repository
1) Add your Redmine API key and base URL to the docker-compose-TEMPLATE.yml file
1) Rename the file to docker-compose.yml
1) `make setup`
1) `docker-compose up -d`

This pulls an image from Docker Hub and runs it. If you want to build the image locally, do `docker build` and `docker run` with parameters of your choice. 

Then access the service, setting project number & user numbers and names as GET parameters:

    http://example.com:85/?projectnumber={project_number}&users={user_number}:{user_name}/{user_number}:{user_name}

To see also unassigned issues, add user to the GET parameters:

    none:Unassigned

To see also testable issues, add to the GET parameters:

   &showtestable

Todo
====

- Testing...
- @todo items on code
- refactoring
- `make setup` to change the docker-compose.yml filename