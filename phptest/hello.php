<!-- CC 09/29/26 Simple form to submit name and age to action.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Hello Form</title>
</head>
<body>
    <form action="action.php" method="post">
        <label for="name">Your name:</label>
        <input name="name" id="name" type="text">

        <label for="age">Your age:</label>
        <input name="age" id="age" type="number">

        <button type="submit">Submit</button>
    </form>
</body>
</html>