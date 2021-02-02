<?php
require('../../app/class/Config.inc.php');

$tabela = $_GET['tabela'];
$op = isset($_GET['op'])?$_GET['op']:'';
$m = date('m'); // mês atual
$tbTh ='';

$producaoTotal ='';
$producaoTotalM ='';
$retornoTabela ='';

  if($tabela=='producao'):
    // Entrada total por unidade
    $lerPro = new Read;
    $lerPro->FullRead('SELECT SUM(`qnt_prod`) AS `total` FROM `producao` ');
    $producaoTotal = $lerPro->getResult()[0]['total'];
    
    // Entrada total por mes [Day, Month, Year]
    $lerProM = new Read;
    $lerProM->FullRead("SELECT SUM(`qnt_prod`) AS `totalM` FROM `producao` WHERE MONTH(`data_producao`) = $m");
    $producaoTotalM = $lerProM->getResult()[0]['totalM'];

    // Quantidade por produto
    // - nome produto | total | total mes | total estoque
    $lProduto = new Read;
    $lProduto->ExeRead('receita');
    if($lProduto->getResult()):
      // vai ate tabela producao e faz oq tem q ser feito
      // Calcula total produzido de cada produto
      foreach ($lProduto->getResult() as $key => $value) {
        $producaoTT = new Read;
        $producaoTT->FullRead("SELECT SUM(`qnt_prod`) AS `total` FROM `producao` WHERE `produto_id`= {$value['id']}");
        $producaoTM = new Read;
        $producaoTM->FullRead("SELECT SUM(`qnt_prod`) AS `totalM` FROM `producao` WHERE MONTH(`data_producao`) = $m AND `produto_id`= {$value['id']}");
        $producaoTE = new Read;
        $producaoTE->FullRead("SELECT SUM(`quantidade`) AS `totalE` FROM `producao` WHERE `produto_id`= {$value['id']}");
        
        $mesR = $producaoTM->getResult()[0]['totalM'] ==0?0:$producaoTM->getResult()[0]['totalM'];

        $tbTh = $tbTh."
          <tr>
            <td>{$value['nome']}</td>
            <td>{$producaoTT->getResult()[0]['total']}</td>
            <td>{$mesR}</td>
            <td>{$producaoTE->getResult()[0]['totalE']}</td>
          </tr>
        ";
      }

      $retornoTabela = "
        <table class=\"table table-bordered table-hover\">
          <thead>
            <tr>
              <th scope=\"col\">Produto</th>
              <th scope=\"col\">Total Geral</th>
              <th scope=\"col\">Total Mês</th>
              <th scope=\"col\">Em Estoque</th>
            </tr>
          </thead>
          <tbody>
            {$tbTh}
          </tbody>
        </table>
        ";
    endif;

    return  [
      'producaoTotal'  => $producaoTotal,
      'producaoTotalM' => $producaoTotalM,
      'retornoTabela'  => $retornoTabela
    ];
    
  endif;
?>