<?php
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {
        var_dump($_FILES);
        // if (  ) 
        // {
        //     # code...
        // }

        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        var_dump($extension);

        if ( $extension !== "jpg" && $extension !== "jpeg" && $extension !== "png" && $extension !== "webp"  ) 
        {
            $errors['image'] = "Le format n'est pas valide.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="image">
            <input type="submit">
        </form>
    </body>
</html>