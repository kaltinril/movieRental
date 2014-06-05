<?php //managemovies.php
  require_once 'login.php';
  $db_server = mysql_connect($db_hostname, $db_username, $db_password);
  
  if (!$db_server) die("Unable to connect to MySQL: " .mysql_error());
  
  mysql_select_db($db_database, $db_server)
    or die("Unable to select database: " . mysql_error());
  
  //Get the Genre into the drop down list
  $query = "SELECT GenreID, Name FROM tblGenre";
  $genreDDL = create_ddl('GenreID',$query);
  
  //Search for a record if they clicked search
  if (isset($_POST['chk_search']) )
  {
    $chk_in = $_POST['chk_search'];
    $Title       = get_post('Title');
    $RunLength   = (int)get_post('RunLength');
    $ReleaseDate = get_post('ReleaseDate');
    $GenreID     = get_post('GenreID');
    $Synopsis    = get_post('Synopsis');
    
    $query = "SELECT * " .
             "FROM tblMovie M " .
             "WHERE 1=1 ";
             
    foreach ($chk_in as $i => $value)
    {
      if ($chk_in[$i] == 'Title' && isset($_POST['Title']))
        $query .= " AND M.Title like '%$Title%' ";
      elseif ($chk_in[$i] == 'RunLength' && isset($_POST['RunLength']))
        $query .= " AND M.RunningLength = $RunLength ";
      elseif ($chk_in[$i] == 'ReleaseDate' && isset($_POST['ReleaseDate']))
        $query .= " AND M.ReleaseDate = '$ReleaseDate' ";
      elseif ($chk_in[$i] == 'Genre' && isset($_POST['GenreID']))
        $query .= " AND M.GenreID = $GenreID ";
      elseif ($chk_in[$i] == 'Synopsis' && isset($_POST['Synopsis']))
        $query .= " AND M.Synopsis like '%$Synopsis%' ";
    }
    
    $htmloutput = table_query_output($query);
  }
  else
    $htmloutput = "";
    
  //HTML General
echo <<<_END
    <html>
      <head>
        <link rel="stylesheet" type="text/css" href="generic.css" />
        <title>Movie Rental - Search Movies</title>
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
          <h1> Search Movies </h1>
        </center>
_END;

  //HTML Form
echo <<<_END
<form action="searchmovies.php" method="post">
<pre>
         Title <input type="checkbox" name="chk_search[]" value="Title" /><input type="text" name="Title" />
Running Length <input type="checkbox" name="chk_search[]" value="RunLength" /><input type="text" size="3" name="RunLength" /> (Enter the number of minutes)
  Release Date <input type="checkbox" name="chk_search[]" value="ReleaseDate" /><input type ="text" name="ReleaseDate" /> (Format: YYYY-MM-DD)
         Genre <input type="checkbox" name="chk_search[]" value="Genre" />$genreDDL
      Synopsis <input type="checkbox" name="chk_search[]" value="Synopsis" /><textarea rows="5" cols="40" name="Synopsis" />Short Description of the Movie goes here.</textarea>
               <input type="submit" value="Search" />
<hr />
</pre></form>
	<p>$htmloutput</p>
      </body>
    </html>
_END;

mysql_close($db_server);

//********************************************
//     FUNCTIONS
//********************************************

function get_post($var)
{
  return sanitizeMySQL($_POST[$var]);
}

function table_query_output($var)
{
    $result = mysql_query($var);
  
    if (!$result) die("Database access failed: " . mysql_error());
  
    //Display results on screen
    $rows = mysql_num_rows($result);
    $cols = mysql_num_fields($result);
    
    if ($rows >0)
    {
      //Create Header Row
      $reportoutput = "<table border='1'><tr>";
      
      for ($i = 0; $i < $cols; ++$i)
      {
        $reportoutput .= "<th>" . mysql_field_name($result,$i) . "</th>";
      }
      
      $reportoutput .= "</tr>";
      
      //Add rows/records
      for ($j = 0; $j < $rows ; ++$j)
      {
        $reportoutput .= "<tr>";
        $row = mysql_fetch_row($result);
        for ($i = 0;$i < $cols; ++$i)
        {
        	$reportoutput .= "<td>" . $row[$i] . "</td>";
        }
        $reportoutput .= "</tr>";
      }
      $reportoutput .= "</table>";
    }
    else
      $reportoutput = "No Results, try using less information in your search.";

  return $reportoutput;
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