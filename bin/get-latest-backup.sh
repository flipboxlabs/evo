#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"/../backups

if [ ! -d "$DIR" ]; then
    mkdir $DIR;
fi

BUCKET=''
PROFILE=''
PREFIX=db-backups/PRODUCTION

#Pass the file bash file to this to populate the variables above
SOURCE_VAR_FILE=$1

if [ -z ${SOURCE_VAR_FILE+X} ] && [ ! -e "$SOURCE_VAR_FILE" ]; then
    echo "File doesn't exist: $SOURCE_VAR_FILE"
    exit 1;
fi

KEY=`aws --profile $PROFILE  s3 ls $BUCKET/$PREFIX --recursive | sort | tail -n 1 | awk '{print $4}'`
echo Downloading s3://$BUCKET/$KEY
aws --profile $PROFILE s3 cp s3://$BUCKET/$KEY $DIR

#link latest
unset -v LATEST
for file in "$DIR"/*; do
  [[ $FILE -nt $LATEST ]] && latest=$file
done

ln -s $LATEST $DIR/latest.sql
