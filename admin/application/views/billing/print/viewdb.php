<html>

<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <table class="table table-bordered">
        <thead>
                       <tr>
                <?php foreach($data[0] as $key => $item){
                    echo '<td>'.$key.'</td>';
                }?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data as  $items){ ?>

            <tr>
            <?php foreach($items as $datas){
                    echo '<td>'.$datas.'</td>';
                }?>
            </tr>

              <?php  }?>
        </tbody>

    </table>
</body>

</html>

<style>
    table thead {
  position: sticky;
}
</style>