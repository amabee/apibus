<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
include('../conn.php');

class Driver
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    // Get bookings assigned to the driver
    public function getBookings($json)
    {
        $json = json_decode($json, true);
        $driver_id = $json['driver_id'];

        try {
            $stmt = $this->conn->prepare("SELECT reservations.*, passengers.firstname, passengers.lastname 
                                            FROM reservations 
                                            INNER JOIN passengers ON reservations.passenger_id = passengers.pid 
                                            WHERE driver_id = :driver_id AND DATE(reservations.reservation_time) = CURDATE()
                                            ORDER BY reservations.reservation_status ASC;
                                            ");

            // Bind the driver_id parameter
            $stmt->bindParam(':driver_id', $driver_id, PDO::PARAM_INT);

            $stmt->execute();
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode(array('success' => $reservations));
        } catch (PDOException $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    public function dashBoardData($json)
    {
        $json = json_decode($json, true);
        $driver_id = $json['driver_id'];

        try {
            $stmt = $this->conn->prepare("
                    SELECT 
                    COUNT(DISTINCT res.passenger_id) AS total_passengers,
                    COUNT(DISTINCT trips.tid) AS total_trips
                FROM reservations res
                JOIN tbldrivers ON res.driver_id = tbldrivers.driver_id
                JOIN bus ON tbldrivers.assigned_bus = bus.bid
                JOIN trips ON bus.bid = trips.bus_assigned
                WHERE tbldrivers.driver_id = :driver_id AND DATE(res.reservation_time) = CURDATE();
            ");
            $stmt->bindParam(':driver_id', $driver_id);
            $stmt->execute();

            // Fetch the results
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return json_encode(array('success' => $result));
        } catch (PDOException $e) {
            return json_encode(array('error' => 'Unable to retrieve data'));
        }
    }



    // Get the passenger list for a specific reservation
    public function getPassengerList($json)
    {
        $json = json_decode($json, true);
        $reservation_id = $json['reservation_id'];

        try {
            $stmt = $this->conn->prepare("
                SELECT p.firstname, p.lastname, p.email 
                FROM passengers p 
                INNER JOIN reservations r ON p.pid = r.passenger_id 
                WHERE r.id = :reservation_id");
            $stmt->bindParam(':reservation_id', $reservation_id);
            $stmt->execute();
            $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode(array('success' => $passengers));
        } catch (PDOException $e) {
            return json_encode(array('error' => 'Unable to fetch passengers'));
        }
    }

    public function checkInPassenger($json)
    {
        $json = json_decode($json, true);
        $reservation_id = $json['reservation_id'];

        try {
            $stmt = $this->conn->prepare("UPDATE reservations SET reservation_status = 'checked-in' WHERE id = :reservation_id");
            $stmt->bindParam(':reservation_id', $reservation_id);
            $stmt->execute();

            return json_encode(array('success' => 'Passenger checked in'));
        } catch (PDOException $e) {
            return json_encode(array('error' => 'Unable to check in passenger'));
        }
    }

    public function updateTripStatus($json)
    {
        $json = json_decode($json, true);
        $reservation_id = $json['reservation_id'];
        $status = $json['status'];

        try {
            $stmt = $this->conn->prepare("UPDATE reservations SET reservation_status = :status WHERE id = :reservation_id");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':reservation_id', $reservation_id);
            $stmt->execute();

            return json_encode(array('success' => 'Trip status updated'));
        } catch (PDOException $e) {
            return json_encode(array('error' => 'Unable to update trip status'));
        }
    }

    public function getCurrentDriverTrips($json)
    {
        $json = json_decode($json, true);
        $driver_id = $json['driver_id'];

        try {
            $stmt = $this->conn->prepare("SELECT trips.*, tbldrivers.driver_id, bus.seat_capacity FROM `trips`
                                                    INNER JOIN bus ON trips.bus_assigned = bus.bid
                                                    INNER JOIN tbldrivers ON bus.bid = tbldrivers.assigned_bus
                                                    WHERE tbldrivers.driver_id = :driver_id");
            $stmt->bindParam(':driver_id', $driver_id);
            $stmt->execute();
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode(array('success' => $reservations));
        } catch (PDOException $e) {
            return json_encode(array('error' => 'Unable to fetch reservations'));
        }
    }
}

$driver = new Driver();

if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_REQUEST["operation"]) && isset($_REQUEST["json"])) {
        $operation = $_REQUEST["operation"];
        $json = $_REQUEST["json"];

        switch ($operation) {

            case "getBookings":
                echo $driver->getBookings($json);
                break;

            case "getdashBoardData":
                echo $driver->dashBoardData($json);
                break;

            case "getPassengerList":
                echo $driver->getPassengerList($json);
                break;

            case "checkInPassenger":
                echo $driver->checkInPassenger($json);
                break;

            case "updateTripStatus":
                echo $driver->updateTripStatus($json);
                break;

            case "getCurrentDriverTrips":
                echo $driver->getCurrentDriverTrips($json);
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