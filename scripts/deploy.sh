#!/bin/bash

# Delete any existing file
aws s3 rm s3://serverless-gifmaker/mov_bbb.gif

serverless deploy

sleep 3

aws s3 cp ~/Downloads/test/mov_bbb.mp4 s3://serverless-gifmaker/mov_bbb.mp4
