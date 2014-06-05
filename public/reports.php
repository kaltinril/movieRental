<?php

  //Include the Database Login Information
  require_once 'login.php';

if (isset($_POST['reportselector'])) 
{
  //Store the returned value from the Report Selector
  //into rsrv = Report Selector Return value
  $rsrv = $_POST['reportselector'];

  //Open the connection using the information from the login.php file
  $db_server = mysql_connect($db_hostname,$db_username, $db_password);

  //attempt to connect to the MySQL server
  if (!$db_server) die("Unable to Connect to MySQL:" . mysql_error());

  //Attempt to open the database specified in login.php
  mysql_select_db($db_database) or die("Unable to select database: " . mysql_error());

  //In-Line query select statement
  if ($rsrv=="allmovies")
    $query = "SELECT * FROM tblMovie";
  elseif ($rsrv=="allcustomers")
    $query = "SELECT * FROM tblCustomer";
  elseif ($rsrv=="moviesoverdue")
  {
    $query = "SELECT M.Title, CI.CopyValue, RH.DateRented, RH.DateDue, DATEDIFF(sysdate(),RH.DateDue) 'Days Late', ";
    $query .= "TC.FName, TC.LName, TART.Name RelationshipType, TAP.Name 'Account Plan', TAP.Description 'Account Plan Description' ";
    $query .= "FROM tblMovie M ";
    $query .= "INNER JOIN tblCopyInformation CI ON CI.MovieID = M.MovieID ";
    $query .= "INNER JOIN tblRentalHistory RH ON RH.MovieID = CI.MovieID AND RH.CopyValue = CI.CopyValue ";
    $query .= "INNER JOIN tblAccountCustomer TAC ON TAC.AccountCustomerID = RH.AccountCustomerID ";
    $query .= "INNER JOIN tblAccount TA ON TA.AccountID = TAC.AccountID ";
    $query .= "INNER JOIN tblAccountPlan TAP ON TAP.PlanID = TA.PlanID ";
    $query .= "INNER JOIN tblCustomer TC on TC.CustomerID = TAC.CustomerID ";
    $query .= "INNER JOIN tblAccountRelationshipType TART ON TART.AcctRelationshipID = TAC.AcctRelationshipID ";
    $query .= "WHERE RH.DateReturned IS NULL";
  }
  elseif ($rsrv=="allaccounts")
    $query = "SELECT * FROM tblAccount";
  elseif ($rsrv=="FieldDoc")
  {
    $query = "SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION, IS_NULLABLE, COLUMN_TYPE, COLUMN_KEY, EXTRA ";
    $query .= "FROM information_schema.COLUMNS C ";
    $query .= "WHERE TABLE_SCHEMA = 'MovieRental'";
  }
  else
    $query = "SELECT '' FROM tblAccount";
  
    $reportoutput = table_query_output($query);
}
else $reportoutput = "";

echo <<<_END
    <html>
      <head>
        <link rel="stylesheet" type="text/css" href="generic.css" />
        <title>Movie Rental - Reports</title>
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
        <form method="post" action="reports.php">
          Report <select name="reportselector" size="1">
            <option value="allmovies">All Movies</option>
            <option value="allcustomers">All Customers</option>
            <option value="allaccounts">All Accounts</option>
            <option value="moviesoverdue">Movies Over Due</option>
            <option value="FieldDoc">Data Dictionary (Field)</option>
          </select>
          <input type="submit" value="Display Report" />
        </form>
        <p>$reportoutput</p>
      </body>
    </html>
_END;


//*******************************
//          FUNCTIONS
//*******************************

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

?>