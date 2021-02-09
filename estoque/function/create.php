<?php
  require('../../app/class/Config.inc.php');

  
  if (isset($_POST['lacarProducao']) && $_POST['lacarProducao'] =='produto'):
    $alteracao = 0;
    $volume_novo = 0;
    $ler = new Read;
    $ler->ExeRead('receita', 'WHERE id=:id', "id={$_POST['produto_nome']}");
    $validade = date('Y-m-d', strtotime('+6 month', strtotime($_POST['data_producao'])));
    if(isset($_POST['alteracao'])):
       $alteracao=1;
       $volume_novo = $_POST['volume_novo'];
    endif;
    $dados=[
      'produto_id'      => $ler->getResult()[0]['id'],
      'produto_nome'    => $ler->getResult()[0]['nome'],
      'data_producao'   => $_POST['data_producao'],
      'data_validade'   => $validade,
      'alteracao'       => $alteracao,
      'volume_novo'	    => $volume_novo,
      'quantidade'      => $_POST['quantidade'],
      'qnt_prod'        => $_POST['quantidade']
    ];
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω", "&");
    $dados['volume_novo'] = str_replace($vowels, "",  $dados['volume_novo']);
    $lacar = new Create;
    $lacar->ExeCreate('producao',$dados);
    if($lacar->getResult()>0):
      header("Location: ../lancamento-producao.php?retorno=Ok");
    else:
      header("Location: ../lancamento-producao.php?retorno=Nok");
    endif;
  endif;
  

  if (isset($_POST['lacarProducao']) && $_POST['lacarProducao'] =='transferir'):
    $ler = new Read;
    $ler->ExeRead('producao', 'WHERE id=:id', "id={$_POST['produto_nome']}");
    $Revendedor = new Read;
    $Revendedor->ExeRead('revendedor', 'WHERE id=:id', "id={$_POST['destino_id']}");
    $dados = [
      'produto_id'      => $_POST['produto_id'],
      'produto_nome'    => $ler->getResult()[0]['produto_nome'],
      'data_producao'   => $_POST['data_producao'],
      'data_validade'   => $_POST['data_validade'],
      'alteracao'       => $_POST['alteracao'],
      'volume_novo'     => $_POST['volume_novo'],
      'producao_id'     => $_POST['producao_id'],
      'quantidade'      => $_POST['quantidadeTrans'],
      'data_saida'      => $_POST['data_saida'],
      'destino_id'      => $_POST['destino_id'],
      'destino_apelido' => $Revendedor->getResult()[0]['apelido']
    ];
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω", "&");
    $dados['volume_novo'] = str_replace($vowels, "", $dados['volume_novo']);
    $Transferir = new Create;
    $Transferir->ExeCreate('saidas',$dados);
    if($Transferir->getResult()>0):
      $updateProduto = new Update;
      $troca=['quantidade'=> $_POST['quantidade']];
      $updateProduto->ExeUpdate('producao', $troca, "WHERE id=:id", "id={$_POST['producao_id']}");
      if($updateProduto->getResult()>0):
         header("Location: ../transferir-saida.php?retorno=Ok&destino={$dados['destino_apelido']}&qnt={$dados['quantidade']}&produto={$dados['produto_nome']}");
      endif;
    else:
      header("Location: ../transferir-saida.php?retorno=Nok");
    endif;
  endif;

  if (isset($_POST['lacarProducao']) && $_POST['lacarProducao'] =='vender'):
    $vuni = $_POST['valor'];
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω", "&");
    $vuni = str_replace($vowels, "",  $vuni);
    $vtotal = $vuni * $_POST['quantidadev'];
    $DANIELA=$_POST['idTabSaida']; //id do produto na tabela receita
    $dados = [
      'produto_id'          => $_POST['idTabReceita'], //id do produto na tabela saidas
      'produto_nome'        => $_POST['produto_nome'],
      'data_producao'       => $_POST['data_producao'],
      'data_validade'       => $_POST['data_validade'],
      'alteracao'           => $_POST['alteracao'],
      'volume_novo'         => $_POST['volume_novo'],
      'producao_id'         => $_POST['producao_id'],
      'quantidade'          => $_POST['quantidadev'],
      'data_venda'          => $_POST['data_venda'],
      'origem_id'           => $_POST['destino_id'],
      'origem_apelido'      => $_POST['destino_apelido'],
      'origem_data_saida'   => $_POST['data_saida'],
      'valor_unidade'       => $vuni,
      'valor_total'         => $vtotal,
      'forma_pagamento'     => $_POST['forma_pagamento'],
      'obs_forma_pagamento' => $_POST['obs_forma_pagamento']
    ];
    $vender = new Create;
    $vender->ExeCreate('vendas',$dados);
    if($vender->getResult()>0):
      $updateSaida = new Update;
      $troca=['quantidade'=> $_POST['quantidade']];
      $updateSaida->ExeUpdate('saidas', $troca, "WHERE id=:id", "id={$DANIELA}");
      if($updateSaida->getResult()>0):
         header("Location: ../informar-venda.php?retorno=Ok&produto={$_POST['produto_nome']}&qnt={$dados['quantidade']}&valort={$vtotal}&vendedor={$_POST['destino_apelido']}");
      endif;
    else:
      header("Location: ../informar-venda.php?retorno=Nok");
    endif;
  endif;

  if (isset($_POST['lacarProducao']) && $_POST['lacarProducao'] =='voltarEstoque'):
    $vuni = $_POST['valor'];
    $vowels = array(" ", "R$", ",", "۞", "%", "Ω", "&");
    $vuni = str_replace($vowels, "",  $vuni);
    $vtotal = $vuni * $_POST['quantidadev'];
    $DANIELA=$_POST['idTabSaida'];
    

    
    $dados = [
      'id_produto'          => $_POST['producao_id'],
      'data_volta'          => date('Y/m/d'),
      'de_onde_voltou'      => $_POST['destino_apelido'],
      'data_vencimento'     => $_POST['data_validade'],
      'quantidade'          => $_POST['quantidadev'],
      'descricao'           => $_POST['obs_volta']
    ];
    $voltar = new Create;
    $voltar->ExeCreate('volta_estoque',$dados);


    if($voltar->getResult()>0):
      $updateSaida = new Update;
      $troca=['quantidade'=> $_POST['quantidade']];

      $updateSaida->ExeUpdate('saidas', $troca, "WHERE id=:id", "id={$DANIELA}");
      if($updateSaida->getResult()>0):
        
        $updateProducao = new Update;
        $updateSaida->ExeUpdate('producao', $troca, "WHERE id=:id", "id={$_POST['producao_id']}");

PAREI AQUI TEM QUE FAZER O COISO MUDAR VALOR NA TEBELA PRODUCAO 

         header("Location: ../informar-venda.php?retorno=Ok&produto={$_POST['produto_nome']}&qnt={$dados['quantidade']}&valort={$vtotal}&vendedor={$_POST['destino_apelido']}");
      endif;

    else:
      header("Location: ../informar-venda.php?retorno=Nok");
    endif;

  endif;


