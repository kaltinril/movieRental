<?php //managemovies.php
  require_once 'login.php';
  $db_server = mysql_connect($db_hostname, $db_username, $db_password);
  
  if (!$db_server) die("Unable to connect to MySQL: " .mysql_error());
  
  mysql_select_db($db_database, $db_server)
    or die("Unable to select database: " . mysql_error());
  
  //Get the Genre into the drop down list
  $query = "SELECT GenreID, Name FROM tblGenre";
  $genreDDL = create_ddl('GenreID',$query);
  
  //Delete a record if they clicked delete
  if (isset($_POST['delete']) && isset($_POST['MovieID']))
  {
    $MovieID = get_post('MovieID');
    $query = "DELETE FROM tblMovie WHERE MovieID = $MovieID";
    
    if (!mysql_query($query, $db_server))
      echo "DELETE failed: $query<br />" . mysql_error() . "<br /><br />";
  }
  
  //Add a record if they clicked add
  if (isset($_POST['Title']) &&
      isset($_POST['RunLength']) 
      )
  {
    $Title       = get_post('Title');
    $RunLength   = get_post('RunLength');
    $ReleaseDate = get_post('ReleaseDate');
    $GenreID     = get_post('GenreID');
    $Synopsis    = get_post('Synopsis');
    
    $query = "INSERT INTO tblMovie VALUES" .
             "(null,'$Title',$RunLength,'$ReleaseDate',$GenreID, '$Synopsis')";
    
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
<form action="managemovies.php" method="post">
<pre>
         Title <input type="text" name="Title" />
Running Length <input type="text" size="3" name="RunLength" /> (Enter the number of minutes)
  Release Date <input type ="text" name="ReleaseDate" /> (Format: YYYY-MM-DD)
         Genre $genreDDL
      Synopsis <textarea rows="5" cols="40" name="Synopsis" />Short Description of the Movie goes here.</textarea>
               <input type="submit" value="ADD RECOD" />
<hr />
</pre></form>
_END;

$query = "SELECT M.MovieID, M.Title, M.RunningLength, M.ReleaseDate, G.Name, M.Synopsis FROM tblMovie M ";
$query .= "INNER JOIN tblGenre G ON G.GenreID = M.GenreID";
$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);

for($j = 0 ; $j < $rows ; ++$j)
{
  $row = mysql_fetch_row($result);
  echo <<<_END
  
<pre>
       MovieID: $row[0]
         Title: $row[1]
Running Length: $row[2]
  Release Date: $row[3]
         Genre: $row[4]
      Synopsis: <div class="wraptext"><p class="wraptext">$row[5]</p></div>
</pre>
<form action="managemovies.php" method="post">
  <input type="hidden" name="delete" value="yes" />
  <input type="hidden" name="MovieID" value="$row[0]" />
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

//Function:	create_ddl
//Purpose:	Create an HTML Drop Down List
//Requires:	Input of a Select Statement with ID column, Name Column
//		Current Connection to MySQL database
//Example:	$query = "SELECT ID, Name FROM lookup_table";
//Usage:	$HTML = create_ddl($query);
function create_ddl($ddl_name, $var)
{
  $result = mysql_query($var);
  if (!$result) die ("Database access failed: " . mysql_error());
  $rows = mysql_num_rows($result);

  $html_ddl = '<select name="' . $ddl_name . '" size="1">';
  for($j = 0 ; $j < $rows ; ++$j)
  {
    $row = mysql_fetch_row($result);
    $html_ddl .= '<option value='.$row[0].'>'.$row[1].'</option>';
  }
  $html_ddl .= '</select>';
  return $html_ddl;
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