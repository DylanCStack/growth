<?php
    class User
    {
        private $name;
        private $password;
        private $id;

        function __construct($name, $password, $id = null)
        {
            $this->name = (string) $name;
            $this->password = (string) $password;
            $this->id = $id;

        }

        function getId()
        {
            return $this->id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }

        function getPassword()
        {
            return $this->password;
        }

        function setPassword($new_password)
        {
            $this->password = (string) $new_password;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO users (name, password) VALUES ('{$this->getName()}', '{$this->getPassword()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
            $_SESSION['user'] = $this;
        }

        function getGames()
        {

        }

        function getMaps()
        {
            $returned_maps = $GLOBALS['DB']->query("SELECT * FROM maps WHERE creator_id={$this->getId};")->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Map", ['title', 'type', 'id', 'creator_id', 'champion_id', 'champ_score']);
            $returned_map->setTiles($returned_map->getCoordinates());
            return $returned_maps;
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM users WHERE id={$this->getId()};");
            // MAYBE ADD FUNCTIONALITY
            // $GLOBALS['DB']->exec("DELETE FROM games WHERE user_id={$this->getId()};");

        }

        static function logIn($uname, $upassword)
        {
            $user = $GLOBALS['DB']->query("SELECT * FROM users WHERE name = '{$uname}' AND password = '{$upassword}';");//re add password when working

            if($user)
            {
                $new_user = $user->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "User", ['name', 'password', 'id'])[0];

                $_SESSION['user'] = $new_user;
            }
        }

        function logOut()
        {
            $_SESSION['user'] = [];
        }

        static function find($id)
        {
            $returned_user = $GLOBALS['DB']->query("SELECT * FROM users WHERE id={$id};")->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "User", ['name', 'password', 'id'])[0];

            return $returned_user;
        }

        static function getAll()
        {
            $users = [];

            $returned_users = $GLOBALS['DB']->query("SELECT * FROM users;");
            foreach($returned_users as $user)
            {
                $new_user = new User($user['name'], $user['password'], $user['id']);
                array_push($users, $new_user);
            }
            return $users;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM users;");
            //DELETE GAMES ASSOCIATED WITH PLAYER?
            // $GLOBALS['DB']->exec("DELETE FROM games WHERE user_id = {$this->getId()};");

        }
    }

?>