
<?php
    function Hello() {
        var_dump('Hello!')
    }

    Hello();
    Hello();

    function square($a) {
        if ($a<0){
            retunr 0;
        }
        return $a * $a;
        var_dump('asdasdads')
    }
 
    $answer = square(4);
    var_dump($answer);
    var_dump(square(5));

    for ($i=0; $i<10; $i++) {
        var_dump($i);
    }

    

?>


