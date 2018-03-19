#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"/../backups

if [ ! -d "$DIR" ]; then
    mkdir $DIR;
fi

BUCKET='<EDIT THIS>'
PROFILE='<EDIT THIS>'
PREFIX=db-backups/PRODUCTION

KEY=`aws --profile $PROFILE  s3 ls $BUCKET/$PREFIX --recursive | sort | tail -n 1 | awk '{print $4}'`
echo Downloading s3://$BUCKET/$KEY
aws --profile $PROFILE s3 cp s3://$BUCKET/$KEY $DIR
