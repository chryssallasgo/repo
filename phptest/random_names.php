<!DOCTYPE html>
<html>

<head>
    <title>Random Name Matcher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 200px;
            padding: 5px;
            margin-right: 10px;
        }

        .inputs {
            display: flex;
            gap: 10px;
            align-items: end;
        }

        .inputs div {
            flex: 1;
        }

        button {
            background: #007cba;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #005a87;
        }

        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .no-data {
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Random Name Matcher</h1>

        <?php
        // CC 09/29/26 Process form submission for random name matching
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // CC 09/29/26 Retrieve first and last names from form inputs
            $first_name = [];
            $last_name = [];

            for ($i = 1; $i <= 10; $i++) {
                $first_name_value = trim($_POST["first_name_$i"] ?? '');
                $last_name_value = trim($_POST["last_name_$i"] ?? '');

                // CC 09/29/26 Only add pair if both first and last names are provided
                if ($first_name_value !== '' && $last_name_value !== '') {
                    $first_name[] = $first_name_value;
                    $last_name[] = $last_name_value;
                }
            }

            // CC 09/29/26 Ensure we have exactly 10 pairs (pad with empty strings if needed)
            while (count($first_name) < 10) {
                $first_name[] = '';
                $last_name[] = '';
            }

            // CC 09/29/26 Truncate arrays to 10 pairs if more were submitted
            $first_name = array_slice($first_name, 0, 10);
            $last_name = array_slice($last_name, 0, 10);

            // CC 09/29/26 Create copies for shuffling to preserve original order
            $shuffled_first = $first_name;
            $shuffled_last = $last_name;

            // CC 09/29/26 Shuffle the copies independently to create random pairs
            shuffle($shuffled_first);
            shuffle($shuffled_last);

            // CC 09/29/26 Display the randomly matched first and last names
            echo "<div class='result'>";
            echo "<h2>Randomly Matched Names</h2>";
            if (count($shuffled_first) > 0) {
                echo "<table>";
                echo "<tr><th>First Name</th><th>Last Name</th></tr>";
                for ($i = 0; $i < count($shuffled_first); $i++) {
                    if ($shuffled_first[$i] !== '' && $shuffled_last[$i] !== '') {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($shuffled_first[$i]) . "</td>";
                        echo "<td>" . htmlspecialchars($shuffled_last[$i]) . "</td>";
                        echo "</tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p class='no-data'>No names to display</p>";
            }
            echo "</div>";

            // CC 09/29/26 Display pairs where first name precedes or equals last name alphabetically
            echo "<div class='result'>";
            echo "<h2>Names Where First Name ≤ Last Name (Alphabetically)</h2>";
            $alpha_pair = [];
            for ($i = 0; $i < count($shuffled_first); $i++) {
                $first = $shuffled_first[$i];
                $last = $shuffled_last[$i];
                if ($first !== '' && $last !== '' && strtolower($first) <= strtolower($last)) {
                    $alpha_pair[] = [$first, $last];
                }
            }

            if (count($alpha_pair) > 0) {
                echo "<table>";
                echo "<tr><th>First Name</th><th>Last Name</th></tr>";
                foreach ($alpha_pair as $pair) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pair[0]) . "</td>";
                    echo "<td>" . htmlspecialchars($pair[1]) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='no-data'>No names meet this criteria</p>";
            }
            echo "</div>";

            // CC 09/29/26 Display each last name with its ASCII character sum
            echo "<div class='result'>";
            echo "<h2>Last Names with ASCII Value Sum</h2>";
            if (count($shuffled_last) > 0) {
                echo "<table>";
                echo "<tr><th>Last Name</th><th>ASCII Sum</th></tr>";
                foreach ($shuffled_last as $last_name_value) {
                    if ($last_name_value !== '') {
                        $ascii_sum = 0;
                        for ($j = 0; $j < strlen($last_name_value); $j++) {
                            $ascii_sum += ord($last_name_value[$j]);
                        }
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($last_name_value) . "</td>";
                        echo "<td>" . $ascii_sum . "</td>";
                        echo "</tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p class='no-data'>No last names to display</p>";
            }
            echo "</div>";
        } else {
            // CC 09/29/26 Display the input form for first and last names
        ?>
            <form method="post" action="">
                <div class="form-group">
                    <label>Enter 10 First Names and Last Names:</label>
                    <div class="inputs">
                        <div><strong>First Name</strong></div>
                        <div><strong>Last Name</strong></div>
                    </div>
                    <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo "<div class='inputs'>";
                        echo "<div><input type='text' name='first_name_$i' placeholder='First name $i' required></div>";
                        echo "<div><input type='text' name='last_name_$i' placeholder='Last name $i' required></div>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <button type="submit">Submit</button>
            </form>
        <?php
        }
        ?>
    </div>
</body>

</html>