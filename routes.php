<?php

    Flight::route('GET /register', function() {
        Flight::render('register.tpl', array());
    });

    Flight::route('POST /register', function() {
        $EMAIL_REGEX = "/[a-z0-9-.]+@([a-z0-9-]+\.[a-z0-9-]+){1,}/i";
        $PASSWORD_REGEX = "/[a-z0-9]{8,}/i";
        /* 
         * Email regex : https://imgur.com/YLrfgmQ.png
         * Password regex : https://imgur.com/IB9tex8.png
         * Graphics by https://jex.im/regulex
        */

        $data = Flight::request()->data;

        $errors = array();

        if (!$data->email || !preg_match($EMAIL_REGEX, $data->email))
        {
            $errors[] = !$data->email ? 'No entered email' : 'Entered email is not valid';
        }

        if (!$data->password || !preg_match($PASSWORD_REGEX, $data->password)) {
            $errors[] = !$data->password ? 'No entered password' : 'Entered password is not valid';
        }

        $statement = Flight::get('db')->prepare('
            SELECT email
            FROM utilisateurs
            WHERE email = :email
        ');

        $statement->execute(array(':email' => $data->email));

        $lines = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($lines) > 0) {
            $errors[] = 'Email already exists';
        }

        if (count($errors) == 0) {
            $statement = Flight::get('db')->prepare('
                INSERT INTO utilisateurs (email, mot_de_passe)
                VALUES (:email, :mot_de_passe)
            ');

            $statement->execute(array(
                ':email' => $data->email,
                ':mot_de_passe' => password_hash($data->password, PASSWORD_DEFAULT)
            ));

            Flight::render('success.tpl', array());
        } else {
            Flight::render('register.tpl', array('values' => $_POST, 'errors' => $errors));
        }
    });

    Flight::route('GET /login', function() {
        Flight::render('login.tpl', array());
    });

    Flight::route('POST /login', function() {
        $data = Flight::request()->data;

        $errors = array();

        if (!$data->email)
        {
            $errors[] = 'No entered email';
        }

        if (!$data->password) {
            $errors[] = 'No entered password';
        }

        if (count($errors) == 0) {
            $statement = Flight::get('db')->prepare('
                SELECT email, mot_de_passe
                FROM utilisateurs
                WHERE email = :email
            ');

            $statement->execute(array(':email' => $data->email));

            $lines = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (count($lines) == 0) {
                $errors[] = 'Email not registered';
            } else {
                if (!password_verify($data->password, $lines[0]['mot_de_passe'])) {
                    $errors[] = 'Password is not valid';
                }
            }
        }

        if (count($errors) == 0) {
            $_SESSION['name'] = $data->nom;
            Flight::redirect('/');
        } else {
            Flight::render('login.tpl', array('values' => $_POST, 'errors' => $errors));
        }
    });

    Flight::route('*', function() {
        echo 'Not found (Flight fallback route)';
    });

?>