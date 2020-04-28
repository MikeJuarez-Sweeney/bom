
<?php
// set the current page to one of the main buttons
$nav_selected = "ADMIN";
// make the left menu buttons visible; options: YES, NO
$left_buttons = "YES";
// set the left menu button selected; options will change based on the main selection
$left_selected = "ADMIN";
include("./nav.php");

if(!isset($_SESSION)){
    session_start();
}
?>

<html>

<head>
<style>
table.center {
    margin-left:auto;
    margin-right:auto;
  }
#list ul {
  display: inline-block;
  text-align: left;
}

select {
    display: block;
}

.group {
  width: 250px;
  display: inline-block;
}

#importform {
  margin-top: 1rem;
  background: #acdff2;
  padding: 2rem;
  width: 900px;
}

#importform button {
  margin-top: 3rem;
  display: block;
  background: #01B0F1;
  color: white;
  padding: .5rem 1rem;
  border: solid #ccc 1px;
  border-radius: 4px;
}
</style>

<!--JavaScript to disable and enabled selected options to prevent duplicate selections from form-->
<script>
$(document).ready(function() {
  $("select").change(function()
  {
      $("select option").prop("hidden",""); //enable everything

      //collect the values from selected;
      var  arr = $.map
      (
          $("select option:selected"), function(n)
          {
              return n.value;
          }
      );

      //disable elements
      $("select option").filter(function()
      {
          return $.inArray($(this).val(),arr)>-1; //if value is in the array of selected values
      }).prop("hidden","hidden");

      //re-enable elements
      $("select option").filter(function()
      {
          return $.inArray($(this).val(),arr)==-1; //if value is not in the array of selected values
      }).prop("hidden","");
  }).trigger("change"); // Trigger the change function on page load. Useful if select values are pre-selected.
});
</script>
</head>

<body>
  <h2 style = "color: #01B0F1;">Admin --> Import BOM</h2>
  <div id='list'>
    <p>Before importing a file, please make sure the file is a <span style="font-weight: bold;">CSV</span>
    file with these <span style="font-weight: bold;">15 </span>columns:<br></p>
    <ul>
            <li>App ID</li>
            <li>App Name</li>
            <li>App Version</li>
            <li>Component ID</li>
            <li>Component name</li>
    </ul>
    <ul>
            <li>Component Version</li>
            <li>Component Type</li>
            <li>App Status</li>
            <li>Component Status</li>
            <li>Request ID</li>
    </ul>
    <ul>
            <li>Request Date</li>
            <li>Request Status</li>
            <li>Request Step</li>
            <li>Requestor</li>
            <li>Notes</li>
    </ul>
  </div>
    <form enctype="multipart/form-data" method="POST" role="form">
      <input type="file" name="file" id="file" size="150" style="color:black; display: inline-block;">
      <button style="background: #01B0F1; color: white;" type="submit"
      class="btn btn-default" name="submit" value="submit">Submit</button>
    </form>
</body>
</html>

<?php
/*if(!isset($_SESSION)){
    session_start();
}*/

$c = 0;

$labels = array('app_id', 'app_name', 'app_version', 'cmp_id', 'cmp_name', 'cmp_version',
'cmp_type', 'app_status', 'cmp_status', 'request_id', 'request_date', 'request_status', 'request_step',
'notes', 'requestor');
$data = array();
$map = array();

if (isset($_POST['submit'])) {
  $chkfile = $_FILES['file']['name'];
  $file = $_FILES['file']['tmp_name'];

  //if user clicks button with no file uploaded
  if(!file_exists($file)) {
    echo "<p style='color: white; background-color: red; font-weight: bold; width: 500px;
    text-align: center; border-radius: 2px;'>NO FILE WAS SELCTED</p>";

  }else {
    $extension = 'csv';
    $file_ext = pathinfo($chkfile);

    //if the uploaded file is not a csv file
    if($file_ext['extension'] !== $extension) {
      echo "<p style='color: white; background-color: red; font-weight: bold; width: 500px;
      text-align: center; border-radius: 2px;'>PLEASE SELECT AN CSV FILE</p>";

    }else {
      $target_dir = "csv files/";
      $target_file = $target_dir.basename($_FILES["file"]["name"]);
      move_uploaded_file($file,$target_file);
      $_SESSION["the_file"] = $target_file;
      $handle = fopen($target_file, "r");

      if(FALSE !== $handle) {
          $row = fgetcsv($handle, 1000, ',');
          if(count($row) < 15) {
            echo "<p style='color: white; background-color: red; font-weight: bold; width: 500px;
            text-align: center; border-radius: 2px;'>FILE CAN'T HAVE LESS THAN 15 COLUMNS</p>";
          }else {
            function dropdown($row) {
              foreach($row as $val) {
                  echo '<option value="'.$val.'">'.$val.'</option>';
              }
            }
            include('import_form.php');
          }
      }
    }
  }
}
 ?>

 <?php
 if (isset($_POST['submitform'])) {
   $app_id_col = $_POST['app_id'];
   $app_name_col = $_POST['app_name'];
   $app_version_col = $_POST['app_version'];
   $cmp_id_col = $_POST['cmp_id'];
   $cmp_name_col = $_POST['cmp_name'];
   $cmp_version_col = $_POST['cmp_version'];
   $cmp_type_col = $_POST['cmp_type'];
   $app_status_col = $_POST['app_status'];
   $cmp_status_col = $_POST['cmp_status'];
   $request_id_col = $_POST['request_id'];
   $request_date_col = $_POST['request_date'];
   $request_status_col = $_POST['request_status'];
   $request_step_col = $_POST['request_step'];
   $requestor_col = $_POST['requestor'];
   $notes_col = $_POST['notes'];
   $target_file = $_SESSION["the_file"];

   $headers = array($app_id_col, $app_name_col, $app_version_col, $cmp_id_col, $cmp_name_col,
   $cmp_version_col, $cmp_type_col, $app_status_col, $cmp_status_col, $request_id_col, $request_date_col,
   $request_status_col, $request_step_col, $notes_col, $requestor_col);

   $data = array();
   $map = array();

   //get data
   $handle = fopen($target_file, "r");
   if(FALSE !== $handle) {
       $row = fgetcsv($handle, 1000, ',');

       //get column labels
       foreach($headers AS $header) {
         $index = array_search(strtolower($header), array_map('strtolower', $row));
         if(FALSE !== $index) {
           $map[$index] = $header;
         }
       }

       }

   while($data1 = fgetcsv($handle, 1000, ',')) {
     $row = array();
     foreach($map as $index => $field) {
       $row[$field] = $data1[$index];
     }
       $data[] = $row;
     }

     if(empty($data)) {
       echo "EMPTY";

     }else {
       //delete existing data in table
       $sqlDelete = "DELETE FROM sbom";
       mysqli_query($db, $sqlDelete);

       //insert data into database
       $sqlinsert = $db->prepare('INSERT INTO sbom (app_id, `app_name`, app_version, cmp_id,
         cmp_name, cmp_version, cmp_type, app_status, cmp_status, request_id, request_date,
         request_status, request_step, notes, requestor) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

       $sqlinsert->bind_param('sssssssssssssss', $app_id, $app_name, $app_version,
         $cmp_id, $cmp_name, $cmp_version, $cmp_type, $app_status, $cmp_status, $request_id,
         $request_date, $request_status, $request_step, $notes, $requestor);

         foreach ($data as $row) {
               $app_id = $row[$app_id_col];
               $app_name = $row[$app_name_col];
               $app_version = $row[$app_version_col];
               $cmp_id = $row[$cmp_id_col];
               $cmp_name = $row[$cmp_name_col];
               $cmp_version = $row[$cmp_version_col];
               $cmp_type = $row[$cmp_type_col];
               $app_status = $row[$app_status_col];
               $cmp_status = $row[$cmp_status_col];
               $request_id = $row[$request_id_col];
               $request_date = $row[$request_date_col];
               $request_date = strtotime($request_date);
               $request_date = date('Y/m/d', $request_date);
               $request_status = $row[$request_status_col];
               $request_step = $row[$request_step_col];
               $notes = $row[$notes_col];
               $requestor = $row[$requestor_col];
               $sqlinsert->execute();
         }
         if(!$sqlinsert->execute()) {
           echo '<p style="background: red; color: white; font-size: 2rem;">ERROR: '.$db->error.'</p>';
         }else {
           echo "<p style='color: white; background-color: green; font-weight: bold; width: 500px;
           text-align: center; border-radius: 2px;'>IMPORT SUCCESSFUL";
           echo "<br>".count($data)." rows have been successfully imported into the sbom table.</p>";
         }
     }

   }
  ?>
