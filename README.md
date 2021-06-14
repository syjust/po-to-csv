# PO TO CSV

## Description

A little symfony command using [sepia/po-parser](https://github.com/raulferras/PHP-po-parser) to provide a csv generator from [Poedit](https://poedit.net) translations files (widely used in WordPress environment)

## requirements

php 7.3 at least

## Installation

* Clone the repo: `git clone https://github.com/syjust/po-to-csv`
* Go into the dowloaded directory: `cd po-to-csv/`
* Install symfony dependencies: `composer install` (using [composer](https://getcomposer.org/download/))

## Usage

`php bin/console app:csv:generate fr.po`

This command will generate csv on standard output from `fr.po` file.

You can override csv headers using `-s` & `-t` options.

More informations : `php bin/console app:csv:geneate --help`

```
Description:
  Generate a csv file from a po file using gettext

Usage:
  app:csv:generate [options] [--] <po_file>

Arguments:
  po_file                                the po file to use to generate csv

Options:
  -s, --source-language=SOURCE-LANGUAGE  Override the source language of po file (msgid language) [default: "en_US"]
  -t, --target-language=TARGET-LANGUAGE  Override the target language (msgstr language) - default is red from "Language" po_file header
  ...
```
