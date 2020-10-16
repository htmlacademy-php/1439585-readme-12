<!DOCTYPE html>
<html>
  <head>
    <title>Моё резюме</title>
 <style>
      .important {
        color: green;
        font-style: italic;
      }
    </style>
  </head>
  <body>
<?php
    $year_cur = 1920; // начальный год
    $year_end = 2015;  // последний год
?>

<select name="year">
    <?php while ($year_cur <= $year_end): ?>
    <option value="<?=$year_cur;?>"><?=$year_cur;?></option>
    <?php $year_cur++; ?>
    <?php endwhile; ?>
</select>
  </body>
</html>