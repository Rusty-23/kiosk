<?php
require_once '../connection.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Concatenate the 6 input fields into a single order number
    $orderNumber = implode('', $_POST['orderNumber']);

    // Prepare the SQL query to find the order number in the database
    $stmt = $conn->prepare("SELECT * FROM request WHERE order_number = ? AND request_status = 'confirmed'");
    $stmt->bind_param("s", $orderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the order number exists and status is "confirmed", redirect to the confirmation page
        header("Location: confirmation_page.php?order_number=" . $orderNumber);
        exit();
    } else {
        // If the order number is not found or not confirmed, trigger the error modal
        echo "<script>
                window.addEventListener('load', function() {
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                });
              </script>";
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>
