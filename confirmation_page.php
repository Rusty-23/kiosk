<?php
require_once 'connection.php'; // Database connection
require_once 'lib/S3BucketClient.php';

// Fetch order number from URL
$orderNumber = $_GET['order_number'] ?? null;
if (!$orderNumber) {
    die('Invalid order number.');
}

// Fetch document details from the database
$stmt = $conn->prepare("SELECT * FROM request WHERE order_number = ?");
$stmt->bind_param("s", $orderNumber);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();

if (!$document) {
    die('No document found.');
}

// Assume S3 document link is stored in document_link field
$file_name = $document['document_link'];
$BUCKET = 'vendocu-datastore';
$url = '#';
if (!empty($file_name)) {
    $client = new S3BucketClient($BUCKET);
    $url = $client->getPresignedUrl($file_name, '+6 days');
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document Confirmation</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
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
            position: relative;
        }
        .container {
            background: #fff;
            padding: 80px;
            border-radius: 12px;
            row-gap: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            max-width: 55%;
            max-height: 90%;
            text-align: center;
        }
        .container header {
            height: 95px;
            width: 95px;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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
        .document-info {
            margin-top: 20px;
            text-align: left;
            font-size: 1rem;
        }
        .document-info p {
            margin: 10px 0;
        }
        .credit-box {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: #135701;
            color: #fff;
            font-size: 1.5rem;
            padding: 10px 20px;
            border-radius: 6px;
        }
        button {
            margin-top: 25px;
            width: 100%;
            color: #fff;
            font-size: 1.5rem;
            border: none;
            padding: 15px 0;
            cursor: pointer;
            border-radius: 6px;
            background: #28810f;
            transition: all 0.2s ease;
        }
        button:disabled {
            background: #ddd;
            cursor: not-allowed;
        }
        button:hover:not(:disabled) {
            background: #135701;
        }
        .cancel-btn {
            background-color: #e0e0e0;
            color: #000;
            margin-top: 15px;
        }
        .cancel-btn:hover {
            background-color: #ccc;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="img/vendocu.png" alt="Logo">
        </header>
        <h4>Document Confirmation</h4>

        <div class="document-info">
            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($document['order_number']); ?></p>
            <p><strong>Document Type:</strong> <?php echo htmlspecialchars($document['document_type']); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($document['doc_year']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($document['course']); ?></p>
            <p><strong>Section:</strong> <?php echo htmlspecialchars($document['section']); ?></p>
        </div>

        <button id="printButton" disabled>Print Document</button>

        <!-- Cancel Button -->
        <button class="cancel-btn" onclick="window.location.href='index.html';">Cancel</button>
    </div>

    <div class="credit-box">
        Credit: <span id="creditPoints">0</span>/20
    </div>

    <script>
        let creditPoints = 0;

        function insertCoin() {
            if (creditPoints < 20) {
                creditPoints += 5;
                updateCreditDisplay();
            }
        }

        function updateCreditDisplay() {
            document.getElementById('creditPoints').textContent = creditPoints;
            const printButton = document.getElementById('printButton');
            printButton.disabled = creditPoints < 20;
        }

        document.getElementById('printButton').addEventListener('click', function () {
            if (creditPoints >= 20) {
                creditPoints -= 20;
                updateCreditDisplay();
                const documentLink = "<?php echo $url ?>";
                const newWindow = window.open(documentLink, '_blank');
                if (newWindow) {
                    newWindow.focus();
                    newWindow.print();
                } else {
                    alert("Popup blocked. Please allow popups for this site.");
                }
            }
        });

        setInterval(insertCoin, 3000);
    </script>
</body>
</html>
