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

# Options
The calendar supports the following GET options.

```
sid: the id of the calendar
name: display name when imported
startDate: Y-m-d (startdate of events)
endDate: Y-m-d (enddate of events)
output: ical or json (what to output - default ical)
```

If output using `json`, `sha1` field is included for each event.
This can be used to validate a local cache.
