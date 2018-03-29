<?php

use models\ModelDB as MDB;

class User
{
    public function user_exists($username)
    {
        $sql = "SELECT COUNT(id) FROM sync.account WHERE username = :username";
        $query_params = array(
            ':username' => $username
        );
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public function add_user($mail)
    {
        $user_first = $_POST['first_name'];
        $user_last = $_POST['last_name'];
        $user_email = $_POST['email'];
        $user_department = $_POST['department'];
        $username = strtolower(substr($user_first, 0, 1) . $user_last);
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $email_code = uniqid('code_', true);

        $sql = "INSERT INTO account (username, first_name, last_name, email, ip, time, email_code, department_id) VALUES (:username, :firstname, :lastname, :email, :ip, :time, :email_code, :department)";
        $query_params = array(
            ':username' => $username,
            ':firstname' => $user_first,
            ':lastname' => $user_last,
            ':email' => $user_email,
            ':ip' => $ip,
            ':time' => $time,
            ':email_code' => $email_code,
            ':department' => $user_department
        );
        MDB::query($sql, $query_params);

        $message = "Hello $username, you have been registered with us. Please visit the link below so we can activate your account: ";
        $message .= getenv("APP_URL");
        $message .= "/activate.php?email=$user_email";
//        mail($user_email, 'Please activate your account', $message);
        $mail->addAddress($user_email);
        $mail->From = getenv("EMAIL_USER");
        $mail->FromName = getenv("EMAIL_USERNAME");
        $subject = 'Please activate your account';
        $mail->Subject = $subject;
        $mail->Body = $message;
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'There was an issue sending your registration email.';
        }
        return true;
    }

    public function email_exists($email)
    {
        $sql = "SELECT COUNT(id) FROM account WHERE email = :email";
        $query_params = array(
            ':email' => $email
        );
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public function register($username, $password, $email, $mail)
    {
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $email_code = uniqid('code_', true);
        $password = Bcrypt::hashPass($password);

        $sql = "INSERT INTO account (username, password, email, ip, time) VALUES (:username,:password,:email,:ip,:time)";
        $query_params = array(
            ':username' => $username,
            ':password' => $password,
            ':email' => $email,
            ':ip' => $ip,
            ':time' => $time
        );

        MDB::query($sql, $query_params);

        $message = "Hello $username, thank you for registering with us. Please visit the link below so we can activate your account: ";
        $message .= getenv("APP_URL");
        $message .= "/activate.php?email=$email&email_code=$email_code";
//        mail($email, 'Please activate your account', $message);
        $mail->addAddress($email);
        $mail->From = getenv("EMAIL_USER");
        $mail->FromName = getenv("EMAIL_USERNAME");
        $subject = 'Please activate your account';
        $mail->Subject = $subject;
        $mail->Body = $message;
        if (!$mail->send()) {
            echo 'There was an issue sending your activation email.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Your activation email was sent.';
        }
        $admin_message = "Hello $username has registered for access to ";
        $admin_message .= getenv("APP_NAME");
        $admin_message .= "Please setup permissions.";
//        mail(, '', $admin_message);
        $mail->ClearAllRecipients();
        $mail->addAddress(getenv("EMAIL_USER");
        $mail->From = getenv("EMAIL_USER");
        $mail->FromName = getenv("EMAIL_USERNAME");
        $subject = 'Please setup permissions';
        $mail->Subject = $subject;
        $mail->Body = $admin_message;
        if (!$mail->send()) {
            echo 'There was an issue sending the email to Michael.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Michael has been emailed to setup your permissions';
        }
    }

    public function activate($email)
    {
        $sql = "SELECT COUNT(id) FROM account WHERE email = :email";
        $query_params = array(
            ':email' => $email
        );
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public function login($username, $password)
    {
        $sql = "SELECT password, id FROM account WHERE username = :username";
        $query_params = array(
            ':username' => $username
        );
        $data = MDB::query($sql, $query_params, 'fetch');

        $stored_password = $data['password'];
        $id = $data['id'];
        if (Bcrypt::verifyPass($password, $stored_password) === true) {
            return $id;
        } else {
            return false;
        }
    }

    public function confirm_recover($email)
    {
        $username = $this->fetch_info('username', 'account', 'email', $email);
        $unique = uniqid('', true);
        $random = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $generated_string = $unique . $random;
        $sql = "UPDATE account SET generated_string = :generated_string WHERE email = :email";
        $query_params = array(
            ':generated_string' => $generated_string,
            ':email' => $email
        );
        MDB::query($sql, $query_params);
        return $generated_string;
    }

    public function recover($email, $generated_string)
    {
        if ($generated_string == 0) {
            return false;
        } else {
            $sql = "SELECT COUNT(id) FROM account WHERE email = :email AND generated_string = :generated_string";
            $query_params = array(
                ':email' => $email,
                ':generated_string' => $generated_string
            );
            $rows = MDB::query($sql, [], 'fetchColumn');
            if ($rows == 1) {
                $username = $this->fetch_info('username', 'account', 'email', $email);
                $user_id = $this->fetch_info('id', 'account', 'email', $email);
                $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $generated_password = substr(str_shuffle($charset), 0, 10);
                $this->change_password($user_id, $generated_password);
                $sql = "UPDATE account SET generated_string = 0 WHERE id = :id";
                $query_params = array(
                    ':id' => $user_id
                );
                MDB::query($sql, $query_params);
                return $generated_password;
            } else {
                return false;
            }
        }
    }

    public function change_password($user_id, $password)
    {
        $password_hash = Bcrypt::hashPass($password);
        $sql = "UPDATE account SET password = :password_hash WHERE id = :id";
        $query_params = array(
            ':password_hash' => $password_hash,
            ':id' => $user_id
        );
        return MDB::query($sql, $query_params, 'boolean');
    }

    public function fetch_info($what, $table, $field, $value = NULL)
    {
        $allowed = array('id', 'username', 'first_name', 'last_name', 'email');
        if (!in_array($what, $allowed, true) || !in_array($field, $allowed, true)) {
            throw new InvalidArgumentException;
        } else {
            $sql = "SELECT $what FROM $table WHERE $field = :value";

            $query_params = [
                //':what' => $what,
                //':table' => $table,
                //':field' => $field,
                ':value' => $value
            ];
            return MDB::query($sql, $query_params, 'fetchColumn');
        }
    }

    public function userdata($id)
    {
        $sql = "SELECT * FROM account WHERE id = :id";
        $query_params = array(
            ':id' => $id
        );
        return MDB::query($sql, $query_params, 'fetch');
    }
}
