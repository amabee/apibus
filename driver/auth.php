<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
include('../conn.php');

class AUTH
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    public function login($json)
    {
        $json = json_decode($json, true);

        try {
            if (isset($json['username']) && isset($json['password'])) {
                $username = $json['username'];
                $password = sha1($json['password']);

                $sql = 'SELECT `driver_id`, `firstname`, `lastname`, `email`, `password`, `assigned_bus`
                        FROM `tbldrivers`
                        WHERE `email` = :username AND `password` = :password';
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    unset($row['password']);
                    unset($this->conn);
                    unset($stmt);
                    return json_encode(array("success" => $row));
                } else {
                    return json_encode(array("error" => 'Invalid Credentials'));
                }
            } else {
                return json_encode(array('error' => 'Username or Password required!'));
            }
        } catch (PDOException $e) {
            return json_encode(array('error' => 'Exception Error'));
        } finally {
            unset($this->conn);
            unset($stmt);
        }
    }

    public function signup($json)
    {
        $json = json_decode($json, true);

        try {
            if (
                isset($json['password']) && isset($json['firstname']) &&
                isset($json['lastname']) && isset($json['email']) && isset($json['address'])
            ) {
                $password = sha1($json['password']);
                $firstname = $json['firstname'];
                $lastname = $json['lastname'];
                $email = $json['email'];
                $address = $json['address'];

                $checkSql = 'SELECT * FROM `tbldrivers` WHERE `email` = :email';
                $checkStmt = $this->conn->prepare($checkSql);
                $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
                $checkStmt->execute();

                if ($checkStmt->rowCount() > 0) {
                    return json_encode(array("error" => "Email already exists. Please choose another email."));
                }

                $sql = 'INSERT INTO `tbldrivers` (`firstname`, `lastname`, `email`, `password`, `address`) 
                        VALUES (:firstname, :lastname, :email, :password, :address)';
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
                $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':address', $address, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    return json_encode(array("success" => "Driver account successfully created."));
                } else {
                    return json_encode(array("error" => "Failed to create user account."));
                }
            } else {
                return json_encode(array("error" => "All fields (firstname, lastname, email, password) are required!"));
            }
        } catch (PDOException $e) {
            // return json_encode(array('error' => 'An error occurred while creating the account.'));
            return json_encode(array('error' => $e->getMessage()));
        } finally {
            unset($this->conn);
            unset($stmt);
            unset($checkStmt);
        }
    }
}

$auth = new AUTH();

if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_REQUEST["operation"]) && isset($_REQUEST["json"])) {
        $operation = $_REQUEST["operation"];
        $json = $_REQUEST["json"];

        switch ($operation) {
            case "login":
                echo $auth->login($json);
                break;

            case "signup":
                echo $auth->signup($json);
                break;

            default:
                echo json_encode(array("error" => "No such operation here"));
                break;
        }
    } else {
        echo json_encode(array("error" => "Missing Parameters"));
    }
} else {
    echo json_encode(array("error" => "Invalid Request Method"));
}
?>