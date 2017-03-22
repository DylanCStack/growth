
function Game() {
    this.board = new Board(10,10)
    this.log = [];
    this.playerArray = [new Player(0,"string"), new Player(1,"potato")];
    this.activePlayer = 0;
    this.run = false;
    this.historyArray = [[],[]];
    this.startConditions = [];
    this.winner = 0;
    this.steps = 0;
    this.gameOver = false;
    this.thoughts = new Thoughts(10,10);

}
Game.prototype.endGame = function() {
    if(((this.board.x * this.board.y)) === (this.playerArray[0].score + this.playerArray[1].score)){
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
        if(!(this.gameOver)){
            $.post('/save_game', {'start_conditions': this.startConditions, 'winner_score': winner_score, 'winner': winner}, function(response) {
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
           for(var i = 0; i < 3; i ++){
               game.board.grid[map[i][0]][map[i][1]].active = true;
               game.board.grid[map[i][0]][map[i][1]].player = 1;
               game.board.grid[map[i][0]][map[i][1]].age = 1;

               // console.log(map[i][2]);
               game.playerArray[map[i][2]].score ++;

           }
           for(var i = 3; i < 103; i ++){
               game.thoughts.grid[map[i][0]][map[i][1]].active = true;
               game.thoughts.grid[map[i][0]][map[i][1]].player = 1;
               game.thoughts.grid[map[i][0]][map[i][1]].age = map[i][2];

               // console.log(map[i][2]);

           }


       }

       setTimeout(function() {
         game.saveStartConditions();
         game.run = true;

       }, 1000);
    });

}

Game.prototype.trainNetwork = function() {
  this.saveStartConditions();
  var game = this;
    $.post('/trainNetwork', {'start_conditions': this.startConditions}, function(response) {
        console.log(response)
    })

}

Game.prototype.saveStartConditions = function(){
    this.startConditions = [];
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
      if(!(game.board.grid[x][y].active)){
        game.board.grid[x][y].active = true;
        game.board.grid[x][y].player = game.activePlayer;
        game.board.grid[x][y].age = 1;
        game.playerArray[game.board.grid[x][y].player].score ++;
        // game.historyArray[game.board.grid[x][y].player].push([x,y]);

      }
    });
};



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

function Thoughts(x,y) {
    this.x = x,
    this.y = y,
    this.grid = createArray(this.x,this.y);
}

Board.prototype.grow = function(game) {
    var coords = [];
    for (var i=0;i<this.x;i++) {
        for (var j=0;j<this.y;j++) {
            if(this.grid[i][j].active){
                if(i>0){
                    if(!(this.grid[i-1][j].active)){
                        if(Math.random()>.5){
                            coords.push([i-1,j]);
                            this.grid[i-1][j].player = this.grid[i][j].player;
                        }
                    }
                }
                if(i<this.x-1){
                    if(!(this.grid[i+1][j].active)){
                        if(Math.random()>.5){
                            coords.push([i+1,j]);
                            this.grid[i+1][j].player = this.grid[i][j].player;
                        }
                    }
                }
                if(j>0){
                    if(!(this.grid[i][j-1].active)){
                        if(Math.random()>.5){
                            coords.push([i,j-1]);
                            this.grid[i][j-1].player = this.grid[i][j].player;
                        }
                    }
                }
                if(j<this.y-1){
                    if(!(this.grid[i][j+1].active)){
                        if(Math.random()>.5){

                            coords.push([i,j+1]);
                            this.grid[i][j+1].player = this.grid[i][j].player;
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

Thoughts.prototype.fill = function() {
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
Thoughts.prototype.draw = function(ttx){
    for (var i=0;i<this.x;i++) {
        for (var j=0;j<this.y;j++) {
            this.grid[i][j].draw(ttx);
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
    this.age = 1;
    this.player = 3;
    this.seed = [0,0]
}

Tile.prototype.draw = function(ctx) {
    ctx.beginPath();
    ctx.rect(this.xPos,this.yPos,this.width,this.height);
    if(this.player === 0){
        ctx.fillStyle = "rgba(0,0,220,.7)";
    } else if (this.player === 1) {
        ctx.fillStyle = "rgba(220,0,0,"+this.age+")";
      }
    if(this.active) {
        ctx.fill();
    }
    ctx.strokeStyle = "rgba(255,255,255,1)"
    ctx.stroke();
    ctx.closePath();
}




$(document).ready(function(){
  var canvas = document.getElementById("canvas");
  var thoughts = document.getElementById("thoughts");
    var ctx = canvas.getContext("2d");
    var ttx = thoughts.getContext("2d");
    var game = new Game;
    game.board.fill();
    game.thoughts.fill();

    game.playerClick(canvas);

    function draw(){
      ctx.clearRect(0,0,canvas.width,canvas.height);
        ttx.clearRect(0,0,canvas.width,canvas.height);
        if (game.run){
            game.board.grow(game);
        }
        game.board.draw(ctx);
        game.thoughts.draw(ttx);
        game.endGame();
        console.log(game.thoughts.grid[0][0].age);
    }

    $('#player1').click(function(){
        game.activePlayer = 0;
        erase = false;
    })

    $('#player2').click(function(){
        game.activePlayer = 1;
        erase = false;
    })

    drawInterval = setInterval(draw, 100);

    $('#start').click(function(){
      game.saveStartConditions();
      game.run = true;
    })


    $('#trainNetwork').click(function(){
        game.trainNetwork();
    })

    $('#computer_moves').click(function(){
       game.startComputer();
    })
})
