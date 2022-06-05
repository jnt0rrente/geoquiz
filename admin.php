<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GeoQuiz - Economic geology</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="admin, admin panel, upload">
    <meta name="description" content="Administration tab for the GeoQuiz site." />
    <meta name="viewport" content="width=device-width, initial scale=1.0">

    <link rel="stylesheet" type="text/css" href="style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/admin.js"></script>
</head>

<body>
    <h1>
        GeoQuiz
    </h1>
    <nav>
        <a href="index.html" accesskey="w" tabindex="1">Welcome</a>
        <a href="history.html" accesskey="h" tabindex="2">Historical geology</a>
        <a href="mineralogy.html" accesskey="c" tabindex="3">Mineralogy</a>
        <a href="economic.html" accesskey="e" tabindex="4">Economic geology</a>
        <a href="admin.php" accesskey="a" tabindex="5">Administration</a>
        <a href="quizzes.php" accesskey="q" tabindex="6">Quizzes</a>
    </nav>

    <?php

    echo "  <h2>Admin panel</h2>
            <section>
                <h3>Upload forml xml</h3>
                <p>After defining a forml file, you can use this utility to upload it as a public quiz on this website.</p>
                <input type='file' accept='text/xml' onchange='uploadManager.read(this.files)' />
                <input type='button' name='uploadFile' value='Subir archivo' onclick='uploadManager.upload()' /> 
                <p>Upload status: </p>";

    if (isset($_POST["inputFile"])) {
        $inputFile = $_POST["inputFile"];
        $newTitle = $_POST["newTitle"];
        echo "<p>File uploaded!</p>";
        echo "<p>Your new quiz is titled \" $newTitle \"";
    } 

    echo "</section>";

    

    ?>
</body>

</html>