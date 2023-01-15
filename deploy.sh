#!/usr/bin/env bash
# https://zerowp.com/?p=55

# Get the plugin slug from this git repository.
PLUGIN_SLUG="${PWD##*/}"

# Get the current release version
TAG=$(sed -e "s/refs\/tags\///g" <<< $GITHUB_REF)

# Get the SVN data from wp.org in a folder named `svn`
svn co --depth immediates "https://plugins.svn.wordpress.org/$PLUGIN_SLUG" ./svn

svn update --set-depth infinity ./svn/trunk
svn update --set-depth infinity ./svn/assets
svn update --set-depth infinity ./svn/tags/$TAG

# Copy files from `src` to `svn/trunk`
cp -R ./src/* ./svn/trunk

# Copy the images from `assets` to `svn/assets`
cp -R ./wp_org/assets/* ./svn/assets

# 3. Switch to SVN directory
cd ./svn

# Prepare the files for commit in SVN
svn add --force trunk
svn add --force assets

# Create the version tag in svn
svn cp trunk tags/$TAG

# Prepare the tag for commit
svn add --force tags

# Commit files to wordpress.org.
svn ci  --message "Release $TAG" \
        --username $SVN_USERNAME \
        --password $SVN_PASSWORD \
        --non-interactive
