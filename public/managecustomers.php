<?php //managecustomers.php
  require_once 'login.php';
  $db_server = mysql_connect($db_hostname, $db_username, $db_password);
  
  if (!$db_server) die("Unable to connect to MySQL: " .mysql_error());
  
  mysql_select_db($db_database, $db_server)
    or die("Unable to select database: " . mysql_error());
  
  //Delete a record if they clicked delete
  if (isset($_POST['delete']) && isset($_POST['CustomerID']))
  {
    $CustomerID = get_post('CustomerID');
    $query = "DELETE FROM tblCustomer WHERE CustomerID = $CustomerID";
    
    if (!mysql_query($query, $db_server))
      echo "DELETE failed: $query<br />" . mysql_error() . "<br /><br />";
  }
  
  //Add a record if they clicked add
  if (isset($_POST['FName']) &&
      isset($_POST['LName']) &&
      isset($_POST['BirthDate'])
      )
  {
    $FName       = get_post('FName');
    $MName       = get_post('MName');
    $LName       = get_post('LName');
    $BirthDate   = get_post('BirthDate');
    
    $query = "INSERT INTO tblCustomer VALUES" .
             "(null,'$FName','$MName','$LName','$BirthDate')";
    
    if (!mysql_query($query, $db_server))
      echo "INSERT failed: $query<br />" . mysql_error() . "<br /><br />";
  }
  
  //HTML General
echo <<<_END
    <html>
      <head>
        <link rel="stylesheet" type="text/css" href="generic.css" />
        <title>Movie Rental - Movie Management</title>
      </head>
      <body>
        <center>
          <a href='mainpage.php'>Main Page</a> |
          <a href='rent.php'>Rent</a> |
          <a href='return.php'>Return</a> |
          <a href='searchmovies.php'>Search Movies</a> |
          <a href='reports.php'>Reports</a> |
          <a href='managemovies.php'>Manage Movies</a> |
          <a href='managecustomers.php'>Manage Customers</a> |
          <a href='manageaccounts.php'>Manage Accounts</a><br />
        </center>
_END;

  //HTML Form
echo <<<_END
<form action="managecustomers.php" method="post">
<pre>
    First Name <input type="text" name="FName" />
   Middle Name <input type="text" name="MName" />
     Last Name <input type="text" name="LName" />
    Birth Date <input type="text" name="BirthDate" /> (Format: YYYY-MM-DD)
               <input type="submit" value="ADD RECOD" />
<hr />
</pre></form>
_END;

$query = "SELECT C.* FROM tblCustomer C ";

$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);

for($j = 0 ; $j < $rows ; ++$j)
{
  $row = mysql_fetch_row($result);
  echo <<<_END
  
<pre>
   Customer ID: $row[0]
    First Name: $row[1]
   Middle Name: $row[2]
     Last Name: $row[3]
    Birth Date: $row[4]
</pre>
<form action="managecustomers.php" method="post">
  <input type="hidden" name="delete" value="yes" />
  <input type="hidden" name="CustomerID" value="$row[0]" />
  <input type="submit" value="DELETE RECORD" />
</form>
_END;
}

echo <<<_END
      </body>
    </html>
_END;

mysql_close($db_server);

function get_post($var)
{
  return sanitizeMySQL($_POST[$var]);
}


function sanitizeString($var)
{
  $var = stripslashes($var);
  $var = htmlentities($var);
  $var = strip_tags($var);
  return $var;
}

function sanitizeMySQL($var)
{
  $var = mysql_real_escape_string($var);
  $var = sanitizeString($var);
  return $var;
}


?>