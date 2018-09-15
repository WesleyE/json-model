# JSON-Model

[![Build Status](https://travis-ci.org/WesleyE/json-model.svg?branch=master)](https://travis-ci.org/WesleyE/json-model)
[![Coverage Status](https://coveralls.io/repos/github/WesleyE/json-model/badge.svg?branch=develop)](https://coveralls.io/github/WesleyE/json-model?branch=develop)


The JSON-Model library allows you to save and load JSON Data from disk. It also mirrors some of Laravel's one-to-one and many-to-many/one relations so you can reference other files.  
This library is written in a way so that you can only load the files you need, it uses a Repository that caches the loaded models.

- Save simple models to JSON files
- Read them back, cache the contents in memory
- Have one-to-one, one-to-many and many-to-many relations using `$refs`.


## Notes

This is far from production ready, do not use (yet).

## Relations

This library can handle one-to-one one-to-many and many-to-many relations. In order to have all data in the JSON file, you should add the inverse of a relation so the relation will also show up in the other's JSON file. It can also automatically add the relation to the inverse, but it will not auto save. Remember to save all models or call `Repository::commitToDisk();`.

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
- [x] Define tests for the repository
- [x] Resolve the 'inverse' and add the relation
- [ ] Create documentation