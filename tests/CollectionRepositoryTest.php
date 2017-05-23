<?php

namespace MicheleAngioni\PhalconRepositories\Tests;

class CollectionRepositoryTest extends TestCase
{

    protected function tearDown()
    {
        // Users

        $users = UsersMongo::find();

        foreach ($users as $user) {
            $user->delete();
        }

        parent::tearDown();
    }

    public function testCreate()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = UsersMongo::find();
        $this->assertEquals(3, count($users));
    }

    public function testAll()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->all();
        $this->assertEquals(3, count($users));
    }

    public function testFind()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $user = $usersRepo->find($userId);
        $this->assertEquals($userId, $user->getId());
    }

    public function testFindOrFail()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $user = $usersRepo->findOrFail($userId);
        $this->assertEquals($userId, $user->getId());
    }

    /**
     * @expectedException \MicheleAngioni\PhalconRepositories\ModelNotFoundException
     */
    public function testFailingFindOrFail()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $usersRepo->FindOrFail(substr($userId, 0, -4) . 'ffff');
    }

    public function testFirst()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $user = $usersRepo->first();
        $this->assertEquals($userId, $user->getId());
    }

    public function testFirstOrFail()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $user = $usersRepo->FirstOrFail();
        $this->assertEquals($userId, $user->getId());
    }

    public function testFirstBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
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

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $usersRepo->firstOrFailBy(['username' => 'Error']);
    }

    public function testGetBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getBy(['username' => 'User 2']);
        $this->assertEquals(1, count($users));
        $this->assertEquals('User 2', $users[0]->getUsername());
    }

    public function testGetByLimit()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getByOrder('id', [], 'desc', 2);
        $this->assertEquals(2, count($users));
    }

    public function testGetIn()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);

        $foundUsers = $usersRepo->getIn('_id', [$users[0]->getId(), $users[1]->getId()]);
        $this->assertEquals(2, count($foundUsers));
    }

    public function testGetNotIn()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);

        $foundUsers = $usersRepo->getNotIn('_id', [$users[0]->getId(), $users[1]->getId()]);
        $this->assertEquals(1, count($foundUsers));
        $this->assertEquals($users[2]->getId(), $foundUsers[0]->getId());
    }

    public function testGetInAndWhereByPage()
    {
        $userNumber = 5;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $idArray = [$users[1]->getId(), $users[2]->getId(), $users[3]->getId()];

        $users = $usersRepo->getInAndWhereByPage(1, 2,'_id', $idArray);
        $this->assertEquals(2, count($users));

        foreach ($users as $user) {
            $this->assertTrue(in_array($user->getId(), $idArray));
        }
    }

    public function testGetByPage()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $users = $usersRepo->getByPage(2, 1, [], '_id', 'asc');
        $this->assertEquals(1, count($users));
    }

    public function testUpdateById()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

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

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $usersRepo->destroy($userId);

        $usersRepo->findOrFail($userId);
    }

    /**
     * @expectedException \MicheleAngioni\PhalconRepositories\ModelNotFoundException
     */
    public function testDestroyFirstBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $users = $this->createUsers($usersRepo, $userNumber);
        $userId = $users[0]->getId();

        $usersRepo->destroyFirstBy(['username', 'User ' . $userId]);

        $usersRepo->findOrFail($userId);
    }

    public function testCount()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $countedUserNumber = $usersRepo->count();

        $this->assertEquals($countedUserNumber, $userNumber);
    }

    public function testCountBy()
    {
        $userNumber = 3;

        $usersRepo = new UsersMongoRepo();
        $this->createUsers($usersRepo, $userNumber);

        $countedUserNumber = $usersRepo->countBy(['username' => 'User 1']);

        $this->assertEquals(1, $countedUserNumber);
    }

    /**
     * @param UsersMongoRepo $userRepo
     * @param int $number
     * @return array
     */
    protected function createUsers(UsersMongoRepo $userRepo, int $number = 3): array
    {
        if ($number == 0) {
            return [];
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

class UsersMongoRepo extends \MicheleAngioni\PhalconRepositories\AbstractCollectionRepository
{
    /**
     * @var UsersMongo
     */
    protected $model;

    function __construct()
    {
        $this->model = new UsersMongo();
    }
}

class UsersMongo extends \Phalcon\Mvc\MongoCollection
{
//    use \MicheleAngioni\PhalconRepositories\MongoFix;

    public $id;

    public $username;

    public $text;

    public function getSource()
    {
        return 'users';
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getText()
    {
        return $this->text;
    }

    public static function find(array $parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst(array $parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
