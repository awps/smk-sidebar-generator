#!/usr/bin/env bash

PLUGIN_SLUG="${PWD##*/}"

sed -i -e "s/__STABLE_TAG__/$TRAVIS_TAG/g" ./src/readme.txt
sed -i -e "s/__STABLE_TAG__/$TRAVIS_TAG/g" ./src/${PWD##*/}.php
svn co --depth immediates "https://plugins.svn.wordpress.org/$PLUGIN_SLUG" ./svn

svn update --set-depth infinity ./svn/trunk
svn update --set-depth infinity ./svn/assets
svn update --set-depth infinity ./svn/tags/$TRAVIS_TAG

cp -R ./src/* ./svn/trunk
cp -R ./wp_org/assets/* ./svn/assets

# 3. Switch to SVN repository
cd ./svn

svn add --force trunk
svn add --force assets

svn cp trunk tags/$TRAVIS_TAG

svn add --force tags

svn ci  --message "Release $TRAVIS_TAG" \
        --username $SVN_USERNAME \
        --password $SVN_PASSWORD \
        --non-interactive
