/*MY SQL Specific statements Jeremy Swartwood (CS360)*/

/*Database Create statement:*/

CREATE DATABASE MovieRental CHARACTER SET = 'utf8';

/*User Create Statements:*/

CREATE USER 'remoteuser2'@'localhost' IDENTIFIED BY 'RemoteUser2';

GRANT SELECT, INSERT, UPDATE, DELETE on MovieRental.* TO 'remoteuser2'@'localhost';

/*Table Create Statements:*/

USE MovieRental;

CREATE TABLE tblGenre(
    GenreID int(3) NOT NULL AUTO_INCREMENT,
    Name varchar(30) NOT NULL,
    Description varchar(255),
CONSTRAINT pk_tblGenre PRIMARY KEY (GenreID));

CREATE TABLE tblMediaType(
     MediaTypeID     INT(3)     NOT NULL    AUTO_INCREMENT,
     Name     VARCHAR(30),
     Description     VARCHAR(255),
CONSTRAINT pk_tblMediaType PRIMARY KEY (MediaTypeID));

CREATE TABLE tblAccountPlan(
     PlanID     INT(3)     NOT NULL    AUTO_INCREMENT,
     Name     VARCHAR(35)     NOT NULL,
     Description VARCHAR(255)     NOT NULL,
CONSTRAINT pk_tblAccountPlan PRIMARY KEY (PlanID));

CREATE TABLE tblAccount (
     AccountID     int(10) NOT NULL AUTO_INCREMENT,
     PlanID     INT(3)    NOT NULL,
CONSTRAINT pk_tblAccount PRIMARY KEY (AccountID),
CONSTRAINT fk_tblAccount FOREIGN KEY (PlanID) REFERENCES tblAccountPlan(PlanID));

CREATE TABLE tblCustomer (
     CustomerID     int(10) NOT NULL AUTO_INCREMENT,
     FName          VARCHAR(25)     NOT NULL,
     MName      VARCHAR(25),
     LName           VARCHAR(25)     NOT NULL,
     BirthDate      DATE          NOT NULL,
CONSTRAINT pk_tblCustomer PRIMARY KEY (CustomerID));

CREATE TABLE tblMovie(
     MovieID     int(10) NOT NULL AUTO_INCREMENT,
     Title          VARCHAR(255)     NOT NULL,
     RunningLength     INTEGER     NOT NULL,
     ReleaseDate     DATE,
     GenreID     INT(3) NOT NULL,
     Synopsis     blob,
CONSTRAINT pk_tblMovie PRIMARY KEY (MovieID),
CONSTRAINT fk_tblMovieGenre FOREIGN KEY (GenreID) REFERENCES tblGenre(GenreID));

CREATE TABLE tblCopyInformation (
     MovieID      INTEGER     NOT NULL,
     CopyValue      VARCHAR(3)      NOT NULL,
     PurchaseCost      DECIMAL(10,2),
     PurchaseDate      DATE,
     MediaTypeID       INT(3) NOT NULL,
CONSTRAINT pk_tblCopyInfo PRIMARY KEY (MovieID, CopyValue),
CONSTRAINT fk_tblCopyInfo FOREIGN KEY (MovieID) REFERENCES tblMovie(MovieID),
CONSTRAINT fk_tblCopyInfo2 FOREIGN KEY (MediaTypeID) REFERENCES tblMediaType(MediaTypeID));

CREATE TABLE tblAccountRelationshipType (
     AcctRelationshipID     int(10) NOT NULL AUTO_INCREMENT,
     Name          VARCHAR(100)     NOT NULL,
Description     VARCHAR(255)     NOT NULL,
CONSTRAINT pk_tblAcctRelateType PRIMARY KEY (AcctRelationshipID));     

CREATE TABLE tblAccountCustomer (
     AccountCustomerID     int(10) NOT NULL AUTO_INCREMENT,
     AccountID     INTEGER     NOT NULL,
     CustomerID     INTEGER     NOT NULL,
     DateAdded     DATE          NOT NULL,
     DateRemoved     DATE,
     AcctRelationshipID     INTEGER     NOT NULL,
     CONSTRAINT pk_tblAcctCust PRIMARY KEY (AccountCustomerID),
     CONSTRAINT fk_tblAcctCust1 FOREIGN KEY (AccountID) REFERENCES tblAccount(AccountID),
     CONSTRAINT fk_tblAcctCust2 FOREIGN KEY (CustomerID) REFERENCES tblCustomer(CustomerID),
CONSTRAINT fk_tblAcctCust3 FOREIGN KEY (AcctRelationshipID) REFERENCES tblAccountRelationshipType (AcctRelationshipID));

CREATE TABLE tblRentalHistory (
     MovieID     INTEGER     NOT NULL,
     CopyValue     VARCHAR(3)     NOT NULL,
     AccountCustomerID     INTEGER     NOT NULL,
     DateRented     DATE          NOT NULL,
     DateDue     DATE,
     DateReturned     DATE,
     CONSTRAINT pk_tblRentalHistory PRIMARY KEY (MovieID, CopyValue, AccountCustomerID, DateRented),
     CONSTRAINT fk_tblRentalHistory1 FOREIGN KEY (MovieID, CopyValue) REFERENCES tblCopyInformation (MovieID, CopyValue),
     CONSTRAINT fk_tblRentalHistory2 FOREIGN KEY (AccountCustomerID) REFERENCES tblAccountCustomer (AccountCustomerID));


/*Additional Indexes:*/

CREATE INDEX index_empfname ON tblCustomer(FName);
CREATE INDEX index_emplname ON tblCustomer(LName);
CREATE INDEX index_runlength ON tblMovie(RunningLength);
CREATE INDEX index_title ON tblMovie(Title);

