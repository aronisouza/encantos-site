<?php
  require('app/class/Config.inc.php');

  $corJanelaMensagem = 'bg-branco';
  $txCor = 'tx-azul';

  function getSvgP ($qual, $tamanho, $cor){
    $comum = "<svg width=\"{$tamanho}\" height=\"{$tamanho}\" viewBox=\"0 0 16 16\" class=\"{$cor}\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
      <use xlink:href=\"app/imgs/bootstrap-icons.svg#{$qual}\"/>
    </svg>";
    return $comum;
  }

  function baseMenuP(){
    $financa =	getSvgP('newspaper', '30', 'tx-azul');
    $receita =	getSvgP('book', '30', 'tx-laranja');
    $estoque =	getSvgP('hdd-rack', '30', 'tx-branco');
    return "
    <a href=\"http://$_SERVER[HTTP_HOST]/www.encantosdoflorescer.br/\"
        data-toggle=\"tooltip\" 
        data-placement=\"top\" 
        title=\"Finanças\"
        class=\"mr-3\"><img class=\"mr-3\" src=\"app/imgs/flor.ico\" width=\"48\" height=\"48\">	
      </a>
  
  
      <a href=\"http://$_SERVER[HTTP_HOST]/www.encantosdoflorescer.br/financas\"
        data-toggle=\"tooltip\" 
        data-placement=\"top\" 
        title=\"Finanças\"
        class=\"mr-3\">{$financa}
      </a>
  
      <a href=\"http://$_SERVER[HTTP_HOST]/www.encantosdoflorescer.br/receitas\"
        data-toggle=\"tooltip\" 
        data-placement=\"top\" 
        title=\"Receitas\" 
        class=\"mr-3\">{$receita}
      </a>
  
      <a href=\"http://$_SERVER[HTTP_HOST]/www.encantosdoflorescer.br/estoque\"
        data-toggle=\"tooltip\" 
        data-placement=\"top\" 
        title=\"Estoque\" 
        class=\"mr-3\">{$estoque}
      </a>
    ";
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Finanças</title>
    <!-- Bootstrap core CSS -->
    <link href="app/assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="app/css/offcanvas.css" rel="stylesheet">

  </head>

  <body class="bg-body">
    
<nav class="navbar navbar-expand-sm fixed-top navbar-dark bg-dark container-menu">
  <?php echo baseMenuP();?>
  <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
    <span class="navbar-toggler-icon"></span>
  </button>
</nav>

<main role="main" class="container">
  
  <div class="alert <?php echo $corJanelaMensagem;?> mt-3 shadow-sm" role="alert">
    <div class="d-flex">
      <div class="p-4 flex-shrink-1">
        <?php echo getSvgP('exclamation-circle', '63', $txCor); ?>
      </div>
      <div class="p-2 w-100">
        <h5 class="alert-heading <?php echo $txCor; ?>">Titulo</h5>
        <hr>
        <p class="<?php echo $txCor; ?>">This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
      </div>
    </div>
  </div>

  <div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Recent updates</h6>
    
    <div class="media text-muted pt-3">
      <svg class="bd-placeholder-img mr-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>Placeholder</title><rect width="100%" height="100%" fill="#6f42c1"/><text x="50%" y="50%" fill="#6f42c1" dy=".3em">32x32</text></svg>
      <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">@username</strong>
        Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
      </p>
    </div>

    <small class="d-block text-right mt-3">
      <a href="#">@ Aroni Souza</a>
    </small>
  </div>

</main>

<script src="app/java/jquery-3.5.1.slim.min.js"></script>
<script src="app/assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="app/java/offcanvas.js"></script>

<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });
</script>
</body>
</html>
