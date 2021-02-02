<?php
  require('../app/class/Config.inc.php');
  
  $GerarCombo = new Read;
  $GerarCombo->ExeRead('producao');
  // if($GerarCombo->getRowCount()):
  //   for ($i=0; $i < $GerarCombo->getRowCount(); $i++) { 
  //     $select = $select."<option value=\"{$GerarCombo->getResult()[$i]['id']}\">{$GerarCombo->getResult()[$i]['produto_nome']} | Data: {$GerarCombo->getResult()[$i]['data_producao']} | QNT: [{$GerarCombo->getResult()[$i]['quantidade']}]</option>";
  //   }
  // endif;

  $GerarCombo->ExeRead('producao'); 
    while($resultado = mysqli_fetch_assoc($GerarCombo)){
        $vetor[] = array_map('utf8_encode', $resultado); 
    }    
    
    //Passando vetor em forma de json
    echo json_encode($vetor);

    die;