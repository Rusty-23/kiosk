<?php
require_once 'connection.php'; // Database connection


$error = ''; // Initialize the error variable

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Concatenate the order number from the individual inputs
    $orderNumber = 'ORD-' . implode('', $_POST['orderNumber']); // Add "ORD-" prefix and combine the 6 digits into one string

    // Query the database to check if the order number exists and is confirmed
    $stmt = $conn->prepare("SELECT * FROM request WHERE order_number = ? AND request_status = 'confirmed'");
    $stmt->bind_param("s", $orderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Order number exists and is confirmed
        header("Location: confirmation_page.php?order_number=$orderNumber"); // Redirect to confirmation page
        exit();
    } else {
        // Order number does not exist or is not confirmed
        $error = "Invalid order number or the request is not confirmed.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification Form</title>
    <!-- Include Bootstrap CSS for modal styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
      /* Import Google font - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
body {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #135701;
}
:where(.container, form, .input-field, header) {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.container {
  background: #fff;
  padding: 120px 165px;
  border-radius: 12px;
  row-gap: 20px;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  max-width: 55%;
  max-height: 90%;
}
.container header {
  height: 95px;
  width: 95px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.container header img {
  max-width: 80%;
  max-height: 80%;
}
.container h4 {
  font-size: 2.25rem;
  color: #135701;
  font-weight: 500;
}
form .input-field {
  flex-direction: row;
  column-gap: 12px;
}
.input-field input {
  height: 95px;
  width: 72px;
  border-radius: 6px;
  outline: none;
  font-size: 2.125rem;
  text-align: center;
  border: 1px solid #ddd;
}
.input-field input:focus {
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
}
.input-field input::-webkit-inner-spin-button,
.input-field input::-webkit-outer-spin-button {
  display: none;
}
form button {
  margin-top: 25px;
  width: 100%;
  color: #fff;
  font-size: 1.5rem;
  border: none;
  padding: 15px 0;
  cursor: pointer;
  border-radius: 6px;
  pointer-events: none;
  background: #28810f;
  transition: all 0.2s ease;
}
form button.active {
  background-color: #135701;
  pointer-events: auto;
}
form button:hover {
  background: #135701;
}
.modal-content {
    background-color: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}
.modal-header {
    background-color: #28810f;
    color: #fff;
    border-bottom: none;
    border-radius: 12px 12px 0 0;
}
.modal-footer {
    border-top: none;
}
.modal-body {
    font-size: 1.125rem;
}

    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="img/vendocu.png" alt="Logo">
        </header>
        <h4>Enter Order Number</h4>

        <form method="POST" action="kiosk.php">
            <div class="input-field">
                <input type="number" name="orderNumber[]" />
                <input type="number" name="orderNumber[]" disabled />
                <input type="number" name="orderNumber[]" disabled />
                <input type="number" name="orderNumber[]" disabled />
                <input type="number" name="orderNumber[]" disabled />
                <input type="number" name="orderNumber[]" disabled />
            </div>
            <button type="submit">Verify</button>
        </form>

        <!-- Error Modal -->
        <?php if (!empty($error)) { ?>
            <div class="modal fade show" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true" style="display: block;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="errorModalLabel">Error</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php echo $error; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Include Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        const inputs = document.querySelectorAll("input"),
            button = document.querySelector("button");

        inputs.forEach((input, index1) => {
            input.addEventListener("keyup", (e) => {
                const currentInput = input,
                    nextInput = input.nextElementSibling,
                    prevInput = input.previousElementSibling;

                if (currentInput.value.length > 1) {
                    currentInput.value = "";
                    return;
                }
                if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
                    nextInput.removeAttribute("disabled");
                    nextInput.focus();
                }
                if (e.key === "Backspace") {
                    inputs.forEach((input, index2) => {
                        if (index1 <= index2 && prevInput) {
                            input.setAttribute("disabled", true);
                            input.value = "";
                            prevInput.focus();
                        }
                    });
                }
                if (!inputs[5].disabled && inputs[5].value !== "") {
                    button.classList.add("active");
                    return;
                }
                button.classList.remove("active");
            });
        });

        window.addEventListener("load", () => inputs[0].focus());

        // Show the modal if there's an error
        <?php if (!empty($error)) { ?>
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        <?php } ?>
    </script>
</body>
</html>
