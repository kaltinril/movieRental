<?php
echo <<<_END
    <html>
      <head>
        <link rel="stylesheet" type="text/css" href="mainpage.css" />
        <title>Movie Rental Main Page</title>
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
          <p class="maintitle">
			Movie Rental
          </p>
          <br />
          Created by: Jeremy Swartwood 2011
        </center>
      </body>
    </html>
_END;
?>
