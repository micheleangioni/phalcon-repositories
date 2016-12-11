# Phalcon Repositories

[![License](https://poser.pugx.org/michele-angioni/phalcon-repositories/license)](https://packagist.org/packages/michele-angioni/phalcon-repositories)
[![Latest Stable Version](https://poser.pugx.org/michele-angioni/phalcon-repositories/v/stable)](https://packagist.org/packages/michele-angioni/phalcon-repositories)
[![Latest Unstable Version](https://poser.pugx.org/michele-angioni/phalcon-repositories/v/unstable)](https://packagist.org/packages/michele-angioni/phalcon-repositories)
[![Build Status](https://travis-ci.org/micheleangioni/phalcon-repositories.svg)](https://travis-ci.org/micheleangioni/phalcon-repositories)

## Introduction

Phalcon Repositories lets you easily build repositories for your Phalcon 2 models.

## Installation

Support can be installed through Composer, just include `"michele-angioni/phalcon-repositories": "~0.1"` to your composer.json and run `composer update` or `composer install`.

## Usage

The abstract class `AbstractRepository` consists of a model wrapper with numerous useful queries to be performed over the Phalcon models.
This way implementing the repository pattern becomes straightforward.

As an example let's say we have a `MyApp\Models\Posts` model. 

The easiest way to create a Posts repository is to define a class as such

    <?php namespace MyApp\Repos;

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

Suppose now we need the Post repository in our PostController. For example we can retrieve a post this way 

    <?php namespace MyApp\Controllers;

    use MyApp\Repos\PostsRepository as PostsRepo;
    use Phalcon\Mvc\Controller;

    class PostsController extends Controller 
    {
        
        public function showAction($idPost)
        {
            $postsRepo = new PostsRepo();
            
            $post = $postsRepo->find($idPost);

            // Use the retrieved post
        }
    }
    
We could also bind out repository to the container through the Phalcon dependency injection.
We just need to add a new `postRepo` service in our bootstrap file

    [...]
    
    $di->set('postsRepo', function () {
        return new MyApp\Repos\PostsRepository();
    });
    
    [...]

and than use it in the controller

    <?php namespace MyApp\Controllers;
    
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

The `EloquentRepository` empowers automatically our repositories of the following public methods:

- all()
- find($id)
- findOrFail($id)
- first()
- firstOrFail()
- firstBy(array $where = [])
- firstOrFailBy(array $where = [])
- getBy(array $where = [])
- getByLimit($limit, array $where = [])
- getByOrder($orderBy, array $where = [], $order = 'desc', $limit = 0)
- getIn($whereInKey, array $whereIn = [], $orderBy = NULL, $order = 'desc', $limit = 0)
- getNotIn($whereNotInKey, array $whereNotIn = [], $orderBy = NULL, $order = 'desc', $limit = 0)
- getByPage($page = 1, $limit = 10, array $where = [], $orderBy = NULL, $order = 'desc')
- create(array $inputs = [])
- updateById($id, array $inputs)
- destroy($id)
- destroyFirstBy(array $where)
- count()
- countBy(array $where = [])

### SQL Injection

The `AbstractRepository` uses bind parameters for all `$id` and `$where` clauses. 
`$inputs` parameters in create and update queries are automatically escaped by Phalcon.

The security of the other parameters ($whereInKey, $whereIn = [], $orderBy, $order, $limit etc.) is up to you.

## Contribution guidelines

Phalcon Repositories follows PSR-1, PSR-2 and PSR-4 PHP coding standards, and semantic versioning.

Pull requests are welcome.

## License

Phalcon Repositories is free software distributed under the terms of the MIT license.
