<?php
// ACH 09/29/26 code logic was done with the help of Claude Code
// Customer management system with CRUD operations for customers and payments
// the php used to connect to the database is in db.php but will not be pushed in repo for security reasons. It is in the .gitignore file. The .env file is also in the .gitignore file for security reasons. The .env file contains the database connection information. The .env file is not pushed to the repo for security reasons
require_once 'db.php';
$sort = isset($_GET['sort']) ? $connection->real_escape_string($_GET['sort']) : 'last_name';
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';

// Determine next order for toggling
$next_order = ($order === 'ASC') ? 'DESC' : 'ASC';

// ACH 09/29/26 Get action and ID from GET parameters
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// CC 09/29/26 Handle form submissions for customer and payment operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($post_action === 'add') {
        $first = $connection->real_escape_string($_POST['first_name']);
        $last = $connection->real_escape_string($_POST['last_name']);
        $street = $connection->real_escape_string($_POST['street_address']);
        $city = $connection->real_escape_string($_POST['city']);
        $phone = $connection->real_escape_string($_POST['phone']);
        $sql = "INSERT INTO customer (first_name, last_name, street_address, city, phone) VALUES ('$first', '$last', '$street', '$city', '$phone')";
        if ($connection->query($sql) === TRUE) {
            header("Location: customer.php");
            exit;
        } else {
            // CC 09/29/26 Log error internally and show user-friendly message
            error_log("Database error: " . $sql . " - " . $connection->error);
            $error = "An error occurred while processing your request. Please try again.";
        }
    } elseif ($post_action === 'update') {
        $id = (int)$_POST['id'];
        $first = $connection->real_escape_string($_POST['first_name']);
        $last = $connection->real_escape_string($_POST['last_name']);
        $street = $connection->real_escape_string($_POST['street_address']);
        $city = $connection->real_escape_string($_POST['city']);
        $phone = $connection->real_escape_string($_POST['phone']);
        $sql = "UPDATE customer SET first_name='$first', last_name='$last', street_address='$street', city='$city', phone='$phone' WHERE customer_serial=$id";
        if ($connection->query($sql) === TRUE) {
            header("Location: customer.php");
            exit;
        } else {
            // CC 09/29/26 Log error internally and show user-friendly message
            error_log("Database error: " . $sql . " - " . $connection->error);
            $error = "An error occurred while processing your request. Please try again.";
        }
    } elseif ($post_action === 'delete') {
        $id = (int)$_POST['id'];
        // Delete payments first (foreign key not defined but safe)
        $connection->query("DELETE FROM payment WHERE customer_serial=$id");
        $sql = "DELETE FROM customer WHERE customer_serial=$id";
        if ($connection->query($sql) === TRUE) {
            header("Location: customer.php");
            exit;
        } else {
            // CC 09/29/26 Log error internally and show user-friendly message
            error_log("Database error: " . $sql . " - " . $connection->error);
            $error = "An error occurred while processing your request. Please try again.";
        }
    } elseif ($post_action === 'add_payment') {
        $customer_id = (int)$_POST['customer_serial'];
        $date = $connection->real_escape_string($_POST['payment_date']);
        $amount = $connection->real_escape_string($_POST['payment_amount']);
        $desc = $connection->real_escape_string($_POST['description']);
        $sql = "INSERT INTO payment (customer_serial, payment_date, payment_amount, description) VALUES ($customer_id, '$date', $amount, '$desc')";
        if ($connection->query($sql) === TRUE) {
            header("Location: customer.php?action=payments&id=$customer_id");
            exit;
        } else {
            // CC 09/29/26 Log error internally and show user-friendly message
            error_log("Database error: " . $sql . " - " . $connection->error);
            $error = "An error occurred while processing your request. Please try again.";
        }
    } elseif ($post_action === 'update_payment') {
        $payment_id = (int)$_POST['id'];
        $customer_id = (int)$_POST['customer_serial'];
        $date = $connection->real_escape_string($_POST['payment_date']);
        $amount = $connection->real_escape_string($_POST['payment_amount']);
        $desc = $connection->real_escape_string($_POST['description']);
        $sql = "UPDATE payment SET payment_date='$date', payment_amount=$amount, description='$desc' WHERE payment_serial=$payment_id";
        if ($connection->query($sql) === TRUE) {
            header("Location: customer.php?action=payments&id=$customer_id");
            exit;
        } else {
            // CC 09/29/26 Log error internally and show user-friendly message
            error_log("Database error: " . $sql . " - " . $connection->error);
            $error = "An error occurred while processing your request. Please try again.";
        }
    } elseif ($post_action === 'delete_payment') {
        $payment_id = (int)$_POST['id'];
        $customer_id = (int)$_POST['customer_serial'];
        $sql = "DELETE FROM payment WHERE payment_serial=$payment_id";
        if ($connection->query($sql) === TRUE) {
            header("Location: customer.php?action=payments&id=$customer_id");
            exit;
        } else {
            // CC 09/29/26 Log error internally and show user-friendly message
            error_log("Database error: " . $sql . " - " . $connection->error);
            $error = "An error occurred while processing your request. Please try again.";
        }
    }
}

// ACH 09/29/26 Get customer data for edit/delete/payments
$customer = null;
if (($action === 'edit' || $action === 'delete' || $action === 'payments') && $id > 0) {
    $sql = "SELECT * FROM customer WHERE customer_serial=$id";
    $result = $connection->query($sql);
    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    }
}

// CC 09/29/26 Get customers list with specified sort and order
function get_customers($conn, $sort, $order)
{
    // ACH 09/29/26 Build SQL query to select customer fields with sorting
    $sql = "SELECT customer_serial, first_name, last_name, city, phone FROM customer ORDER BY $sort $order";
    return $conn->query($sql);
}


function get_payments($conn, $customer_id, $sort, $order)
{
    // ACH 09/29/26 SQL query to select payment fields for customer with sorting
    $sql = "SELECT payment_serial, payment_date, payment_amount, description FROM payment WHERE customer_serial=$customer_id ORDER BY $sort $order";
    return $conn->query($sql);
}

// ACH 09/29/26 For payments list, get sort/order from GET or default
$pay_sort = isset($_GET['pay_sort']) ? $connection->real_escape_string($_GET['pay_sort']) : 'payment_date';
$pay_order = isset($_GET['pay_order']) && strtoupper($_GET['pay_order']) === 'DESC' ? 'DESC' : 'ASC';
$pay_next_order = ($pay_order === 'ASC') ? 'DESC' : 'ASC';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Customer Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            cursor: pointer;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            padding: 5px 10px;
            margin: 2px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            font-size: 14px;
        }

        .btn.back {
            background-color: #555;
        }

        .edit {
            background-color: #4CAF50;
        }

        .delete {
            background-color: #f44336;
        }

        .payments {
            background-color: #2196F3;
        }

        .add {
            background-color: #FF9800;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type=text],
        input[type=date],
        input[type=number] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }

        .back {
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <h1>Customer Management</h1>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <?php
    // ACH 09/29/26 Handle different actions based on GET parameter
    switch ($action) {
        case 'list':
    ?>
            <p>
                <a href="customer.php?action=add" class="btn add">Add Record</a>
            </p>
            <table>
                <thead>
                    <tr>
                        <th>
                            <a href="customer.php?sort=last_name&order=<?php echo $next_order; ?>">Last Name
                                <?php if ($sort === 'last_name'): ?>
                                    <?php echo $order === 'ASC' ? '▲' : '▼'; ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="customer.php?sort=first_name&order=<?php echo $next_order; ?>">First Name
                                <?php if ($sort === 'first_name'): ?>
                                    <?php echo $order === 'ASC' ? '▲' : '▼'; ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="customer.php?sort=city&order=<?php echo $next_order; ?>">City
                                <?php if ($sort === 'city'): ?>
                                    <?php echo $order === 'ASC' ? '▲' : '▼'; ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="customer.php?sort=phone&order=<?php echo $next_order; ?>">Phone
                                <?php if ($sort === 'phone'): ?>
                                    <?php echo $order === 'ASC' ? '▲' : '▼'; ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = get_customers($connection, $sort, $order);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                            echo "<td>";
                            echo "<a href='customer.php?action=edit&id=" . $row['customer_serial'] . "' class='btn edit'>Edit</a> ";
                            echo "<a href='customer.php?action=delete&id=" . $row['customer_serial'] . "' class='btn delete'>Delete</a> ";
                            echo "<a href='customer.php?action=payments&id=" . $row['customer_serial'] . "' class='btn payments'>Payments</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No customers found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php
            break;

        case 'add':
        ?>
            <h2>Add New Customer</h2>
            <form method="post" action="customer.php">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="street_address">Street Address:</label>
                    <input type="text" id="street_address" name="street_address">
                </div>
                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city">
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <input type="submit" value="Save">
                <a href="customer.php" class="btn back">Cancel</a>
            </form>
            <?php
            break;

        case 'edit':
            if ($customer) {
            ?>
                <h2>Edit Customer</h2>
                <form method="post" action="customer.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $customer['customer_serial']; ?>">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="street_address">Street Address:</label>
                        <input type="text" id="street_address" name="street_address" value="<?php echo htmlspecialchars($customer['street_address']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                    </div>
                    <input type="submit" value="Update">
                    <a href="customer.php" class="btn back">Cancel</a>
                </form>
            <?php
            } else {
                echo "<p>Customer not found.</p>";
            }
            break;

        case 'delete':
            if ($customer) {
            ?>
                <h2>Delete Customer</h2>
                <p>Are you sure you want to delete customer:</p>
                <p><strong><?php echo htmlspecialchars($customer['last_name'] . ', ' . $customer['first_name']); ?></strong></p>
                <form method="post" action="customer.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $customer['customer_serial']; ?>">
                    <input type="submit" value="Confirm Delete" class="btn delete">
                    <a href="customer.php" class="btn back">Cancel</a>
                </form>
            <?php
            } else {
                echo "<p>Customer not found.</p>";
            }
            break;

        case 'payments':
            if ($customer) {
            ?>
                <h2>Payments for <?php echo htmlspecialchars($customer['last_name'] . ', ' . $customer['first_name']); ?></h2>
                <p>
                    <a href="customer.php?action=add_payment&cid=<?php echo $customer['customer_serial']; ?>" class="btn add">Add Payment</a>
                    <a href="customer.php" class="btn back">Back to Customers</a>
                </p>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="customer.php?action=payments&id=<?php echo $customer['customer_serial']; ?>&pay_sort=payment_date&pay_order=<?php echo $pay_next_order; ?>">Payment Date
                                    <?php if ($pay_sort === 'payment_date'): ?>
                                        <?php echo $pay_order === 'ASC' ? '▲' : '▼'; ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="customer.php?action=payments&id=<?php echo $customer['customer_serial']; ?>&pay_sort=payment_amount&pay_order=<?php echo $pay_next_order; ?>">Amount
                                    <?php if ($pay_sort === 'payment_amount'): ?>
                                        <?php echo $pay_order === 'ASC' ? '▲' : '▼'; ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="customer.php?action=payments&id=<?php echo $customer['customer_serial']; ?>&pay_sort=description&pay_order=<?php echo $pay_next_order; ?>">Description
                                    <?php if ($pay_sort === 'description'): ?>
                                        <?php echo $pay_order === 'ASC' ? '▲' : '▼'; ?>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $presult = get_payments($connection, $customer['customer_serial'], $pay_sort, $pay_order);
                        if ($presult->num_rows > 0) {
                            while ($prow = $presult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($prow['payment_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($prow['payment_amount']) . "</td>";
                                echo "<td>" . htmlspecialchars($prow['description']) . "</td>";
                                echo "<td>";
                                echo "<a href='customer.php?action=edit_payment&pid=" . $prow['payment_serial'] . "&cid=" . $customer['customer_serial'] . "' class='btn edit'>Edit</a> ";
                                echo "<a href='customer.php?action=delete_payment&pid=" . $prow['payment_serial'] . "&cid=" . $customer['customer_serial'] . "' class='btn delete'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No payments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<p>Customer not found.</p>";
            }
            break;

        case 'add_payment':
            $customer_id = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;
            if ($customer_id > 0) {
                // verify customer exists
                $cust_sql = "SELECT * FROM customer WHERE customer_serial=$customer_id";
                $cust_res = $connection->query($cust_sql);
                if ($cust_res && $cust_res->num_rows > 0) {
                    $cust = $cust_res->fetch_assoc();
                ?>
                    <h2>Add Payment for <?php echo htmlspecialchars($cust['last_name'] . ', ' . $cust['first_name']); ?></h2>
                    <form method="post" action="customer.php">
                        <input type="hidden" name="action" value="add_payment">
                        <input type="hidden" name="customer_serial" value="<?php echo $customer_id; ?>">
                        <div class="form-group">
                            <label for="payment_date">Payment Date:</label>
                            <input type="date" id="payment_date" name="payment_date" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_amount">Amount:</label>
                            <input type="number" step="0.01" id="payment_amount" name="payment_amount" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" id="description" name="description">
                        </div>
                        <input type="submit" value="Save Payment">
                        <a href="customer.php?action=payments&id=<?php echo $customer_id; ?>" class="btn back">Cancel</a>
                    </form>
                <?php
                } else {
                    echo "<p>Customer not found.</p>";
                }
            } else {
                echo "<p>Invalid customer.</p>";
            }
            break;

        case 'edit_payment':
            $payment_id = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
            $customer_id = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;
            if ($payment_id > 0 && $customer_id > 0) {
                $sql = "SELECT * FROM payment WHERE payment_serial=$payment_id AND customer_serial=$customer_id";
                $result = $connection->query($sql);
                if ($result && $result->num_rows > 0) {
                    $payment = $result->fetch_assoc();
                ?>
                    <h2>Edit Payment</h2>
                    <form method="post" action="customer.php">
                        <input type="hidden" name="action" value="update_payment">
                        <input type="hidden" name="id" value="<?php echo $payment_id; ?>">
                        <input type="hidden" name="customer_serial" value="<?php echo $customer_id; ?>">
                        <div class="form-group">
                            <label for="payment_date">Payment Date:</label>
                            <input type="date" id="payment_date" name="payment_date" value="<?php echo htmlspecialchars($payment['payment_date']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_amount">Amount:</label>
                            <input type="number" step="0.01" id="payment_amount" name="payment_amount" value="<?php echo htmlspecialchars($payment['payment_amount']); ?>" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($payment['description']); ?>">
                        </div>
                        <input type="submit" value="Update Payment">
                        <a href="customer.php?action=payments&id=<?php echo $customer_id; ?>" class="btn back">Cancel</a>
                    </form>
                <?php
                } else {
                    echo "<p>Payment not found.</p>";
                }
            } else {
                echo "<p>Invalid request.</p>";
            }
            break;

        case 'delete_payment':
            $payment_id = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
            $customer_id = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;
            if ($payment_id > 0 && $customer_id > 0) {
                $sql = "SELECT * FROM payment WHERE payment_serial=$payment_id AND customer_serial=$customer_id";
                $result = $connection->query($sql);
                if ($result && $result->num_rows > 0) {
                    $payment = $result->fetch_assoc();
                ?>
                    <h2>Delete Payment</h2>
                    <p>Are you sure you want to delete this payment?</p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($payment['payment_date']); ?>,
                        <strong>Amount:</strong> <?php echo htmlspecialchars($payment['payment_amount']); ?>,
                        <strong>Description:</strong> <?php echo htmlspecialchars($payment['description']); ?>
                    </p>
                    <form method="post" action="customer.php">
                        <input type="hidden" name="action" value="delete_payment">
                        <input type="hidden" name="id" value="<?php echo $payment_id; ?>">
                        <input type="hidden" name="customer_serial" value="<?php echo $customer_id; ?>">
                        <input type="submit" value="Confirm Delete" class="btn delete">
                        <a href="customer.php?action=payments&id=<?php echo $customer_id; ?>" class="btn back">Cancel</a>
                    </form>
    <?php
                } else {
                    echo "<p>Payment not found.</p>";
                }
            } else {
                echo "<p>Invalid request.</p>";
            }
            break;

        default:
            // ACH 09/29/26 fallback to list
            header("Location: customer.php");
            exit;
    }
    ?>

</body>

</html>
<?php $connection->close(); ?>