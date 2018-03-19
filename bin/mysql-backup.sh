#!/bin/bash

#Configure this!
## MySQL Dumps From Prod
### Install mysql on Mac
#```bash
#brew install mysql
#```

### Configure MySQL Config Editor CLI (Will prompt for db password)
## Create a login-path on your local that makes sense for the project
#```bash
#mysql_config_editor set --login-path=<EDIT THIS> -h <HOST> -u <USER> -p
#```
#
#### Run bash dump script
#
#```bash
#sh bin/sql-backup.sh
#```

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"/../backups

FILE=$DIR/backup-`date +%Y%m%d-%H%M%S`.sql

DB_NAME='<EDIT THIS>'
LOGIN_PATH='<EDIT THIS>'

if [ ! -d "$DIR" ]; then
    mkdir $DIR;
fi

mysqldump --login-path=$LOGIN_PATH \
--ignore-table=$DB_NAME.craft_assettransformindex \
--ignore-table=$DB_NAME.craft_assetindexdata \
--ignore-table=$DB_NAME.craft_cache \
--ignore-table=$DB_NAME.craft_sessions \
--ignore-table=$DB_NAME.craft_templatecaches \
--ignore-table=$DB_NAME.craft_templatecachequeries \
--ignore-table=$DB_NAME.craft_templatecacheelements \
--ignore-table=$DB_NAME.craft_entryversions \
--single-transaction --skip-triggers --add-drop-table $DB_NAME > $FILE && \
#AND
mysqldump --login-path=$LOGIN_PATH -d $DB_NAME \
    craft_assettransformindex \
    craft_assetindexdata \
    craft_sessions \
    craft_templatecaches \
    craft_templatecachequeries \
    craft_templatecacheelements \
    craft_entryversions >> $FILE && \
#WARNING! THIS SED COMMAND ONLY WORKS ON MAC
sed -i '' 's/DEFINER=`[^`][^`]*`@`[^`][^`]*`//g' $FILE
