<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Educo</title>
		<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.jqueryui.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.3/css/scroller.jqueryui.min.css">
	</head>
	<body>
    <h1>Cat Makeup Data from DB</h1>
    <h3><a href="quickstart.php?import=true">New Import</a> | <a href="logout.php">Logout</a></h3>
    <table id="example" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>FUll Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM emails";
          $result = $link->query($sql);

          if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
              echo "<tr><td>" . $row["Id"]. " </td><td> " . $row["full_name"]. "</td><td> " . $row["email"]. "</td></tr>";
            }
            }
           ?>
        </tbody>
    </table>
    <script type="text/javascript">
    $(document).ready(function () {
        $('#example').DataTable({
            "processing": true,
            "info": true,
            "stateSave": true,

        });
    });
    </script>

  </body>
</html>
