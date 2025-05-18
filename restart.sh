#!/bin/bash

spinner() {
    local pid=$!
    local delay=0.1
    local spinstr='|/-\'
    while ps -p $pid > /dev/null 2>&1; do
        local temp=${spinstr#?}
        printf " [%c]  " "$spinstr"
        local spinstr=$temp${spinstr%"$temp"}
        sleep $delay
        printf "\b\b\b\b\b\b"
    done
}

echo -n "Deleting active containers..."
(docker-compose down) & spinner
echo " Done."

echo "Rebuilding containers..."
(docker-compose up --build -d) & spinner
echo " Done."

echo "Listing containers..."
sleep 5
docker ps -a
