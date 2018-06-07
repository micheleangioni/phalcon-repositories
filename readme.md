# Phalcon Repositories

[![License](https://poser.pugx.org/michele-angioni/phalcon-repositories/license)](https://packagist.org/packages/michele-angioni/phalcon-repositories)
[![Latest Stable Version](https://poser.pugx.org/michele-angioni/phalcon-repositories/v/stable)](https://packagist.org/packages/michele-angioni/phalcon-repositories)
[![Latest Unstable Version](https://poser.pugx.org/michele-angioni/phalcon-repositories/v/unstable)](https://packagist.org/packages/michele-angioni/phalcon-repositories)
[![Build Status](https://travis-ci.org/micheleangioni/phalcon-repositories.svg)](https://travis-ci.org/micheleangioni/phalcon-repositories)

## Introduction

Phalcon Repositories lets you easily build repositories for your Phalcon models, for both **SQL** and **Mongo** drivers.

PHP 7.1+ and Phalcon 3.2+ are required.

## Installation

Phalcon Repositories can be installed through Composer, just run `composer require michele-angioni/phalcon-repositories`.

## Usage with SQL Drivers

The abstract class `AbstractRepository` consists of a model wrapper with numerous useful queries to be performed over the Phalcon models.
This way implementing the repository pattern becomes straightforward.

As an example let's say we have a `MyApp\Models\Posts` model. 

The easiest way to create a Posts repository is to define a class as such

```php
<?php

namespace MyApp\Repos;

use MicheleAngioni\PhalconRepositories\AbstractRepository;
use MyApp\Models\Posts;

class PostsRepository extends AbstractEloquentRepository
{
    protected $model;

    public function __construct(Posts $model)
    {
        $this->model = $model;
    }
}
```

Suppose now we need the Post repository in our PostController. For example we can retrieve a Post this way

```php
<?php

namespace MyApp\Controllers;

use MyApp\Repos\PostsRepository as PostsRepo;
use Phalcon\Mvc\Controller;
use MyApp\Models\Posts;

class PostsController extends Controller 
{
    
    public function showAction($idPost)
    {
        $postsRepo = new PostsRepo(new Posts());
        
        $post = $postsRepo->find($idPost);

        // Use the retrieved post
    }
}
```
    
We could also bind out repository to the container through the Phalcon dependency injection.
We just need to add a new `postRepo` service in our bootstrap file

```php
$di->set('postsRepo', function () {
    return new MyApp\Repos\PostsRepository(new \MyApp\Models\Posts());
});
```

and than use it in the controller

```php
<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class PostsController extends Controller 
{
    
    public function showAction($idPost)
    {
        $postsRepo = $this->getDI()->getPostsRepo();
        
        $post = $postsRepo->find($idPost);

        // Use the retrieved Post
    }
}
```

## Usage with MongoDB

The abstract class `AbstractCollectionRepository`, similary to `AbstractRepository`, consists of a model wrapper with numerous useful queries to be performed over the Phalcon collections.
This way implementing the repository pattern becomes straightforward.

As an example let's say we have a `MyApp\Models\Posts` collection

```php
<?php

namespace MyApp\Models;

use Phalcon\Mvc\MongoCollection;

class Posts extends MongoCollection
{
    use \MicheleAngioni\PhalconRepositories\MongoFix; // Fix for Phalcon 3.1.x with PHP 7.1

    [...]
}
```

The easiest way to create a Posts repository is to define a class as such

```php
<?php namespace MyApp\Repos;

use MicheleAngioni\PhalconRepositories\AbstractCollectionRepository;
use MyApp\Models\Posts;

class PostsRepository extends AbstractCollectionRepository
{
    protected $model;

    public function __construct(Posts $model)
    {
        $this->model = $model;
    }
}
```

Suppose now we need the Post repository in our PostController. For example we can retrieve a Post this way

```php
<?php

namespace MyApp\Controllers;

use MyApp\Repos\PostsRepository as PostsRepo;
use Phalcon\Mvc\Controller;
use MyApp\Models\Posts;

class PostsController extends Controller
{

    public function showAction($idPost)
    {
        $postsRepo = new PostsRepo(new Posts());

        $post = $postsRepo->find($idPost);

        // Use the retrieved Post
    }
}
```

We could also bind out repository to the container through the Phalcon dependency injection.
We just need to add a new `postRepo` service in our bootstrap file

```php
$di->set('postsRepo', function () {
    return new MyApp\Repos\PostsRepository(new \MyApp\Models\Posts());
});
```

and than use it in the controller

```php
<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class PostsController extends Controller
{

    public function showAction($idPost)
    {
        $postsRepo = $this->getDI()->getPostsRepo();

        $post = $postsRepo->find($idPost);

        // Use the retrieved post
    }
}
```

### Method list

The `AbstractRepository` and `AbstractCollectionRepository` empower automatically our repositories of the following public methods:

- `all()`
- `find($id)`
- `findOrFail($id)`
- `first()`
- `firstOrFail()`
- `firstBy(array $where = [])`
- `firstOrFailBy(array $where = [])`
- `getBy(array $where = [])`
- `getByLimit(int $limit, array $where = [])`
- `getByOrder(string $orderBy, array $where = [], string $order = 'desc', int $limit = 0)`
- `getIn(string $whereInKey, array $whereIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0)`
- `getNotIn(string $whereNotInKey, array $whereNotIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0)`
- `getInAndWhereByPage(int $page = 1, int $limit = 10, string $whereInKey = null, array $whereIn = [], $where = [], $orderBy = null, string $order = 'desc')`
- `getByPage(int $page = 1, int $limit = 10, array $where = [], string $orderBy = null, string $order = 'desc')`
- `create(array $inputs = [])`
- `updateById($id, array $inputs)`
- `destroy($id)`
- `destroyFirstBy(array $where)`
- `count()`
- `countBy(array $where = [])`

The `AbstractRepository` contains also the methods:

- `getByGroupBy(string $groupBy, array $where = [], bool $addCounts = false)`
- `truncate()`

while the `AbstractCollectionRepository` allows for aggregations through:

- `getAggregate(array $match = [], array $project = [], array $group = [], int $limit = 0)`

### The $where parameter with SQL drivers

The `$where` parameter allows the use of various operators with the SQL driver, other than the equals `=`, even the `LIKE` keyword.

The following formats are supported:

- `'key' => 'value'`

    Examples:

    ```php
    $where = ['username' => 'Richard']
    ```

- `'key' => ['value', 'operator']`

    Examples:

    ```php
    $where = ['age' => [30, '=']]
    $where = ['age' => [30, '<']]
    $where = ['age' => [30, '>']]
    $where = ['username' => ['%Fey%', 'LIKE']]
    ```

- `['key1%OR%key2'] => ['value', 'operator']`

    Examples:

    ```php
    `$where = ['username%OR%description' => ['%Feynman%', 'LIKE']]`
    ```

### SQL Injection

The `AbstractRepository` and `AbstractCollectionRepository` use bind parameters for all `$id` and `$where` clauses.
`$inputs` parameters in create and update queries are automatically escaped by Phalcon.

The security of the other parameters ($whereInKey, $whereIn = [], $orderBy, $order, $limit etc.) is up to you.

## Testing

Install dependencies with `composer install` and then run `vendor/bin/phpunit tests`.

## Contribution guidelines

Phalcon Repositories follows PSR-1, PSR-2 and PSR-4 PHP coding standards, and semantic versioning.

Pull requests are welcome.

## License

Phalcon Repositories is free software distributed under the terms of the MIT license.
