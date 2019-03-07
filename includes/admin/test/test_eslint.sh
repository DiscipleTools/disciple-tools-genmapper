#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")/../../../"

printf 'eslint version: %s\n' "$(eslint --version)"

eval eslint \
    --ignore-pattern vendor/ \
    --ignore-pattern lib/ \
    --ignore-pattern '*.min.js' \
    .
