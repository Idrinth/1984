# 1984 [![Lint](https://github.com/Idrinth/1984/actions/workflows/lint.yml/badge.svg)](https://github.com/Idrinth/1984/actions/workflows/lint.yml) [![PSR-12](https://github.com/Idrinth/1984/actions/workflows/psr-12.yml/badge.svg)](https://github.com/Idrinth/1984/actions/workflows/psr-12.yml) [![Unittest](https://github.com/Idrinth/1984/actions/workflows/unittest.yml/badge.svg)](https://github.com/Idrinth/1984/actions/workflows/unittest.yml) [![Phan](https://github.com/Idrinth/1984/actions/workflows/phan.yml/badge.svg)](https://github.com/Idrinth/1984/actions/workflows/phan.yml)
## A small tool to remotely track bash histories

This tool is merely a small project of mine to aggregate my own bash histories. It it is obviously not intended for surveillance of other users. If you plan to use it like that make sure, that the bash history is written immediately (`history -a`) and the program runs with root rights.

The name is a reference to George Orwell's book Nineteen Eighty-Four, since this is a surveillance tool if used on other users than yourself. Consider this a warning of what may be possible.

## Requirements

### Source-Server

- php >= 5.3
- ext-curl (optional)
- ext-pcntl (optional)
- openssl
- root access

### Target-Server

- webserver
- php >= 7.1
- write access to database
- ext-pdo
- mariadb/mysql/sqlite depending on database choice

### Generator-System

- php >= 7.1
- openssl

### Development-System

- php >= 7.1
- ext-curl
- ext-ast
- openssl

## bin/remote-logger.php

Run this script to generate files to be deployed.
```sh
php bin/remote-logger.php source-server-ip targer-server-host-or-ip communication-protocol sdatabase-dns enable-log-deduplication
```

- source-server-ip: for example `1.2.3.4`
- target-server-host-or-ip: for example `1.1.2.3` or `idrinth.de`, can take multiple comma-separated entries
- communication-protocol: http by default, could be any curl can use in theory
- database-dns: optional, defaults to `sqlite:/tmp/remote_bash_log.sqlite`
- enable-log-deduplication: defaults to `false`, set to `true` to remove duplicate entries from the logs in one package

### dist/home/*.kill

Contains the export statement to permanently disable this specific instance of the script. Do NOT lose this file, since disabling is hard to do otherwise.

### dist/source/*.sh

This is the script to be started on the server to monitor.

### dist/source/*.php

This is the script actually monitoring the server.

### dist/target/*.php

This is the api, that validates the write requests and saves them to the given database

## Help & Support

If you want direct support options, feel free to drop by on [Discord](https://discord.gg/xHSF8CGPTh).
