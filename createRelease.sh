#!/bin/bash

# Halt on errors
set -e

# Paths
COMPILED_GUI="pc_admin/ui"
ADMIN_ARCHIVE="pc_admin.tar.gz"
LDAP_ARCHIVE="ldap.tar.gz"
SAMBA_ARCHIVE="samba.tar.gz"
ADMIN_FOLDER="pc_admin/"
LDAP_FOLDER="ldap/"
SAMBA_FOLDER="samba/"

# Cleanup
if [ -d "$COMPILED_GUI" ]; then
  rm -rf $COMPILED_GUI
fi
if [ -f "$ADMIN_ARCHIVE" ]; then
  rm -rf $ADMIN_ARCHIVE
fi
if [ -f "$LDAP_ARCHIVE" ]; then
  rm -rf $LDAP_ARCHIVE
fi
if [ -f "$SAMBA_ARCHIVE" ]; then
  rm -rf $SAMBA_ARCHIVE
fi

# Compile frontend
pushd ui
npm install
#npm run build-prod
npm run build-dev
popd

# Create ui folder and copy compiled code over
mkdir $COMPILED_GUI
cp -r ui/www/* "$COMPILED_GUI/"

# Create archive
tar cfvz $ADMIN_ARCHIVE $ADMIN_FOLDER
tar cfvz $LDAP_ARCHIVE $LDAP_FOLDER
tar cfvz $SAMBA_ARCHIVE $SAMBA_FOLDER
