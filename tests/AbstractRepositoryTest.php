<?php

namespace MicheleAngioni\PhalconRepositories\Tests;

class AuthWebTest extends TestCase
{

    public function testCreate()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = Users::find();
        $this->assertEquals(3, count($users));
    }

    public function testAll()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->all();
        $this->assertEquals(3, count($users));
    }

    public function testFind()
    {
        $userNumber = 3;
        $userId = 1;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $user = $usersRepo->find($userId);
        $this->assertEquals($userId, $user->getId());
    }

    public function testFindOrFail()
    {
        $userNumber = 3;
        $userId = 1;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $user = $usersRepo->FindOrFail($userId);
        $this->assertEquals($userId, $user->getId());
    }

    /**
     * @expectedException \MicheleAngioni\PhalconRepositories\ModelNotFoundException
     */
    public function testFailingFindOrFail()
    {
        $userNumber = 3;
        $userId = 1;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $usersRepo->FindOrFail($userId * 1000);
    }

    public function testFirst()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $user = $usersRepo->first();
        $this->assertEquals(1, $user->getId());
    }

    public function testFirstOrFail()
    {
        $userNumber = 3;
        $userId = 1;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $user = $usersRepo->FirstOrFail();
        $this->assertEquals($userId, $user->getId());
    }

    public function testFirstBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $user = $usersRepo->firstBy(['username' => 'User 1']);
        $this->assertEquals('User 1', $user->getUsername());
    }

    /**
     * @expectedException \MicheleAngioni\PhalconRepositories\ModelNotFoundException
     */
    public function testFailingFirstOrFailBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $usersRepo->firstOrFailBy(['username' => 'Error']);
    }

    public function testGetBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getBy(['username' => 'User 1']);
        $this->assertEquals(1, count($users));
        $this->assertEquals('User 1', $users->getFirst()->getUsername());
    }

    public function testGetByLimit()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getByOrder('id', [], 'desc', 2);
        $this->assertEquals(2, count($users));
        $this->assertEquals(3, $users->getFirst()->getId());
    }

    public function testGetIn()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getIn('id', [2, 3]);
        $this->assertEquals(2, count($users));
        $this->assertEquals(2, $users->getFirst()->getId());
    }

    public function testGetNotIn()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getNotIn('id', [2, 3]);
        $this->assertEquals(1, count($users));
        $this->assertEquals(1, $users->getFirst()->getId());
    }

    public function testGetByPage()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getByPage(2, 1, [], 'id', 'asc');
        $this->assertEquals(1, count($users));
        $this->assertEquals(2, $users->getFirst()->getId());
    }

    public function testUpdateById()
    {
        $userNumber = 3;
        $userId = 2;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $usersRepo->updateById($userId, ['text' => 'Test text']);

        $user = $usersRepo->findOrFail($userId);

        $this->assertEquals('Test text', $user->getText());
    }

    /**
     * @expectedException \MicheleAngioni\PhalconRepositories\ModelNotFoundException
     */
    public function testDestroy()
    {
        $userNumber = 3;
        $userId = 2;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $usersRepo->destroy($userId);

        $usersRepo->findOrFail($userId);
    }

    /**
     * @expectedException \MicheleAngioni\PhalconRepositories\ModelNotFoundException
     */
    public function testDestroyFirstBy()
    {
        $userNumber = 3;
        $userId = 2;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $usersRepo->destroyFirstBy(['username', 'User ' . $userId]);

        $usersRepo->findOrFail($userId);
    }


    protected function createUsers($userRepo, $number = 3)
    {
        for ($i = 1; $i <= $number; $i++) {
            $userRepo->create([
                'username' => 'User ' . $i
            ]);
        }
    }
}

class UsersRepo extends \MicheleAngioni\PhalconRepositories\AbstractRepository
{
    /**
     * @var Users
     */
    protected $model;

    function __construct()
    {
        $this->model = new Users();
    }
}

class Users extends \Phalcon\Mvc\Model
{
    public $id;

    public $username;

    public $text;

    public function getSource()
    {
        return 'users';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getText()
    {
        return $this->text;
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
