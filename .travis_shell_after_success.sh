#!/bin/bash

if [ "$TRAVIS_REPO_SLUG" == "Progi1984/YATSPHP" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "5.5" ]; then

  echo -e "Publishing PHPDoc...\n"

  cp -R build/docs $HOME/docs-latest

  cd $HOME
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "travis-ci"
  git clone --quiet --branch=gh-pages https://${GH_TOKEN}@github.com/Progi1984/YATSPHP gh-pages > /dev/null

  cd gh-pages
  cd docs
  git rm -rf ./$TRAVIS_BRANCH
  cp -Rf $HOME/docs-latest ./$TRAVIS_BRANCH
  git add -f .
  git commit -m "PHPDocumentor (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
  git push -fq origin gh-pages > /dev/null

  echo -e "Published PHPDoc to gh-pages.\n"

fi