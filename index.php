<?php
//Call the class for creating the xml file
require("xml_creator.php");

//Create a new instance of the XMLCreator object and it will automatically call its methods
new XMLCreator("blacklist.dat", "entrada.csv", "saida.xml");

?>
