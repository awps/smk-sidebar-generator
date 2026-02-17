#!/usr/bin/env bash
# https://zerowp.com/?p=55

set -e

# Get the plugin slug from this git repository.
PLUGIN_SLUG="${PWD##*/}"

# Get the current release version
TAG="${GITHUB_REF#refs/tags/}"

echo "Deploying version $TAG of $PLUGIN_SLUG to WordPress.org..."

# Get the SVN data from wp.org in a folder named `svn`
svn co --depth immediates "https://plugins.svn.wordpress.org/$PLUGIN_SLUG" ./svn

svn update --set-depth infinity ./svn/trunk
svn update --set-depth infinity ./svn/assets

# Clean trunk directory (remove all files, keep directory)
rm -rf ./svn/trunk/*

# Copy files from `src` to `svn/trunk`
cp -R ./src/* ./svn/trunk

# Copy the images from `assets` to `svn/assets`
cp -R ./wp_org/assets/* ./svn/assets

# Switch to SVN directory
cd ./svn

# Add new files and remove deleted files
svn add --force trunk
svn add --force assets

# Remove files from SVN that no longer exist
svn status | grep '^!' | awk '{print $2}' | xargs -r svn rm

# Check if tag already exists
if svn ls "tags/$TAG" 2>/dev/null; then
    echo "Tag $TAG already exists, updating..."
    svn update --set-depth infinity "tags/$TAG"
    rm -rf "tags/$TAG"/*
    cp -R trunk/* "tags/$TAG"/
else
    # Create the version tag in svn
    svn cp trunk "tags/$TAG"
fi

# Prepare the tag for commit
svn add --force tags

# Commit files to wordpress.org.
svn ci  --message "Release $TAG" \
        --username "$SVN_USERNAME" \
        --password "$SVN_PASSWORD" \
        --non-interactive

echo "Successfully deployed version $TAG to WordPress.org"