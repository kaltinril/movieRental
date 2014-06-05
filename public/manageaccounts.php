<?php //managemovies.php
  require_once 'login.php';
  $db_server = mysql_connect($db_hostname, $db_username, $db_password);
  
  if (!$db_server) die("Unable to connect to MySQL: " .mysql_error());
  
  mysql_select_db($db_database, $db_server)
    or die("Unable to select database: " . mysql_error());
  
  //Get the PlanID into the drop down list
  $query = "SELECT PlanID, Name FROM tblAccountPlan";
  $planDDL = create_ddl('PlanID',$query);

  //Get the Account Relationship Type into the drop down list
  $query = "SELECT AcctRelationshipID, Name FROM tblAccountRelationshipType";
  $acctRelateDDL = create_ddl('RelationID',$query);

  //Get list of Customers into a drop down list
  $query = "SELECT CustomerID, CONCAT(TRIM(CONCAT(FName,' ',Coalesce(MName,''))),' ',LName) FROM tblCustomer";
  $custDDL = create_ddl('CustomerID',$query);

  //Add Customer to Account
  if (isset($_POST['update']) && 
      isset($_POST['AccountID']) && 
      isset($_POST['CustomerID']) &&
      isset($_POST['RelationID'])
     )
  {
    $AccountID  = get_post('AccountID');
    $CustomerID = get_post('CustomerID');
    $RelationID = get_post('RelationID');
    
    $query = "INSERT INTO tblAccountCustomer VALUES" .
             "(null,$AccountID, $CustomerID, sysdate(),null,$RelationID)";
    
    if (!mysql_query($query, $db_server))
      echo "DELETE failed: $query<br />" . mysql_error() . "<br /><br />";
  }
  
  //Add a record if they clicked add
  if (isset($_POST['PlanID']) &&
      isset($_POST['addnew']) 
      )
  {
    $PlanID       = get_post('PlanID');
    
    $query = "INSERT INTO tblAccount VALUES" .
             "(null,$PlanID)";
    
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
<form action="manageaccounts.php" method="post">
<pre>
               <input type="hidden" name="addnew" value="yes" />
Select a Plan and click (New Account) to create a new account.
          Plan $planDDL
               <input type="submit" value="New Account" />
<hr />
</pre></form>
_END;

$query = "SELECT A.AccountID, P.Name FROM tblAccount A ";
$query .= "INNER JOIN tblAccountPlan P ON P.PlanID = A.PlanID";
$result = mysql_query($query);
if (!$result) die ("Database access failed: " . mysql_error());
$rows = mysql_num_rows($result);

for($j = 0 ; $j < $rows ; ++$j)
{
  $row = mysql_fetch_row($result);
  
  $query = "SELECT C.CustomerID, CONCAT(TRIM(CONCAT(C.FName,' ',Coalesce(C.MName,''))),' ',C.LName) FROM tblCustomer C " .
           "INNER JOIN tblAccountCustomer TAC ON TAC.CustomerID = C.CustomerID " .
           "WHERE TAC.AccountID = " . $row[0];
  $acctOwnersDDL = create_ddl('acctOwners',$query);
  
  echo <<<_END
  
<pre>
     AccountID: $row[0]
          Plan: $row[1]
   Acct Owners: $acctOwnersDDL
</pre>
<form action="manageaccounts.php" method="post">
  <input type="hidden" name="update" value="yes" />
  <input type="hidden" name="AccountID" value="$row[0]" />
  $custDDL $acctRelateDDL <input type="submit" value="Add Customer to Account" />
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