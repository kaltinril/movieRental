<?php
  require_once 'login.php';
  $db_server = mysql_connect($db_hostname, $db_username, $db_password);
  
  if (!$db_server) die("Unable to connect to MySQL: " .mysql_error());
  
  mysql_select_db($db_database, $db_server)
    or die("Unable to select database: " . mysql_error());

  //Get list of Customers into a drop down list
  $query = "SELECT -1, ' ' from tblAccountPlan " .
           "UNION SELECT CustomerID, CONCAT(TRIM(CONCAT(FName,' ',Coalesce(MName,''))),' ',LName) FROM tblCustomer";
  $custDDL = create_ddl('CustomerID',$query);

  //Get list of Customers into a drop down list
  $query = "SELECT -1, ' ' from tblAccountPlan " .
           "UNION SELECT MovieID, Title from tblMovie";
  $movieDDL = create_ddl('MovieID',$query);


  if (isset($_POST['MovieID']) && 
      isset($_POST['CustomerID'])
     )
  {
    $CustomerID = get_post('CustomerID');
    $MovieID    = get_post('MovieID');
    
    if ($CustomerID != -1 && $MovieID != -1)
    {
      $query = "SELECT AccountCustomerID FROM tblAccountCustomer " .
               "WHERE CustomerID = $CustomerID " .
               "LIMIT 0,1";
    
      $result = mysql_query($query);
      if (!$result) die ("Database access failed: " . mysql_error());
      
      
      $rows = mysql_num_rows($result);
      if ($rows<1) die ("Unable to find Account this customer is on");
      
      $row = mysql_fetch_row($result);
      
      $query = "INSERT INTO tblRentalHistory VALUES" .
               "($MovieID,'A',$row[0], sysdate(),DATE_ADD(sysdate(), INTERVAL 1 DAY),null)";
      
      if (!mysql_query($query, $db_server))
        echo "DELETE failed: $query<br />" . mysql_error() . "<br /><br />";
    }
  }

echo <<<_END
    <html>
      <head>
        <link rel="stylesheet" type="text/css" href="generic.css" />
        <title>Movie Rental - Rent a movie</title>
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
        <p> 1. Select a customer from the first drop down list <br />
            2. Select a movie to rent to the customer selected <br /> 
            3. The Movie Copy will default to copy (A) <br />
            4. Press Rent</p>
        <form action="rent.php" method="post">
        <pre>
 Customer: $custDDL
    Movie: $movieDDL
          <input type="submit" value="Rent" />
          </pre>
        </form>
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