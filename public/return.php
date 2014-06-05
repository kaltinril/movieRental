<?php //return.php
  require_once 'login.php';
  $db_server = mysql_connect($db_hostname, $db_username, $db_password);
  
  if (!$db_server) die("Unable to connect to MySQL: " .mysql_error());
  
  mysql_select_db($db_database, $db_server)
    or die("Unable to select database: " . mysql_error());

  //Update Rental History
  if (isset($_POST['MovieID']) && 
      isset($_POST['CopyValue']) && 
      isset($_POST['AccountCustomerID']) &&
      isset($_POST['DateRented'])
     )
  {
    $MovieID           = get_post('MovieID');
    $CopyValue         = get_post('CopyValue');
    $AccountCustomerID = get_post('AccountCustomerID');
    $DateRented        = get_post('DateRented');
    
    $query = "UPDATE tblRentalHistory " .
             "SET DateReturned = sysdate() " .
             "WHERE MovieID = $MovieID " .
             "AND CopyValue = '$CopyValue' " .
             "AND AccountCustomerID = $AccountCustomerID " .
             "AND DateRented = '$DateRented' ";
    
    if (!mysql_query($query, $db_server))
      echo "UPDATE failed: $query<br />" . mysql_error() . "<br /><br />";
  }
  
  //HTML General
echo <<<_END
    <html>
      <head>
        <link rel="stylesheet" type="text/css" href="generic.css" />
        <title>Movie Rental - Return a movie</title>
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
          <h1> Return Movies</h1>       
        </center>
        
_END;

  //HTML Form

$query = "SELECT TRH.MovieID, TRH.CopyValue, TRH.AccountCustomerID, TRH.DateRented, TRH.DateDue, " .
         "DATEDIFF(sysdate(),TRH.DateDue) 'Days Late', C.FName, C.LName, M.Title, TMT.Name Media_Type " . 
         "FROM tblRentalHistory TRH " .
         "INNER JOIN tblCopyInformation TCI ON TCI.MovieID = TRH.MovieID AND TCI.CopyValue = TRH.CopyValue " .
         "INNER JOIN tblMovie M ON M.MovieID = TCI.MovieID " .
         "INNER JOIN tblMediaType TMT ON TMT.MediaTypeID = TCI.MediaTypeID " .
         "INNER JOIN tblAccountCustomer TAC ON TAC.AccountCustomerID = TRH.AccountCustomerID " .
         "INNER JOIN tblCustomer C ON C.CustomerID = TAC.CustomerID " .
         "WHERE TRH.DateReturned IS NULL AND DATEDIFF(sysdate(),TRH.DateDue)>=0 ";
         
$htmlOutput = table_query_output($query,4);
  
echo <<<_END
        $htmlOutput
      </body>
    </html>
_END;

mysql_close($db_server);

//*******************************
//          FUNCTIONS
//*******************************


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

function table_query_output($var, $startCol)
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
      
      for ($i = $startCol; $i < $cols; ++$i)
      {
        $reportoutput .= "<th>" . mysql_field_name($result,$i) . "</th>";
      }
      
      $reportoutput .= "<th>Action</th>";
      
      $reportoutput .= "</tr>";
      
      //Add rows/records
      for ($j = 0; $j < $rows ; ++$j)
      {
        $reportoutput .= '<tr><form action="return.php" method="post">';  //Add a form to each row so we can add a Return button to each row.
        $row = mysql_fetch_row($result);
        for ($i = $startCol;$i < $cols; ++$i)
        {
          $reportoutput .= "<td>" . $row[$i] . "</td>";
        }
        //Store the primary key information
        $reportoutput .= '<td><input type="hidden" name="MovieID" value="' . $row[0] . '" />' .
                         '<input type="hidden" name="CopyValue" value="' . $row[1] . '" />' .
                         '<input type="hidden" name="AccountCustomerID" value="' . $row[2] . '" />' .
                         '<input type="hidden" name="DateRented" value="' . $row[3] . '" />' .
                         '<input type="submit" value="Return" /></td>';
        
        $reportoutput .= "</form></tr>";
      }
      $reportoutput .= "</table>";
    }
    else
      $reportoutput = "There are no movies currently due or late";

  return $reportoutput;
}

?>