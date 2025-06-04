<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cart_data = json_decode($_POST['cart_data'], true);

    // Check if this is a checkout (has user info)
    if (isset($_POST['name'], $_POST['address'], $_POST['email'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $address = $conn->real_escape_string($_POST['address']);
        $email = $conn->real_escape_string($_POST['email']);

        // Insert order info (optional: create an orders table and link cart items)
        // For now, just insert cart items
        if (!empty($cart_data)) {
            foreach ($cart_data as $item) {
                $product_name = $conn->real_escape_string($item['name']);
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];

                $sql = "INSERT INTO cart_items (product_name, quantity, price, customer_name, customer_address, customer_email) VALUES ('$product_name', $quantity, $price, '$name', '$address', '$email')";

                if ($conn->query($sql) !== TRUE) {
                    $message = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            $message = "Order received for $name, $address, $email";
        } else {
            $message = "No cart data received";
        }
    } else {
        // Just cart data, no user info
        if (!empty($cart_data)) {
            foreach ($cart_data as $item) {
                $product_name = $conn->real_escape_string($item['name']);
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];

                $sql = "INSERT INTO cart_items (product_name, quantity, price) VALUES ('$product_name', $quantity, $price)";

                if ($conn->query($sql) !== TRUE) {
                    $message = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            $message = "Cart data inserted successfully";
        } else {
            $message = "No cart data received";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = "No cart data received";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Form</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url("bg.jpg") no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            text-align: center;
            color: rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: rgba(173, 173, 149, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            margin: 10px;
            text-align: center;
        }

        .message {
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Checkout Form</h1>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="insert_cart.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <input type="hidden" name="cart_data" id="cart-data-checkout">

            <button type="submit">Submit Order</button>
        </form>

        <!-- Button to download PDF -->
        <form action="generate_pdf.php" method="POST">
            <input type="hidden" name="cart_data" id="cart-data">
            <button type="submit">Download Receipt</button>
        </form>
    </div>

    <script>
        // Get cart data from localStorage, or use an empty array if not present
        const cartData = JSON.stringify(JSON.parse(localStorage.getItem("cart") || "[]"));
        // Set for checkout form
        const cartCheckout = document.getElementById("cart-data-checkout");
        if (cartCheckout) cartCheckout.value = cartData;
        // Set for PDF form
        const cartPdf = document.getElementById("cart-data");
        if (cartPdf) cartPdf.value = cartData;
    </script>
</body>
</html>
