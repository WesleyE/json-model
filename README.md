# JSON-Model

[![Build Status](https://travis-ci.org/WesleyE/json-model.svg?branch=master)](https://travis-ci.org/WesleyE/json-model)
[![Coverage Status](https://coveralls.io/repos/github/WesleyE/json-model/badge.svg?branch=develop)](https://coveralls.io/github/WesleyE/json-model?branch=develop)


The JSON-Model library allows you to save and load JSON Data from disk. It also mirrors some of Laravel's one-to-one and many-to-many/one relations so you can reference other files.

## Notes

This is far from production ready, do not use (yet).

## Examples
```
$repository = new Repository();

// Setup the region
$region = Region::new();
$region->name = 'Europe';
$repository->save($region);

// Setup the country
$country = Country::new();
$country->alpha_2 = 'NL';
$country->name = 'The Netherlands';
$repository->save($country);

// Attach the region
$country->region()->associate($region);

// Test if we can get the region
$associatedRegion = $country->region()->get();
```

## Todo List

- [x] Move to a proper UUID package
- [x] Let the developer specify the json output directory
- [x] Move to an non-static repository, but keep the model<->repo connection
- [x] We cannot use 'realpath' when the relation does not exist yet
- [x] Create 'dirty' checks for saving
- [x] Create our own Exceptions
- [ ] Resolve the 'inverse' and add the relation
- [ ] Define tests for the repository
- [ ] Create documentation