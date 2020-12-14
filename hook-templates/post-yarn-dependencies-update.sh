#!/usr/bin/env bash

# Runs when yarn dependencies have changed e.g. package.json or yarn.lock content have changed

set -x # echo on

yarn install
