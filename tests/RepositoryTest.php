<?php

namespace MicheleAngioni\PhalconRepositories\Tests;

class RepositoryTest extends TestCase
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

        $user = $usersRepo->firstBy(['username' => 'User 2']);
        $this->assertEquals('User 2', $user->getUsername());
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

        $users = $usersRepo->getBy(['username' => 'User 2']);
        $this->assertEquals(1, count($users));
        $this->assertEquals('User 2', $users->getFirst()->getUsername());
    }

    public function testGetByWithLikeUsername()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $data = [
            'username%OR%text' => ['%Unique%', 'LIKE']
        ];

        $users = $usersRepo->getBy($data);
        $this->assertEquals(1, count($users));
    }

    public function testGetByWithLikeText()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $data = [
            'username%OR%text' => ['%Particular%', 'LIKE']
        ];

        $users = $usersRepo->getBy($data);
        $this->assertEquals(1, count($users));
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

    public function testGetInAndWhereByPage()
    {
        $userNumber = 5;
        $idArray = [2, 3, 4];

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getInAndWhereByPage(1, 2,'id', $idArray);
        $this->assertEquals(2, count($users));

        foreach ($users as $user) {
            $this->assertTrue(in_array($user->getId(), $idArray));
        }
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

    public function testGetByGroupBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $users[2]->setText($users[1]->getText());
        $users[2]->save();

        $usersList = $usersRepo->getByGroupBy('text', [], true);

        $this->assertEquals(2, count($usersList));
        $this->assertTrue($usersList[0]['text'] === $users[0]->getText());
        $this->assertTrue((int)$usersList[0]['number'] === 1);
        $this->assertTrue($usersList[1]['text'] === $users[1]->getText());
        $this->assertTrue((int)$usersList[1]['number'] === 2);
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

    public function testTruncate()
    {
        // $this->markTestSkipped(
            // 'The TRUNCATE keyword is not present in SQLite, so the truncate() method cannot be tested.'
        // );
    }

    public function testCount()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $countedUserNumber = $usersRepo->count();

        $this->assertEquals($userNumber, $countedUserNumber);
    }

    public function testCountBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $countedUserNumber = $usersRepo->countBy(['username' => 'User 1']);

        $this->assertEquals(1, $countedUserNumber);
    }

    public function testCountByLikeUsername()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $countedUserNumber = $usersRepo->countBy(['username%OR%text' => ['%Unique%', 'LIKE']]);

        $this->assertEquals(1, $countedUserNumber);
    }

    public function testCountByLikeText()
    {
        $userNumber = 3;

        $usersRepo = new UsersRepo();
        $this->createUsers($usersRepo, $userNumber);

        $countedUserNumber = $usersRepo->countBy(['username%OR%text' => ['%Particular%', 'LIKE']]);

        $this->assertEquals(1, $countedUserNumber);
    }

    protected function createUsers($userRepo, $number = 3)
    {
        if ($number == 0) {
            return;
        }

        $users = [];

        for ($i = 1; $i <= $number - 1; $i++) {
            $users[] = $userRepo->create([
                'username' => 'User ' . $i,
                'text' => 'Long Text ' . $i
            ]);
        }

        $users[] = $userRepo->create([
            'username' => 'Unique Username ',
            'text' => 'Particular text '
        ]);

        return $users;
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

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
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
