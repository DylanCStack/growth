<?php
    class Network {
        public $num_layers;
        public $sizes;
        public $biases;
        public $weights;
        public $activations;
        public $zs;
        public $nabla_b;
        public $nabla_w;

        function __construct($sizes) {
            $this->num_layers = sizeof($sizes);
            $this->sizes = $sizes;
            $this->biases = [];

            for($i = 0; $i<$this->num_layers-1; $i++) {
                array_push($this->biases, []);
                for($j = 0; $j<$sizes[$i+1]; $j++) {
                    array_push($this->biases[$i], rand(0,100)/100);
                }
            }

            $this->nabla_b=[];
            for($i = 0; $i<$this->num_layers-1; $i++) {
                array_push($this->nabla_b, []);
                for($j = 0; $j<$sizes[$i+1]; $j++) {
                    array_push($this->nabla_b[$i], 0);
                }
            }

            $this->activations = [];
            for($i = 0; $i<$this->num_layers; $i++) {
                array_push($this->activations, []);
                for($j = 0; $j<$sizes[$i]; $j++) {
                    array_push($this->activations[$i], 0);
                }
            }

            $this->zs = [];
            for($i = 0; $i<$this->num_layers; $i++) {
                array_push($this->zs, []);
                for($j = 0; $j<$sizes[$i]; $j++) {
                    array_push($this->zs[$i], 0);
                }
            }

            $this->weights = [];
            for($i = 0; $i<$this->num_layers-1; $i++) {
                array_push($this->weights, []);
                for($j = 0; $j<$sizes[$i+1]; $j++) {
                    array_push($this->weights[$i], []);
                    for($k = 0; $k<$sizes[$i]; $k++){
                        array_push($this->weights[$i][$j], rand(-100,100)/100);
                    }
                }
            }

            $this->nabla_w = [];
            for($i = 0; $i<$this->num_layers-1; $i++) {
                array_push($this->nabla_w, []);
                for($j = 0; $j<$sizes[$i+1]; $j++) {
                    array_push($this->nabla_w[$i], []);
                    for($k = 0; $k<$sizes[$i]; $k++){
                        array_push($this->nabla_w[$i][$j], 0);
                    }
                }
            }
        }


        function feedforward($a){
            for ($i = 0; $i<$this->sizes[0]; $i++) {
                $this->activations[0][$i] = $a[$i];
            }

            for ($i = 1; $i<$this->num_layers; $i++){
                for($j = 0; $j<($this->sizes[$i]); $j++){
                    $interim_activations = [];
                    foreach($this->activations[$i-1] as $individual){
                        array_push($interim_activations, [$individual]);
                    }
                    $interim_weights = [$this->weights[$i-1][$j]];
                    $dot_product = Network::dot($interim_weights, $interim_activations);
                    $this->activations[$i][$j] = Network::sigmoid(($dot_product[0][0])+$this->biases[$i-1][$j]);
                }
            }
            $last = sizeof($this->activations);
            return $this->activations[$last-1];
        }

        function backprop($x, $y, $eta) {
            $activation = $x;

            for ($i = 0; $i<$this->sizes[0]; $i++) {
                $this->activations[0][$i] = $activation[$i];
            }

            for ($i = 1; $i<$this->num_layers; $i++){
                for($j = 0; $j<($this->sizes[$i]); $j++){
                    $interim_activations = [];
                    foreach($this->activations[$i-1] as $individual){
                        array_push($interim_activations, [$individual]);
                    }
                    $interim_weights = [$this->weights[$i-1][$j]];
                    $dot_product = Network::dot($interim_weights, $interim_activations);
                    $dot_product = $dot_product[0][0];
                    $this->zs[$i][$j] = $dot_product;
                    $this->activations[$i][$j] = Network::sigmoid(($dot_product)+$this->biases[$i-1][$j]);
                }
            }

            $delta = [];
            $last_layer = sizeof($this->zs)-1;
            $pre_delta = Network::cost_derivative($y);
            for ($i=0;$i<sizeof($pre_delta);$i++){
                array_push($delta, $pre_delta[$i] * Network::sigmoid_prime($this->zs[$last_layer][$i]));
            }

            $last_layer = sizeof($this->nabla_b)-1;
            // var_dump($delta);
            $this->nabla_b[$last_layer] = $delta;
            // var_dump($this->nabla_b[$last_layer]);
            // var_dump($this->nabla_b);


            $delta_transform = [];
            foreach($delta as $d){
                array_push($delta_transform, [$d]);
            }

            $last_layer = sizeof($this->nabla_w)-1;
            $this->nabla_w[$last_layer] = Network::dot($delta_transform, [$this->activations[sizeof($this->activations)-2]]);
            // var_dump($this->nabla_w);

            for($i = $this->num_layers-2;$i>0;$i--){
                // var_dump($this->zs[$i]);
                $z = $this->zs[$i];
                $sp = [];
                foreach($z as $c){
                    array_push($sp, Network::sigmoid_prime($c));
                }
                // var_dump($sp);


                $new_weights = [];
                for($k=0;$k<sizeof($this->weights[$i][0]);$k++) {
                    array_push($new_weights, []);
                    for($j=0;$j<sizeof($this->weights[$i]);$j++) {
                        array_push($new_weights[$k], $this->weights[$i][$j][$k]);
                    }
                }
                // var_dump($delta_transform);
                // var_dump($new_weights);
                $delta_transform = Network::dot($new_weights, $delta_transform);
                for($j=0;$j<sizeof($delta_transform);$j++){
                  $delta_transform[$j][0] = $delta_transform[$j][0]*$sp[$j];
                }

                // var_dump($this->nabla_b);
                for($j=0;$j<sizeof($delta_transform);$j++){
                  $this->nabla_b[$i-1][$j] = $delta_transform[$j][0];
                }

                // var_dump($this->biases);
                // var_dump($delta_transform);
                // var_dump([$this->activations[$i-1]]);
                // var_dump($this->nabla_w[$i]);
                $temp_var = Network::dot($delta_transform, [$this->activations[$i-1]]);
                // var_dump($temp_var);
                $this->nabla_w[$i-1] = $temp_var;
                // var_dump($this->nabla_w[$i-1]);

            }


            for($i = 0; $i<$this->num_layers-2; $i++) {
                for($j = 0; $j<$this->sizes[$i+1]; $j++) {
                  // var_dump($this->biases[$i]);a
                    // var_dump($this->nabla_b[$i][$j]);
                    $this->biases[$i][$j] = $this->biases[$i][$j]-($eta*$this->nabla_b[$i][$j]);
                }
            }

            for($i = 0; $i<$this->num_layers-1; $i++) {
                for($j = 0; $j<$this->sizes[$i+1]; $j++) {
                    for($k = 0; $k<$this->sizes[$i]; $k++){
                      // var_dump($this->nabla_w[$i][$j][$k]);
                        $this->weights[$i][$j][$k] = $this->weights[$i][$j][$k]-($eta*$this->nabla_w[$i][$j][$k]);
                    }
                }
            }

        }

















        function cost_derivative($training_y){
            $last_layer = sizeof($this->activations) - 1;
            $output_activations = $this->activations[$last_layer];
            $cost_derivative_array = [];
            for($i=0;$i<sizeof($training_y);$i++) {
                array_push($cost_derivative_array, ($output_activations[$i]-$training_y[$i]));
            }
            return $cost_derivative_array;
        }

        static function sigmoid($z) {
            $value =  1.0/(1.0+exp(-$z));
            return $value;
        }

        static function sigmoid_prime($z) {
            $value = Network::sigmoid($z)*(1-Network::sigmoid($z));
            return $value;
        }

        static function dot($array1, $array2){
            $result_array = [];
            for($i=0;$i<sizeof($array1);$i++){
                array_push($result_array, []);
                for($j=0;$j<sizeof($array2[0]);$j++){
                    array_push($result_array[$i], 0);
                }
            }

            for($i=0;$i<sizeof($array1);$i++){
                for($j=0;$j<sizeof($array2[0]);$j++){
                    for($k=0;$k<sizeof($array1[$i]);$k++){
                        $result_array[$i][$j] += ($array1[$i][$k]*$array2[$k][$j]);
                    }
                }
            }

            return $result_array;

        }


        static function parse_training_grid($response_array){
          $player1_result = [];
          $player2_result = [];
          for($i=0;$i<20;$i++) {
            for($j=0;$j<20;$j++) {
              array_push($player1_result, 0);
              array_push($player2_result, 0);
            }
          }

          for($k=0;$k<6;$k++) {
            for($i=0;$i<20;$i++) {
              for($j=0;$j<20;$j++) {
                if($response_array[$k][0] == $i && $response_array[$k][1] == $j && $response_array[$k][2] == 0) {
                  $player1_result[($i*20) + $j] = 1;
                }
              }
            }
          }

          for($k=0;$k<6;$k++) {
            for($i=0;$i<20;$i++) {
              for($j=0;$j<20;$j++) {
                if($response_array[$k][0] == $i && $response_array[$k][1] == $j && $response_array[$k][2] == 1) {
                  $player2_result[($i*20) + $j] = 1;
                }
              }
            }
          }

          return [$player1_result, $player2_result];
        }

        static function parse_playing_grid($response_array){
          $player1_result = [];
          for($i=0;$i<20;$i++) {
            for($j=0;$j<20;$j++) {
              array_push($player1_result, 0);
            }
          }

          for($k=0;$k<sizeof($response_array);$k++) {
            for($i=0;$i<20;$i++) {
              for($j=0;$j<20;$j++) {
                if($response_array[$k][0] == $i && $response_array[$k][1] == $j && $response_array[$k][2] == 0) {
                  $player1_result[($i*20) + $j] = 1;
                }
              }
            }
          }
          return $player1_result;
        }


    }


?>
