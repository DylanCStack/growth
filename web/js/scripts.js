var seed = Math.random();
var erase = false;

function mapLoading(game){
    var url = window.location.href;
    var id = url.substring(url.search(/\d+$/));

    $.post('/getMap/'+ id, {'id': id}, function(response) {
        console.log(response);
        map = JSON.parse(response);
        console.log(map);
        console.log(map.length);
        var activeTiles = map.length;
        if(activeTiles > 0){
            for(var i = 0; i < activeTiles; i ++){
                game.board.grid[map[i][0]][map[i][1]].active = true;
                game.board.grid[map[i][0]][map[i][1]].player = parseInt(map[i][2]);
                // console.log(map[i][2]);
                game.playerArray[map[i][2]].score ++;

            }

        }
    });


}

function Game() {
    this.board = new Board(20,20)
    this.log = [];
    this.playerArray = [new Player(0,"string"), new Player(1,"potato"), new Player(2, "wall"), new Player(3, "string")];
    this.activePlayer = 0;
    this.run = false;
    this.historyArray = [[],[]];
    this.startConditions = [];
    this.winner = 0;
    this.steps = 0;
    this.gameOver = false;

}
Game.prototype.endGame = function() {
    if(((this.board.x * this.board.y) - this.playerArray[2].score) === (this.playerArray[0].score + this.playerArray[1].score)){
        if(this.playerArray[0].score > this.playerArray[1].score) {
            this.winner = 0;
        } else {
            this.winner = 1;
        }
        $("#end-game").text("GAME OVER Player 1 score: "+this.playerArray[0].score+" Player 2 score: "+this.playerArray[1].score);
        //post game data
        var winner_score = this.playerArray[0].score;
        var winner = 0;
        if( this.playerArray[0].score < this.playerArray[1].score){
            winner_score = this.playerArray[1].score;
            winner = 1;
        }

        var url = window.location.href;
        var map_id = url.substring(url.search(/\d+$/));
        var player_int = url.substring(url.search(/\d+(?=\/\d)/), url.search(/\d+(?=\/\d)/)+1);
        // var player_int = url.substring(url.search(/\d+(?=\/\d\/)/),url.search(/\d+(?=\/\d\/)/)+1);

        if(!(this.gameOver)){
            $.post('/save_game', {'start_conditions': this.startConditions, 'map_id': map_id,'player_int': player_int, 'winner_score': winner_score, 'winner': winner}, function(response) {
                console.log(JSON.parse(response));
            });
            this.gameOver = true;
        }

    }
}


Game.prototype.startComputer = function() {
  this.saveStartConditions();
  var game = this;
    $.post('/start_computer_game', {'start_conditions': this.startConditions}, function(response) {
      console.log(response)
       var map = JSON.parse(response);
       console.log(map);
       console.log(map.length);
       var activeTiles = map.length;
       if(activeTiles > 0){
           for(var i = 0; i < activeTiles; i ++){
               game.board.grid[map[i][0]][map[i][1]].active = true;
               game.board.grid[map[i][0]][map[i][1]].player = parseInt(map[i][2]);
               // console.log(map[i][2]);
               game.playerArray[map[i][2]].score ++;

           }

       }
    });

}


Game.prototype.saveStartConditions = function(){

     for (var i=0;i<this.board.x;i++) {
         for (var j=0;j<this.board.y;j++) {
             if(this.board.grid[i][j].active){
                 this.startConditions.push([i,j,this.board.grid[i][j].player]);
             }
         }
     }
}

Game.prototype.playerClick = function(canvas) {
    var game = this;
    $("canvas").click(function(e){
      var bb = canvas.getBoundingClientRect();
      var x = e.clientX - bb.left;
      var y = e.clientY - bb.top;
      x = Math.floor(x/(500/game.board.x));
      y = Math.floor(y/(500/game.board.y));
      if(erase) {
        game.board.grid[x][y].active = false;
        game.playerArray[game.board.grid[x][y].player].score --;
        game.board.grid[x][y].player = 3;
      }else if(!(game.board.grid[x][y].active)){
        game.board.grid[x][y].active = true;
        game.board.grid[x][y].player = game.activePlayer;
        game.playerArray[game.board.grid[x][y].player].score ++;
        // game.historyArray[game.board.grid[x][y].player].push([x,y]);

      }
    });
    $("canvas").mousedown(function(){//IMPERATIVE
      $('canvas').mousemove(function(e){//DANGER SEE IMPERATIVES
        var bb = canvas.getBoundingClientRect();
        var x = e.clientX - bb.left;
        var y = e.clientY - bb.top;
        x = Math.floor(x/(500/game.board.x));
        y = Math.floor(y/(500/game.board.y));
        if(erase) {
          game.board.grid[x][y].active = false;
          game.playerArray[game.board.grid[x][y].player].score --;
          game.board.grid[x][y].player = 3;
        }else if(!(game.board.grid[x][y].active)){
          game.board.grid[x][y].active = true;
          game.board.grid[x][y].player = game.activePlayer;
          game.playerArray[game.board.grid[x][y].player].score ++;
          // game.historyArray[game.board.grid[x][y].player].push([x,y]);

        }
      });
    });
    $(document).mouseup(function(){//ALSO IMPERATIVE
      $("canvas").unbind("mousemove");
    })
};

Game.prototype.generateWalls = function(canvas){
  // this.activePlayer = 2;
  // var x = this.board.x-1;
  // var y = this.board.y-1;
  // for (var i=0;i<this.board.x;i++) {
  //     for (var j=0;j<this.board.y;j++) {
  //       if(seed > Math.random() && !(this.board.grid[x][y].active)){
  //         this.board.grid[x][y].active = true;
  //         this.board.grid[x][y].player = this.activePlayer;
  //         this.playerArray[this.board.grid[x][y].player].score ++;
  //
  //       }
  //     }
  // }
    // game.historyArray[game.board.grid[x][y].player].push([x,y]);
}
Game.prototype.saveConditions = function(){
    var conditions = [];
     for (var i=0;i<this.board.x;i++) {
         for (var j=0;j<this.board.y;j++) {
             if(this.board.grid[i][j].active){
                 conditions.push([i,j,this.board.grid[i][j].player]);
             }
         }
     }
     var title = $("#title").val();
     var type = $("#type").val();
     $.post("/save_map", {"map":conditions, "title":title, "type":type}, function(response){
         console.log(response);
         console.log("-----------Parsed response below, unparsed above-------------");
         var parsedResponse = JSON.parse(response);
         console.log(parsedResponse);
        for(var i = 0; i < parsedResponse.length; i++){
          console.log("Its an array");
        }
        console.log("--------")
     })

}
Game.prototype.saveGame = function(){
    var conditions = [];
     for (var i=0;i<this.board.x;i++) {
         for (var j=0;j<this.board.y;j++) {
             if(this.board.grid[i][j].active){
                 conditions.push([i,j,this.board.grid[i][j].player]);
             }
         }
     }
     var title = $("#title").val();
     var type = $("#type").val();
     $.post("/save_map", {"map":conditions, "title":title, "type":type}, function(response){
         console.log(response);
         console.log("-----------Parsed response below, unparsed above-------------");
         var parsedResponse = JSON.parse(response);
         console.log(parsedResponse);
        for(var i = 0; i < parsedResponse.length; i++){
          console.log("Its an array");
        }
        console.log("--------")
     })
}


function Player(id,style) {
    this.id = id,
    this.style = "linear",
    this.score = 0,
    this.active = false
    // this.history =
}

function Board(x,y) {
    this.x = x,
    this.y = y,
    this.grid = createArray(this.x,this.y);
}


Board.prototype.grow = function(game) {
    var coords = [];
    for (var i=0;i<this.x;i++) {
        for (var j=0;j<this.y;j++) {
            if(this.grid[i][j].active && this.grid[i][j].player != 2){
            // if((this.grid[i][j+1].active && this.grid[i][j-1].active && this.grid[i+1][j].active && this.grid[i-1][j].active) && (this.grid[i][j+1].player === this.grid[i][j-1].player && this.grid[i+1][j].player === this.grid[i-1][j].player)) {
            //     this.grid[i][j].active = true;
            //     this.grid[i][j].player = this.grid[i][j+1].player;
            // }
                this.grid[i][j].age ++;
                // if(i>0 && i < this.x-1 && j>0 && j<this.y-1) {
                //     var direction = Math.floor(Math.random()*4);
                //     if (direction === 3) {
                //         coords.push([i-1,j]);
                //         this.grid[i-1][j].player = this.grid[i][j].player;
                //         this.grid[i-1][j].seed = this.grid[i][j].seed;
                //         this.grid[i][j].active = false;
                //     } else if (direction === 2) {
                //         coords.push([i+1,j]);
                //         this.grid[i+1][j].player = this.grid[i][j].player;
                //         this.grid[i+1][j].seed = this.grid[i][j].seed;
                //         this.grid[i][j].active = false;
                //
                //     } else if (direction === 1) {
                //         coords.push([i,j-1]);
                //         this.grid[i][j-1].player = this.grid[i][j].player;
                //         this.grid[i][j-1].seed = this.grid[i][j].seed;
                //         this.grid[i][j].active = false;
                //
                //     } else if (direction === 0) {
                //         coords.push([i,j+1]);
                //         this.grid[i][j+1].player = this.grid[i][j].player;
                //         this.grid[i][j+1].seed = this.grid[i][j].seed;
                //         this.grid[i][j].active = false;
                //
                //     }
                // }
                if(i>0){
                    if(!(this.grid[i-1][j].active)){
                        if((Math.random()-this.grid[this.grid[i][j].seed[0]][this.grid[i][j].seed[1]].age/200)>.5){
                            coords.push([i-1,j]);
                            this.grid[i-1][j].player = this.grid[i][j].player;
                            this.grid[i-1][j].seed = this.grid[i][j].seed;
                        }
                    }
                }
                if(i<this.x-1){
                    if(!(this.grid[i+1][j].active)){
                        if((Math.random()-this.grid[this.grid[i][j].seed[0]][this.grid[i][j].seed[1]].age/200)>.5){
                            coords.push([i+1,j]);
                            this.grid[i+1][j].player = this.grid[i][j].player;
                            this.grid[i+1][j].seed = this.grid[i][j].seed;
                        }
                    }
                }
                if(j>0){
                    if(!(this.grid[i][j-1].active)){
                        if((Math.random()-this.grid[this.grid[i][j].seed[0]][this.grid[i][j].seed[1]].age/200)>.5){
                            coords.push([i,j-1]);
                            this.grid[i][j-1].player = this.grid[i][j].player;
                            this.grid[i][j-1].seed = this.grid[i][j].seed;
                        }
                    }
                }
                if(j<this.y-1){
                    if(!(this.grid[i][j+1].active)){
                        if((Math.random()-this.grid[this.grid[i][j].seed[0]][this.grid[i][j].seed[1]].age/200)>.5){
                            coords.push([i,j+1]);
                            this.grid[i][j+1].player = this.grid[i][j].player;
                            this.grid[i][j+1].seed = this.grid[i][j].seed;
                        }
                    }
                }
            }
        }
    }
    this.spread(coords, game);
}
Board.prototype.spread = function(coords, game) {
    for (var i=0;i<coords.length;i++) {
        if(!(this.grid[coords[i][0]][coords[i][1]].active)){
            this.grid[coords[i][0]][coords[i][1]].active = true;
            game.playerArray[this.grid[coords[i][0]][coords[i][1]].player].score ++;

        }

    }
}

Board.prototype.fill = function() {
    for (var i=0;i<this.x;i++) {
        for (var j=0;j<this.y;j++) {
            this.grid[i][j] = new Tile(this.x,this.y,i,j);
        }
    }
}

Board.prototype.draw = function(ctx) {
    for (var i=0;i<this.x;i++) {
        for (var j=0;j<this.y;j++) {
            this.grid[i][j].draw(ctx);
        }
    }
}

function createArray(length) {
    var arr = new Array(length || 0),
    i = length;

    if (arguments.length > 1) {
        var args = Array.prototype.slice.call(arguments, 1);
        while(i--) arr[length-1 - i] = createArray.apply(this, args);
    }

    return arr;
}

function Tile(xWidth,yWidth,xPos,yPos) {
    this.xPos=(500/xWidth)*xPos,
    this.yPos=(500/yWidth)*yPos,
    this.height=(500/xWidth)
    this.width=(500/yWidth),
    this.active = false;
    this.age = 0;
    this.player = 3;
    this.seed = [0,0]
}

Tile.prototype.draw = function(ctx) {
    ctx.beginPath();
    ctx.rect(this.xPos,this.yPos,this.width,this.height);
    if(this.player === 0){
        ctx.fillStyle = "rgba(0,0,220,.7)";
    } else if (this.player === 1) {
        ctx.fillStyle = "rgba(220,0,0,.7)";
    } else if (this.player === 2) {
        ctx.fillStyle = "rgba(150,150,150,1)";
    } else {
        ctx.fillStyle = "rgba(255,255,255,1)";
    }
    if(this.active) {
        ctx.fill();
    }
    ctx.strokeStyle = "rgba(10,10,10,.5)"
    ctx.stroke();
    ctx.closePath();
}




$(document).ready(function(){
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    // var steps = 110;
    var game = new Game;
    game.board.fill();

    game.playerClick(canvas);
    game.generateWalls(canvas);
    mapLoading(game);

    function draw(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        if (game.run){
            game.board.grow(game);
            // steps += 1;
        }
        // console.log(steps);
        // if (steps > 20) {
            // game.run = false;
            // steps = 0;
            // console.log(game.historyArray);
        // }
        game.board.draw(ctx);
        game.endGame();
        // debugger;
    }

    $('#player1').click(function(){
        game.activePlayer = 0;
        erase = false;
    })

    $('#player2').click(function(){
        game.activePlayer = 1;
        erase = false;
    })
    $('#wall').click(function(){
        game.activePlayer = 2;
        erase = false;
    })
    $('#erase').click(function(){
      game.activePlayer = 3;
      erase = true;
    })

    drawInterval = setInterval(draw, 100);

    $('#start').click(function(){
        game.run = true;
        game.saveStartConditions();
    })
    $('#save').click(function(){
        game.saveConditions();
        //
    })

    $('#computer_moves').click(function(){
       game.startComputer();
    })
})
