#!/bin/sh

# Awk is choosen because it's fast and portable. You can use gawk, original awk or even the lightning fast mawk.
# The mysqldump file is traversed only once.

# Converts a mysqldump file into a Sqlite 3 compatible file. It also extracts the MySQL `KEY xxxxx` from the
# CREATE block and create them in separate commands _after_ all the INSERTs.

# Usage: $ ./mysql2sqlite | sqlite3 database.sqlite
# --compatible=ansi
# mysqldump --compatible=ansi --default-character-set=utf8  --skip-extended-insert --compact -u root -p123456  hehu360 cities districts provinces | \


awk -F ",$" '

BEGIN{ FS=",$" }

# Skip comments
/^\/\*/ { next }

# Print all `INSERT` lines. The single quotes are protected by another single quote.
/INSERT/ { gsub( /\\\047/, "\047\047" ); print; next }

# Print the ?CREATE? line as is and capture the table name.
/^CREATE/ {
        print
        if ( match( $0, /\"[^\"]+/ ) ) tableName = substr( $0, RSTART+1, RLENGTH-1 ) 
}

# Replace `FULLTEXT KEY` or any other `XXXXX KEY` except PRIMARY by `KEY`
/^  [^"]+KEY/ && !/^  PRIMARY KEY/ { gsub( /.+KEY/, "  KEY" ) }

# Print all fields definition lines except the `KEY` lines.
/^  / && !/^(  KEY|\);)/ {
        gsub( /AUTO_INCREMENT|auto_increment/, "" )
        gsub( /COMMENT.+,/, "" )
        gsub( /(CHARACTER SET|character set) [^ ]+ /, "" )
        gsub( /DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP|default current_timestamp on update current_timestamp/, "" )
        gsub( /(COLLATE|collate) [^ ]+ /, "" )
        gsub(/(ENUM|enum)[^)]+\)/, "text ")
        gsub(/(SET|set)\([^)]+\)/, "text ")
        gsub(/UNSIGNED|unsigned/, "")
        if (prev) print prev ","
        prev = $1
}

# `KEY` lines are extracted from the `CREATE` block and stored in array for later print 
# in a separate `CREATE KEY` command. The index name is prefixed by the table name to 
# avoid a sqlite error for duplicate index name.
/^(  KEY|\);)/ {
        if (prev) print prev
        prev=""
        if ($0 == ");"){
                print
        } else {
                if ( match( $0, /\"[^\"]+/ ) ) indexName = substr( $0, RSTART+1, RLENGTH-1 ) 
                if ( match( $0, /\([^\)]+/ ) ) indexKey = substr( $0, RSTART+1, RLENGTH-1 ) 
                key[tableName]=key[tableName] "CREATE INDEX \"" tableName "_" indexName "\" ON \"" tableName "\" (" indexKey ");\n"
        }
}

# Print all `KEY` creation lines.
END {
        for (table in key) printf key[table]
}
' 

