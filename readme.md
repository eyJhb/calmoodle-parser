# Parser for calmoodle
This is a parser for Calmoodle, to enable students at AAU, to get automatic updating calendars from Calmoodle into e.g. Google Calendar.

There are links that can be used from Moodle itself, but they are unreliable..

Feel free to come with new features etc!
This is currently designed to be easy to use..

# Running dev
Just run `./run.sh`, and it should spin up a server.
You might want to get a shell for the Docker instance, which can be done using...

```
# find the docker instance id
docker ps 
docker exec -it <instance-id> sh
```
