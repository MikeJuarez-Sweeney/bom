<?php
  $nav_selected = "SETUP";
  $left_buttons = "YES";
  $left_selected = "";
  $newTag = "";
  include("./nav.php");
  
  function updateTags($db)
{
    global $newTag;
    $sql = "UPDATE scope_preferences
            SET default_scope = '$newTag'
            WHERE preference_id = 0;";
    $result = $db->query($sql);
    echo "You selected ".$_POST["tag"];
}
if(isset($_POST['submit']))
{
   $newTag = $_POST["tag"];
   updateTags($db);
} 
 ?>

 <div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Setup --> </h3>

    </div>
</div>

<html>
<body>
Change Scope Preferences <br>
The scope must be a single string with no spaces or special characters
other than commas used for delmiting. E.g "active,special,released" <br><br>

<form action="setup.php" method="post">
Scope: <input type="text" name="tag"><br>
<input type="submit" value="update" name='submit'>
</form>
</body>
</html>

<?php include("./footer.php"); ?>