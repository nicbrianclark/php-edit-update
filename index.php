<?php
require_once('./db.php');

if(isset($_POST['submit'])) {
    // get POST information
    $id = $_POST['id_to_edit'];
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $rating = floatval($_POST['rating']);
    
    // set up form errors
    $errors = [
        "name" => "",
        "brand" => "",
        "type" => "",
        "rating" => ""
    ];

    // check for empty fields, add to errors array
    if(empty($name)) {
        $errors['name'] = "You must include a name";
    }
    if(empty($brand)) {
        $errors['brand'] = "You must include a brand";
    }
    if(empty($type)) {
        $errors['type'] = "You must include a type";
    }
    if(empty($rating)) {
        $errors['rating'] = "You must include a rating.";
    } else {
        $number_pattern = "/^[\"0-9.]$/";
        if (!preg_match($number_pattern, $rating)) {
            $errors['rating'] = "Rating must be a number";
        }
    }


    // check for empty errors array
    if (!array_filter($errors)) {

        // update database record with new info
        $update_sql = "UPDATE edititems SET name = '$name', brand = '$brand', type = '$type', rating =' $rating' WHERE id = '$id'";        

        // "UPDATE edititems SET name = $name, brand = $brand, type = $type, rating = $rating WHERE id = $id"
        
        mysqli_query($conn, $update_sql);
    }

    


}

if (isset($_GET['id'])) {
    $candyId = $_GET['id'];

    $sql = "SELECT id, name, brand, type, rating FROM edititems WHERE id=$candyId";
    $result = mysqli_query($conn, $sql);

    $candy = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $sql = "SELECT id, name, brand, type, rating FROM edititems";
    $result = mysqli_query($conn, $sql);

    $candy = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candy Ratings</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Emilys+Candy&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background-image: url('./assets/chocolate-texture.webp');
            background-repeat: repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: Helvetica, Arial, sans-serif;
        }
        main {
            background: white;
            padding: 4rem;
            box-shadow: 0 12px 24px rgba(0,0,0,0.5);
        }
        form {
            display: grid;
            gap: 1rem;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        h1 {
            font-family: "Emilys Candy", serif;
            font-weight: 400;
            font-style: normal;
            color: #3b2d9b;
        }
        input:not([type="submit"]) {
            min-height: 32px;
            border: 1px solid #ff66cc;
            text-indent: .5rem;
        }
        label {
            color: #4b3832;
        }
        input[type="range"] {
            accent-color: #ff66cc;
        }
        input[type="submit"] {
            border: none;
            padding: 1rem;
            background-color: #3b2d9b;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 16px;
            font-family: "Emilys Candy", serif;
            font-style: normal;
            letter-spacing: 8px;
        }
        .errors {
            color: red;
        }
    </style>
</head>
<body>
    <main>
        <h1>Update Candy Rating</h1>
        <?php if (isset($_GET['id'])) : ?>
            <div class="current-candy">
            <h2><?php echo $candy[0]['name']; ?></h2>
            <ul>
                <li><?php echo $candy[0]['brand']; ?></li>
                <li><?php echo $candy[0]['type']; ?></li>
                <li><?php echo $candy[0]['rating']; ?></li>
            </ul>
        </div>
        <form action="./index.php?id=<?php echo $candyId; ?>" method="POST">
            <input type="hidden" name="id_to_edit" value="<?php echo $candyId; ?>" />

            <div class="input-group">
                <label for="name">Candy Name</label>
                <input type="text" name="name" id="name" value="<?php if (isset($candy[0]['name'])) { echo $candy[0]['name']; } ?>">
                
                <?php if (!empty($errors['name'])) {
                    echo "<p class='errors'>" . $errors['name'] . "</p>"; }
                ?>
            </div>

            <div class="input-group">
                <label for="brand">Brand</label>
                <input type="text" name="brand" id="brand" value="<?php if (isset($candy[0]['brand'])) { echo $candy[0]['brand']; } ?>">

                <?php if (!empty($errors['brand'])) {
                    echo "<p class='errors'>" . $errors['brand'] . "</p>"; }
                ?>
            </div>

            <div class="input-group">
                <label for="type">Candy Type</label>
                <input type="text" name="type" id="type" value="<?php if (isset($candy[0]['type'])) { echo $candy[0]['type']; } ?>">

                <?php if (!empty($errors['type'])) {
                    echo "<p class='errors'>" . $errors['type'] . "</p>"; }
                ?>
            </div>

            <div class="input-group">
                <label for="rating">Rating</label>
                <input type="range" name="rating" id="rating" min="1" max="5" step="1" value="<?php if (isset($candy[0]['rating'])) { echo $candy[0]['rating']; } ?>">
                <div>
                    <p>Current Rating: <span id="currentRating"></span></p>
                </div>

                <?php if (!empty($errors['rating'])) {
                    echo "<p class='errors'>" . $errors['rating'] . "</p>"; }
                ?>
            </div>

            <div class="input-group">
                <input type="submit" name="submit" value="Submit">
            </div>
        </form>
        <?php else : ?>
            <h2>No Candy Selected. Add an ID query parameter.</h2>
            <p>Current IDs available (You don't have to do this, but it's good UX!):
                <ul>
                    <?php foreach ($candy as $item) : ?>
                        <li><?php echo $item['id']; ?></li>
                    <?php endforeach ?>
                </ul>
            </p>
        <?php endif ?>

    </main>
    
    <script>
        const updateRating = () => {
            const currentRating = document.querySelector('#rating').value;
            const ratingContainer = document.querySelector('#currentRating');
            ratingContainer.textContent = currentRating;
        }
        updateRating();

        const ratingSlider = document.querySelector('#rating');
        ratingSlider.addEventListener('change', function(){ updateRating() });
        
    </script>
</body>
</html>