#!/bin/bash
docker run --rm -it -v $(pwd)/:/src -p 8080:8080 calmoodle php7 -S 0.0.0.0:8080 -t /src/src -d display_errors=on -d error_reporting=-1 /src/src/router.php
