#!/bin/bash

REPO_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"/../


DB_DRIVER='mysql'
DB_SERVER=''
DB_USER=''
DB_PASSWORD=''
DB_DATABASE=''
DB_SCHEMA=''
DB_TABLE_PREFIX=''
DB_PORT='3306'
CRAFT_ENVIRONMENT=''

#Pass the file bash file to this to populate the variables above
SOURCE_VAR_FILE=$1

if [ -z ${SOURCE_VAR_FILE+X} ] && [ ! -e "$SOURCE_VAR_FILE" ]; then
    echo "File doesn't exist: $SOURCE_VAR_FILE"
    exit 1;
fi

#Append underscore if there is a table prefix
if [ -n "$DB_TABLE_PREFIX" ]; then
    DB_TABLE_PREFIX="${DB_TABLE_PREFIX}_"
fi

#### Run bash dump script
#
#```bash
#sh bin/sql-backup-server.sh
#```

# Create the backups dir if it's not there
BACKUP_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"/../backups

FILE=$BACKUP_DIR/backup-`date +%Y%m%d-%H%M%S`.sql

if [ ! -d "$BACKUP_DIR" ]; then
    mkdir $BACKUP_DIR;
fi

BUCKET='<EDIT THIS>'
PROFILE=''
if [ "LOCAL" == $CRAFT_ENVIRONMENT ]; then
    PROFILE='--profile=<EDIT THIS>'
    DB_SERVER=127.0.0.1
fi

CONNECTION="-h $DB_SERVER -u $DB_USER -p$DB_PASSWORD -P $DB_PORT"

mysqldump  $CONNECTION \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"assettransformindex" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"assetindexdata" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"cache" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"sessions" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"templatecaches" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"templatecachequeries" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"templatecacheelements" \
--ignore-table="$DB_DATABASE."$DB_TABLE_PREFIX"entryversions" \ #<EDIT THIS> You may want to back these up.
--single-transaction --add-drop-table --skip-triggers $DB_DATABASE > $FILE && \
#AND
mysqldump $CONNECTION -d $DB_DATABASE \
    "$DB_TABLE_PREFIX"assettransformindex \
    "$DB_TABLE_PREFIX"assetindexdata \
    "$DB_TABLE_PREFIX"sessions \
    "$DB_TABLE_PREFIX"templatecaches \
    "$DB_TABLE_PREFIX"templatecachequeries \
    "$DB_TABLE_PREFIX"templatecacheelements \
    "$DB_TABLE_PREFIX"entryversions >> $FILE;

gzip $FILE &&  aws $PROFILE s3 sync $BACKUP_DIR s3://$BUCKET/db-backups/$CRAFT_ENVIRONMENT/`date +%Y%m%d`/;
