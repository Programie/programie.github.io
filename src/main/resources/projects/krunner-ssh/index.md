## Introduction

KRunner SSH provides a simple backend for KRunner providing SSH hosts from your .ssh/known_hosts file as search results.

It allows you to directly connect to often used SSH servers by simply searching for the hostname in KRunner.

## Installation

* Copy `ssh-runner.desktop` to `~/.local/share/kservices5`
* Start `runner.py` (add it to your startup applications)

## Terminal Command

As every terminal emulator has different options on how to start a new SSH session, you can specify the command to open the SSH session as arguments passed to `runner.py`.

The `{}` placeholder will be replaced by the hostname.

Examples:

* Konsole: `konsole -e 'ssh {}'` (default)
* [Tilix](https://gnunn1.github.io/tilix-web/): `tilix -e 'ssh {}'`
* GNOME Terminal: `gnome-terminal -- ssh {}`

Example call: `/path/to/runner.py konsole -e 'ssh {}'`

## Usage

Open KRunner (usually Alt+F2) and search for a host listed in your known_hosts file.