# 1984
## A small tool to remotely track bash histories

This tool is merely a small project of mine to aggregate my own bash histories. It it is obviously not intended for surveillance of other users. If you plan to use it like that make sure, that the bash history is written immediately (`history -a`) and the program runs with root rights.

## maker.php

Run this script to generate files to be deployed.
```sh
php maker.php source-server-ip targer-server-host-or-ip communication-protocol sdatabase-dns enable-log-deduplication
```

- source-server-ip: for example `1.2.3.4`
- target-server-host-or-ip: for example `1.1.2.3` or `idrinth.de`
- communication-protocol: http by default, could be any curl can use in theory
- database-dns: optional, defaults to sqlite:/var/log/remote_bash_log.sqlite
- enable-log-deduplication: defaults to false, set to true to remove duplicate entries from the logs in one package

### dist/home/*.kill

Contains the export statement to permanently disable this specific instance of the script. Use kill afterwards. Do NOT lose this file, since disabling is hard to do otherwise.

### dist/source/*.sh

This is the script to be started on the server to monitor.

### dist/source/*.sh

This is the script actually monitoring the server.

### dist/target/*.php

This is the api, that validates the write requests and saves them to the given database
