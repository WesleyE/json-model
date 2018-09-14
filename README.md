# JSON-Model

[![Build Status](https://travis-ci.org/WesleyE/json-model.svg?branch=master)](https://travis-ci.org/WesleyE/json-model)
[![Coverage Status](https://coveralls.io/repos/github/WesleyE/json-model/badge.svg?branch=develop)](https://coveralls.io/github/WesleyE/json-model?branch=develop)


The JSON-Model library allows you to save and load JSON Data from disk. It also mirrors some of Laravel's one-to-one and many-to-many/one relations so you can reference other files.

## Notes

This is far from production ready, do not use (yet).

## Todo List

- [x] Move to a proper UUID package
- [ ] Move to an non-static repository, but keep the model<->repo connection
- [ ] Resolve the 'inverse' and add the relation
- [ ] We cannot use 'realpath' when the relation does not exist yet
- [ ] Create 'dirty' checks for saving
- [ ] Define tests for the repository
- [ ] Let the developer specify the json output directory
- [ ] Create documentation