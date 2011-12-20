PDO_Proxy is a PHP Class that implements a Proxy for PDO to support MySQL Replication on ORMs that don't implement Replication but use
PDO. It has been writed to use with RedBeanPHP but should work anywhere a PDO Object is used.

What it does?
-------------

It just chooses the right connection where to send Queries. Master, or Slave. Also know as Read Write Splitting.

EXAMPLE
-------

For RedBeanPHP
---------------

$proxy = new PDO_Proxy();
$PDO_Proxy->SetupMaster('mysql:host=MASTER_DB_HOST;dbname=MASTER_DB_NAME','MASTER_DB_USER','MASTER_DB_PASSWORD');
$PDO_Proxy->SetupSlave('mysql:host=SLAVE_DB_HOST;dbname=SLAVE_DB_NAME','SLAVE_DB_USER','SLAVE_DB_PASSWORD');
R::setup($PDO_Proxy);

Need to tweak RedBean a little bit
----------------------------------

You have to make a slight modification to RedBean to make him believe PDO_Proxy is a PDO Object.

On your rb.php file, search for this line of code:

if ($dsn instanceof PDO) {

And replace it with:

if ($dsn instanceof PDO || $dsn instanceof PDO_Proxy) {

This line happens twice on current RedBean v3.0.1, on lines 29 and 1937 on rb.php file. Repalce both.

Limitations
-----------

PDO_Proxy supports the simplest MySQL Replication setup, where you have a Master, and a Slave. Fell free to modify it to support other setups.

