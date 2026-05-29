<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>

    @page{
        size: 130px 130px;
        margin:10px;
    }
    /* Define a class for the square label */
    .square-label {
      width: 110px; /* Set the width of the square */
      height: 110px; /* Set the height of the square */
      text-align: center;
    }
  </style>
</head>
<body>

  <!-- Use the square-label class for your label -->
  <div class="square-label">
    <img src="<?php echo $img[0]['src']; ?>" alt="Your Image" style="max-width: 100%; max-height: 100%;"><br>
    <label style="font-size:10px; font-weight:bold; text-align:center;"><?php echo $img[0]['name']; ?></label>

  </div>

</body>
</html>
