<?php
Class XmlCreator {

  //Declaring variables for file names
  private $dat_file;
  private $csv_file;
  private $xml_file;

  //Declaring array to store data to use between methods
  private $banned = [];
  private $final_data = [];

  //Creating the class constructor, it receives the names for the blacklist, the data and the final file
  public function __construct($dat_file = "blacklist.dat", $csv_file = "entrada.csv", $xml_file = "saida.xml"){
    //Saving the parameters to the object parameters
    $this->dat_file = $dat_file;
    $this->csv_file = $csv_file;
    $this->xml_file = $xml_file;

    //Print the status of the script so the user can check whats happening
    echo "Iniciando criação do arquivo XML em base aos archivos $dat_file e $csv_file<br>";

    //Call the methods in order to write the final XML
    $this->readBlackList();
    $this->readCSV();
    $this->writeXML();
  }

  //Make a function to check if external files exists so the script doesnt break
  private function checkFile($fileName){
    if(!file_exists($fileName)){
      //if the file doesnt exist then print an error so the user knows where it stopped
      exit("O archivo $fileName não existe");
    }
  }

  //Make a function to read the .dat file
  private function readBlacklist(){
    //call the method to check if file exists
    $this->checkFile($this->dat_file);
    //Reading the blacklist
    $blacklist = file($this->dat_file);
    //Store the data in the array with the bans and standarize them to a simple string without line breaks
    foreach($blacklist as $item){
      $item = str_replace(array("\r", "\n"), '', $item);
      //push the record to the banned array
      $this->banned[] = $item;
    }
    //print the status of the script
    echo 'Blacklist pronta para bannear<br>';
  }

  //Make a function to read the .csv file
  private function readCSV(){
    //call the method to check if file exists
    $this->checkFile($this->csv_file);
    //if the file can be opened then parse it as csv
    if (($handle = fopen($this->csv_file, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1024)) !== FALSE) {
        //check if the record is in the blacklist
        if(!in_array($data[0], $this->banned)){
          //push the data to the array
          $this->final_data[] = $data;
        }
      }
      //close the file
      fclose($handle);
      //print the status of the script
      echo "Dados do $this->csv_file parseados<br>";
    }
  }

  //Make a function to write the .xml file
  private function writeXML(){
    //create the DomDocument object to write de .xml
    $dom = new DomDocument('1.0', 'UTF-8');
    //create the root element that will be the parent of everything
    $rootElement = $dom->appendChild($dom->createElement("table"));
    //Writing a new table row for each record
    foreach ($this->final_data as $key => $record) {
        //check if is the first record then set it as a header row
        $row_type = ($key == 0) ? 'th' : 'tr';
        $track = $rootElement->appendChild($dom->createElement($row_type));
        //writing each item of record in a new cell
        foreach ($record as $item) {
          $track->appendChild($dom->createElement('td', $item));
        }
    }

    //formatting the output
    $dom->formatOutput = true;

    //saving the file
    $dom->save($this->xml_file);

    //print the status of the script
    echo "Archivo $this->xml_file criado<br>";
  }

}
?>
