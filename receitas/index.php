<?php
  require('../app/class/Config.inc.php');
  $corJanelaMensagem = 'bg-branco';
  $txCor = 'tx-azul';
  $cardsReceitas=null;
  $read = new Read;
  $read->ExeRead('receita');
  if($read->getRowCount()):
    // $j=0;
    for($i = 0 ; $i < $read->getRowCount(); $i++):
      $imgc ="{$read->getResult()[$i]['imagem']}";
      // if($j>=2):
      $cardsReceitas = $cardsReceitas ."
      <div class=\"col-sm-3 mb-3\">
        <div class=\"card\">
          <div class=\"card-header text-center\">
            {$read->getResult()[$i]['nome']}
          </div>
          <ul class=\"list-group list-group-flush\">
            <li class=\"list-group-item text-center\">
              <button type=\"button\" class=\"btn btn-link abrirModal\" data-toggle=\"modal\" data-target=\"#exampleModal\" parametro=\"{$imgc}\">
                <figure>
                  <img src=\"{$read->getResult()[$i]['imagem']}\" class=\"card-img-top img-receita-index\" width=\"100%\">
                </figure>
              </button>
            </li>
            <li class=\"list-group-item text-center\">
              <a 
                data-toggle=\"tooltip\" 
                data-placement=\"top\" 
                title=\"Cria uma tabela onde é possivel ver Precificação\" 
                class=\"btn btn-outline-info btn-sm btn-block\" 
                href=\"tabela.php?tabelaId={$read->getResult()[$i]['id']}&receitanome={$read->getResult()[$i]['nome']}&rendimento={$read->getResult()[$i]['rendimento']}&precoReal={$read->getResult()[$i]['preco_real']}\" 
                role=\"button\">
                  Tabela
              </a>
              <a 
                data-toggle=\"tooltip\" 
                data-placement=\"top\" 
                title=\"Edita as informações desta Receita\" 
                class=\"btn btn-outline-success btn-sm btn-block\" 
                href=\"adicionar-receita.php?idReceita={$read->getResult()[$i]['id']}&imagem={$read->getResult()[$i]['imagem']}\" 
                role=\"button\">
                Editar
              </a>
              <a 
                data-toggle=\"tooltip\" 
                data-placement=\"top\" 
                title=\"Adiciona, Edita ou Remove itens desta Receita\" 
                class=\"btn btn-outline-secondary btn-sm btn-block\" 
                href=\"itens-de-receita.php?idreceita={$read->getResult()[$i]['id']}&nomereceita={$read->getResult()[$i]['nome']}&rendimento={$read->getResult()[$i]['rendimento']}&tipo={$read->getResult()[$i]['tipo']}\" 
                role=\"button\">
                  Add Itens
              </a>
              <a 
                data-toggle=\"tooltip\" 
                data-placement=\"top\" 
                title=\"Exibe a receita em forma de livro\" 
                class=\"btn btn-outline-warning btn-sm btn-block\" 
                href=\"receita.php?nomeReceita={$read->getResult()[$i]['nome']}&idReceita={$read->getResult()[$i]['id']}&medida={$read->getResult()[$i]['medida']}&tipo={$read->getResult()[$i]['tipo']}&rendimento={$read->getResult()[$i]['rendimento']}&precoReal={$read->getResult()[$i]['preco_real']}&imagem={$read->getResult()[$i]['imagem']}\"
                role=\"button\">
                  Receita
              </a>
            </li>
          </ul>
        </div>
      </div>
    ";
      // endif;

    endfor;
  else:
    $cardsReceitas='Ainda não temos receitas criadas.';
  endif;
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Receitas</title>
    <link href="../app/assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../app/css/offcanvas.css" rel="stylesheet">
  </head>
<body class="bg-body">
  <nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-dark container-menu">
  <?php echo baseMenu();?>
    <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a class="nav-link" href="adicionar-receita.php">Adicionar Receita</a></li>
        <li class="nav-item"><a class="nav-link" href="itens-de-receita.php">Itens de uma Receita</a></li>
        <li class="nav-item"><a class="nav-link" href="embalagem-de-receita.php">Embalagem de uma Receita</a></li>
      </ul>
      <form class="form-inline my-2 my-lg-0" method="get">
        <input class="form-control mr-sm-2" type="text" placeholder="Desativado" name="idnota" disabled>
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="pesquisa" value="envia" disabled><?php echo getSvg('search', '20', 'tx-branco');?></button>
      </form>
    </div>
  </nav>
  <main role="main" class="container">
    
    <div class="my-3 p-3 bg-branco rounded shadow-sm">
      <h6 class="border-bottom border-gray pb-2 mb-0">Receitas contidas em seu livro</h6>
      
      <div class="row mt-3">
        <!-- <div class="col-sm-3 mb-3">
          <div class="card">
            < ?php echo $cardsReceitas;?>
          </div>
        </div> -->
      
        <?php echo $cardsReceitas;?>
        
      </div>

      <small class="d-block text-right mt-3">
        <a href="#">@ Aroni Souza</a>
      </small>
    </div>
  </main>
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Imagem</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <img src="" width="100%" class="mt-3" />
        </div>
      </div>
    </div>
  </div>
<script src="../app/java/jquery-3.5.1.slim.min.js"></script>
<script src="../app/assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="../app/java/offcanvas.js"></script>
<script>

  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });

  $(".abrirModal").click(function() {
    var url = $(this).find("img").attr("src");
    $("#myModal img").attr("src", url);
    $("#myModal").modal("show");
  });

</script>
</body>
</html>
