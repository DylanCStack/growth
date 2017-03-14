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
    if(empty($_SESSION['user'])){
        $_SESSION['user'] = [];
    }

    $app->get('/', function() use($app) {
      $network = new Network([2,3,2]);
        $a = [1,0];
        $b = [0,1];
        $c = [1,1];
        // var_dump($network->activations);
        // var_dump($network->weights);
        // var_dump($network->biases);
        // $array1 = [[1,2,3],[4,5,6],[7,8,9]];
        // $array2 = [[3],[4],[5]];
        // $result = Network::dot($array1,$array2);
        // $result = $network->feedforward($a);
        // $_SESSION['user'] = [];//uncomment to fix pesky session bugs
        // echo 'next';
        // var_dump($network->weights);
        //
        for($i=0;$i<100;$i++){
          echo 'next';
            $network->backprop([1,0],[0,1],1);
            // echo 'next';
            // var_dump($network->activations);
            // var_dump($network->weights);
            var_dump($network->biases);
            // $network->backprop([0,1],[1,0],.1);
        //     var_dump($network->feedforward($a));
        //     var_dump($network->feedforward($b));
        //     var_dump($network->feedforward($c));
        //     $network->backprop([0,1],[0,1],.1);
        //     var_dump($network->feedforward($a));
        //     var_dump($network->feedforward($b));
        //     var_dump($network->feedforward($c));
            // $network->backprop([1,1],[1,1],1);
        //     var_dump($network->feedforward($a));
        //     var_dump($network->feedforward($b));
        //     var_dump($network->feedforward($c));
        }
        //
        // var_dump($network->feedforward($a));
        // var_dump($network->feedforward($b));
        // var_dump($network->feedforward($c));
        // //
        // //
        // //
        // //
        // // var_dump($result);
        // $network = new Network([400,100,100,400]);
        //
        // //  $player_map = $_POST['start_conditions'];
        //  $map = Map::find(38);
        //  $coords = $map->getCoordinates();
        //  $map2 = Map::find(39);
        //  $coords2 = $map2->getCoordinates();
        //  $map3 = Map::find(40);
        //  $coords3 = $map3->getCoordinates();
        //  $map4 = Map::find(41);
        //  $coords4 = $map4->getCoordinates();
        //  $map5 = Map::find(42);
        //  $coords5 = $map5->getCoordinates();
        //  // var_dump($coords3);
        //  $loser_moves = Network::parse_training_grid($coords)[0];
        //  $winner_moves = Network::parse_training_grid($coords)[1];
        //  $loser_moves2 = Network::parse_training_grid($coords2)[0];
        //  $winner_moves2 = Network::parse_training_grid($coords2)[1];
        //  $loser_moves3 = Network::parse_training_grid($coords3)[0];
        //  $winner_moves3 = Network::parse_training_grid($coords3)[1];
        //  $loser_moves4 = Network::parse_training_grid($coords4)[0];
        //  $winner_moves4 = Network::parse_training_grid($coords4)[1];
        //  $loser_moves5 = Network::parse_training_grid($coords5)[0];
        //  $winner_moves5 = Network::parse_training_grid($coords5)[1];
        //
        // //  $player_moves = Network::parse_playing_grid($player_map);
        //
        //  for($i=0;$i<1;$i++){
        //    $network->backprop($loser_moves, $winner_moves, 1);
        //    $network->backprop($loser_moves2, $winner_moves2, 1);
        //    $network->backprop($loser_moves3, $winner_moves3, 1);
        //    $network->backprop($loser_moves4, $winner_moves4, 1);
        //    $network->backprop($loser_moves5, $winner_moves5, 1);
        //  }
        //  $confidence_array = ($network->feedforward($loser_moves5));
        //  arsort($confidence_array);
        // //  var_dump($confidence_array);
        //  $computer_move1x;
        //  $computer_move1y;
        //  $computer_move2x;
        //  $computer_move2y;
        //  $computer_move3x;
        //  $computer_move3y;
        //  $i = 0;
        //  foreach($confidence_array as $key => $value) {
        //     if ($i == 0) {
        //       $x_coord = $key % 20;
        //       $computer_move1x = $x_coord;
        //       $y_coord = ($key-$x_coord)/20;
        //       $computer_move1y = $y_coord;
        //     } elseif ($i == 1) {
        //       $x_coord = $key % 20;
        //       $computer_move2x = $x_coord;
        //       $y_coord = ($key-$x_coord)/20;
        //       $computer_move2y = $y_coord;
        //     } elseif ($i == 2) {
        //       $x_coord = $key % 20;
        //       $computer_move3x = $x_coord;
        //       $y_coord = ($key-$x_coord)/20;
        //       $computer_move3y = $y_coord;
        //     } else {
        //       $computer_moves = [[$computer_move1x,$computer_move1y,1],[$computer_move2x,$computer_move2y,1],[$computer_move3x,$computer_move3y,1]];
        //       break;
        //     }
        //     $i++;
        //  }


        //  $computer_moves = [[$computer_move1x,$computer_move1y,1],[$computer_move2x,$computer_move2y,1],[$computer_move3x,$computer_move3y,1]];
        //








        return $app["twig"]->render("root.html.twig", ['user' => $_SESSION['user'], 'edit' => false]);
    });

    $app->post('/start_computer_game', function() use($app) {
      $network = new Network([400,100,100,400]);

       $player_map = $_POST['start_conditions'];
       $map = Map::find(38);
       $coords = $map->getCoordinates();
       $map2 = Map::find(39);
       $coords2 = $map2->getCoordinates();
       $map3 = Map::find(40);
       $coords3 = $map3->getCoordinates();
       $map4 = Map::find(41);
       $coords4 = $map4->getCoordinates();
       $map5 = Map::find(42);
       $coords5 = $map5->getCoordinates();
       // var_dump($coords3);
       $loser_moves = Network::parse_training_grid($coords)[0];
       $winner_moves = Network::parse_training_grid($coords)[1];
       $loser_moves2 = Network::parse_training_grid($coords2)[0];
       $winner_moves2 = Network::parse_training_grid($coords2)[1];
       $loser_moves3 = Network::parse_training_grid($coords3)[0];
       $winner_moves3 = Network::parse_training_grid($coords3)[1];
       $loser_moves4 = Network::parse_training_grid($coords4)[0];
       $winner_moves4 = Network::parse_training_grid($coords4)[1];
       $loser_moves5 = Network::parse_training_grid($coords5)[0];
       $winner_moves5 = Network::parse_training_grid($coords5)[1];

       $player_moves = Network::parse_playing_grid($player_map);

       for($i=0;$i<10;$i++){
         $network->backprop($loser_moves, $winner_moves, 1);
         $network->backprop($loser_moves2, $winner_moves2, 1);
         $network->backprop($loser_moves3, $winner_moves3, 1);
         $network->backprop($loser_moves4, $winner_moves4, 1);
         $network->backprop($loser_moves5, $winner_moves5, 1);
       }
       $confidence_array = ($network->feedforward($player_moves));
       arsort($confidence_array);
      //  var_dump($confidence_array);
       $computer_move1x;
       $computer_move1y;
       $computer_move2x;
       $computer_move2y;
       $computer_move3x;
       $computer_move3y;
       $i = 0;
       foreach($confidence_array as $key => $value) {
          if ($i == 0) {
            $x_coord = $key % 20;
            $computer_move1x = $x_coord;
            $y_coord = ($key-$x_coord)/20;
            $computer_move1y = $y_coord;
          } else {
            $computer_moves = [[$computer_move1x,$computer_move1y,1]];
            break;
          }
          $i++;
       }

       return json_encode($computer_moves);

    });

    $app->get('/hello', function() use($app) {
        $result = 'hello';
        return $result;
    });
    $app->get('/deleteAll', function() use($app) {
        Map::deleteAll();
        return $app->redirect("/");
    });

    $app->post('/save_map', function() use ($app){
        //will save map here

        $map = new Map($_POST['title'], $_POST['type'], 0, $_SESSION['user']->getId(), 0, 0, $_POST['map']);

        $map->save();

        return json_encode($_POST['map']);
    });

    $app->post('/save_game', function() use($app) {
        $_SESSION['user']->saveGame($_POST['start_conditions'], $_POST['map_id'], $_POST['winner_score'], $_POST['player_int'], $_POST['winner']);

        return json_encode([$_POST['start_conditions'], $_POST['map_id'], $_POST['winner_score'], $_POST['player_int'], $_POST['winner']]);
    });

    $app->get('/load_map', function() use($app) {
        $maps = Map::getAll();
        return $app['twig']->render('all_maps.html.twig', ['maps' => $maps, 'user'=>$_SESSION['user']]);
    });


    $app->get('/play/{type}/{id}', function($id) use($app) {

        return $app['twig']->render('root.html.twig', ['user'=>$_SESSION['user'], 'edit' => false]);
    });

    $app->post('/getMap/{id}', function($id) use($app) {
        $map = Map::find($id);
        $response = $map->getCoordinates();
        return json_encode($response);
    });

    $app->get('/create_map', function() use($app) {
        return $app['twig']->render('root.html.twig', ['edit' => true, 'user'=>$_SESSION['user']]);
    });

    $app->post('/sign_up', function() use ($app) {
        $user = new User($_POST['username'], $_POST['password']);
        $user->save();
        return $app->redirect("/");
    });

    $app->post('/log_in', function() use ($app) {
        User::logIn($_POST['username'], $_POST['password']);
        return $app->redirect("/");
    });

    $app->post('/log_out', function() use ($app) {
        $_SESSION['user']->logOut();
        return $app->redirect("/");
    });



    return $app;
?>
