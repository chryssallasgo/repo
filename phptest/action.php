<?php
// CC 09/29/26 Display submitted name and age with proper escaping
// CC 09/29/26 htmlspecialchars prevents XSS, (int) ensures age is numeric
?>
Hi <?php echo htmlspecialchars($_POST['name']); ?>.
You are <?php echo (int) $_POST['age']; ?> years old.