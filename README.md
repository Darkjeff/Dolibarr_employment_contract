Employment contract
============================

Module to manage employment contract

INSTALL
-------

Since version 3 it is possible to define an alternative root directory,
This allows you to store, same place, plug-ins and custom templates.
Just create a directory at the root of Dolibarr (eg custom),
then declare it in the file conf.php :

examples :

$dolibarr_main_url_root='http://myserver';
$dolibarr_main_document_root='/path/of/dolibarr/htdocs';
$dolibarr_main_url_root_alt='http://myserver/custom';
$dolibarr_main_document_root_alt='/path/of/dolibarr/htdocs/custom';

Copy "emcontract" directory to the root of "custom" directory.
