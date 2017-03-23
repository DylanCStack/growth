<?php
date_default_timezone_set("America/Los_Angeles");
require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../src/Map.php";
require_once __DIR__."/../src/Network.php";
require_once __DIR__."/../src/User.php";


$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), ["twig.path" => __DIR__."/../views"]);

$app['debug']= true;

$server = 'mysql:host=localhost:8889;dbname=growth';
$username = 'root';
$password = 'root';
$DB = new PDO($server, $username, $password);



session_start();
if(empty($_SESSION['network'])){
  $_SESSION['network'] = new Network([100,200,100]);
}

$app->get('/', function() use($app) {
  $testwork = new Network([2,3,2]);
  $a = [1,0];
  $b = [0,1];
  $c = [1,1];
  for ($i=0;$i<1000;$i++) {
    $testwork->backprop($a,$a,.1);
    $testwork->backprop($b,$a,.1);
    $testwork->backprop($c,$b,.1);

  }
  // echo 'next------    ';
  var_dump($testwork->feedforward($a));
  var_dump($testwork->feedforward($b));
  var_dump($testwork->feedforward($c));


  return $app["twig"]->render("root.html.twig", ['edit' => false]);
});




$app->post('/start_computer_game', function() use($app) {


$player_map = $_POST['start_conditions'];
$player_moves = Network::parse_playing_grid($player_map);


    $confidence_array = ($_SESSION['network']->feedforward($player_moves));
    arsort($confidence_array);
    // var_dump($confidence_array);
    $computer_move1x;
    $computer_move1y;
    $computer_move2x;
    $computer_move2y;
    $computer_move3x;
    $computer_move3y;
    $i = 0;
    foreach($confidence_array as $key => $value) {
      if ($i == 0) {
        $x_coord = $key % 10;
        $computer_move1x = $x_coord;
        $y_coord = ($key-$x_coord)/10;
        $computer_move1y = $y_coord;
      } elseif ($i == 1) {
          $x_coord = $key % 10;
          $computer_move2x = $x_coord;
          $y_coord = ($key-$x_coord)/10;
          $computer_move2y = $y_coord;
      } elseif ($i == 2) {
            $x_coord = $key % 10;
            $computer_move3x = $x_coord;
            $y_coord = ($key-$x_coord)/10;
            $computer_move3y = $y_coord;
          }
      else {
        // $computer_moves = [[$computer_move1x,$computer_move1y,1],[$computer_move2x,$computer_move2y,1],[$computer_move3x,$computer_move3y,1]];
        $computer_moves = [[$computer_move1y,$computer_move1x,1],[$computer_move2y,$computer_move2x,1],[$computer_move3y,$computer_move3x,1]];
        break;
      }
      $i++;
    }
    // $computer_moves = [];
    //
    //
    for($i=0;$i<10;$i++) {
        for($j=0;$j<10;$j++){
            array_push($computer_moves, [$i,$j,$confidence_array[$i*10 + $j]]);
        }
    }
    return json_encode($computer_moves);


  });


  $app->get('/deleteAll', function() use($app) {
    $_SESSION['network'] = new Network([100,200,100]);

    return $app->redirect("/");
  });

  $app->post('/trainNetwork', function() use($app) {
    $player_map = $_POST['start_conditions'];

    $a = [1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
    $b = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0];
    $c = [0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
    $d = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1];

    $player_moves = Network::parse_training_grid($player_map);
    // var_dump($player_moves[0]);
    // var_dump($player_moves[1]);
    for($i=0;$i<10;$i++){
      // $_SESSION['network']->backprop($player_moves[0],$player_moves[1],.1);
      $_SESSION['network']->backprop($a,$b,.1);
      $_SESSION['network']->backprop($c,$d,.1);

    }



    return json_encode('done');
  });


return $app;
?>
