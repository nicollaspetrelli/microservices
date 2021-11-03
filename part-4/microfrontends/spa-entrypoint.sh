#!/bin/sh
set -e
if [ ! -d "/app/node_modules/webpack" ]; then
    yarn upgrade
fi
yarn start
