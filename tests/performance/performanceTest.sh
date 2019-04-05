#!/usr/bin/env bash

# Test the api with concurrent connections, backgrounding each process so that it can test 2 endpoints at the same time

ab -n 10 -c 2 http://melbourne.sma.com.au/stagearea/api > test1.txt &
ab -n 10 -c 2 http://melbourne.sma.com.au/stagearea/api > test2.txt &