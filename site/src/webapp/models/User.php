<?php

namespace ttm4135\webapp\models;

class User
{
    const INSERT_QUERY = "INSERT INTO users(username, password, email, bio, isadmin) VALUES(:username, :password, :email , :bio , :isadmin)";
    const UPDATE_QUERY = "UPDATE users SET username= :username, password= :password, email= :email, bio= :bio, isadmin= :isadmin WHERE id= :id ";
    const DELETE_QUERY = "DELETE FROM users WHERE id= :id ";
    const FIND_BY_NAME_QUERY = "SELECT * FROM users WHERE username= :username";
    const FIND_BY_ID_QUERY = "SELECT * FROM users WHERE id= :id ";


    protected $id = null;
    protected $username;
    protected $password;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $isAdmin = 0;

    static $app;



    static function make($id, $username, $password, $email, $bio, $isAdmin )
    {
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->bio = $bio;
        $user->isAdmin = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    function save()
    {
        if ($this->id === null) {
            $statement = self::$app->db->prepare(self::INSERT_QUERY);
            $statement->bindValue(':username', $this->username);
            $statement->bindValue(':password', $this->password);
            $statement->bindValue(':email', $this->email);
            $statement->bindValue(':bio', $this->bio);
            $statement->bindValue(':isadmin', $this->isAdmin);

        } else {
            $statement = self::$app->db->prepare(self::UPDATE_QUERY);
            $statement->bindValue(':username', $this->username);
            $statement->bindValue(':password', $this->password);
            $statement->bindValue(':email', $this->email);
            $statement->bindValue(':bio', $this->bio);
            $statement->bindValue(':isadmin', $this->isAdmin);
            $statement->bindValue(':id', $this->id);


        }
        return $statement->execute();
    }

    function delete()
    {
        $statement = self::$app->db->prepare(self::UPDATE_QUERY);
        $statement->bindValue(':id', $this->id);

        return $statement->execute();
    }

    function getId()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }
    function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }


    /**
     * Get user in db by userid
     *
     * @param string $userid
     * @return mixed User or null if not found.
     */
    static function findById($userid)
    {
        $statement = self::$app->db->prepare(self::FIND_BY_ID_QUERY);
        $statement->bindValue(':id', $userid);
        $result = $statement->execute();
        $row = $statement->fetch();

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     */
    static function findByUser($username)
    {
        $statement = self::$app->db->prepare(self::FIND_BY_NAME_QUERY);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }

    
    static function all()
    {
        $statement = self::$app->db->prepare("SELECT * FROM users");
        $statement->execute();
        $results = $statement->fetchAll();


        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            array_push($users, $user);
        }

        return $users;
    }

    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['bio'],
            $row['isadmin']
        );
    }

}


  User::$app = \Slim\Slim::getInstance();

