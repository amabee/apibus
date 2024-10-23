<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
include('../conn.php');


class Passenger
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
    }

    public function getDestinations()
    {

        try {
            $sql = "SELECT DISTINCT `tid`, `from_loc`, `to_loc`, `fare_price`, `bus_assigned`, tbldrivers.driver_id FROM `trips`
                        INNER JOIN tbldrivers ON tbldrivers.assigned_bus = trips.bus_assigned";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return json_encode(array("success" => $result));
        } catch (PDOException $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }

    public function getBusSeats($json)
    {
        $json = json_decode($json, true);
        $tripId = $json['trip_id'];

        if (!isset($json['reservation_time'])) {
            $reservationDate = date('Y-m-d');
        } else {
            $reservationDate = $json['reservation_time'];
        }

        try {
            // Fetch the bus assigned to the trip and also get the fare_price from the trips table
            $sql = "SELECT `bus`.`bid`, `bus`.`seat_capacity`, `trips`.`fare_price` 
                    FROM `bus` 
                    JOIN `trips` ON `bus`.`bid` = `trips`.`bus_assigned` 
                    WHERE `trips`.`tid` = :trip_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':trip_id', $tripId);
            $stmt->execute();
            $bus = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$bus) {
                return json_encode(array("error" => "Bus not found for the specified trip."));
            }

            $seatCapacity = $bus['seat_capacity'];
            $farePrice = $bus['fare_price']; // Fetch fare price from the result

            // Count reserved seats based on the reservation date
            $sql = "SELECT COUNT(`seat_number`) as reserved_count 
                    FROM `reservations` 
                    WHERE `trip_id` = :trip_id 
                    AND `reservation_status` = 'active' 
                    AND DATE(`reservation_time`) = :reservation_date";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':trip_id', $tripId);
            $stmt->bindParam(':reservation_date', $reservationDate);
            $stmt->execute();
            $reserved = $stmt->fetch(PDO::FETCH_ASSOC);
            $reservedCount = $reserved['reserved_count'];

            $availableSeatsCount = $seatCapacity - $reservedCount;

            // Get reserved seat numbers based on the reservation date
            $sql = "SELECT `seat_number` 
                    FROM `reservations` 
                    WHERE `trip_id` = :trip_id 
                    AND `reservation_status` = 'active' 
                    AND DATE(`reservation_time`) = :reservation_date";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':trip_id', $tripId);
            $stmt->bindParam(':reservation_date', $reservationDate);
            $stmt->execute();
            $reservedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $allSeats = range(1, $seatCapacity);
            $availableSeats = array_diff($allSeats, $reservedSeats);

            return json_encode(array(
                "success" => true,
                "seat_capacity" => $seatCapacity,
                "reserved_count" => $reservedCount,
                "available_count" => $availableSeatsCount,
                "available_seats" => array_values($availableSeats),
                "reserved_seats" => array_values($reservedSeats),
                "reservation_date" => $reservationDate,
                "fare_price" => $farePrice
            ));
        } catch (PDOException $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }

    public function createReservation($json)
    {
        $json = json_decode($json, true);

        // Ensure required fields are present
        if (!isset($json['trip_id'])) {
            return json_encode(array("error" => "Trip ID is empty."));
        }
        if (!isset($json['paymentMode'])) {
            return json_encode(array("error" => "Payment mode is empty."));
        }
        if (!isset($json['passengerType'])) {
            return json_encode(array("error" => "Passenger type is empty."));
        }
        if (!isset($json['numOfPassenger'])) {
            return json_encode(array("error" => "Number of passengers is empty."));
        }
        if (!isset($json['totalAmount'])) {
            return json_encode(array("error" => "Total amount is empty."));
        }
        if (!isset($json['driverId'])) {
            return json_encode(array("error" => "Driver ID is empty."));
        }
        if (!isset($json['passengerId'])) {
            return json_encode(value: array("error" => "Passenger ID is empty."));
        }
        if (!isset($json['seatNumber'])) {
            return json_encode(array("error" => "Seat number is empty."));
        }
        if (!isset($json['reservationDate'])) {
            return json_encode(array("error" => "Reservation time is empty."));
        }

        try {
            $sql = "INSERT INTO `reservations`(`trip_id`, `payment_mode`, `passenger_type`, `number_of_passengers`, `total_amount`, `created_at`, `driver_id`, `passenger_id`, `seat_number`, `reservation_time`, `reservation_status`)
                    VALUES (:trip_id, :paymentMode, :passengerType, :numOfPassenger, :totalAmount, NOW(), :driverId, :passengerId, :seatNumber, :reservationDate, 'active')";

            // Prepare the statement
            $stmt = $this->conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':trip_id', $json['trip_id']);
            $stmt->bindParam(':paymentMode', $json['paymentMode']);
            $stmt->bindParam(':passengerType', $json['passengerType']);
            $stmt->bindParam(':numOfPassenger', $json['numOfPassenger']);
            $stmt->bindParam(':totalAmount', $json['totalAmount']);
            $stmt->bindParam(':driverId', $json['driverId']);
            $stmt->bindParam(':passengerId', $json['passengerId']);
            $stmt->bindParam(':seatNumber', $json['seatNumber']);
            $stmt->bindParam(':reservationDate', $json['reservationDate']);

            // Execute the statement
            if ($stmt->execute()) {
                return json_encode(array("success" => "Reservation created successfully."));
            } else {
                return json_encode(array("error" => "Failed to create reservation."));
            }
        } catch (PDOException $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }




}

$passenger = new Passenger();


if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_REQUEST["operation"]) && isset($_REQUEST["json"])) {
        $operation = $_REQUEST["operation"];
        $json = $_REQUEST["json"];

        switch ($operation) {

            case "getDestinations":
                echo $passenger->getDestinations();
                break;

            case "getBusSeats":
                echo $passenger->getBusSeats($json);
                break;

            case "createReservation":
                echo $passenger->createReservation($json);
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