#!/bin/sh
reset
find . -iname '*.php' -exec php -l {} \; | grep -i -P '^(?:(?!No syntax errors detected).)*$'