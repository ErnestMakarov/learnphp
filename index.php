<<<<<<< HEAD
<?php
    function Hello() {
        var_dump('Hello!');
=======

<?php
    function Hello() {
        var_dump('Hello!')
>>>>>>> e066de84ea5e04062fa242b52eb251a34e89e5d3
    }

    Hello();
    Hello();

    function square($a) {
        if ($a<0){
<<<<<<< HEAD
            echo 'Error: Negative number';
        }
        return $a * $a;
        var_dump('asdasdads');
=======
            retunr 0;
        }
        return $a * $a;
        var_dump('asdasdads')
>>>>>>> e066de84ea5e04062fa242b52eb251a34e89e5d3
    }
 
    $answer = square(4);
    var_dump($answer);
    var_dump(square(5));

    for ($i=0; $i<10; $i++) {
        var_dump($i);
    }
<<<<<<< HEAD
?>
=======

    

?>


>>>>>>> e066de84ea5e04062fa242b52eb251a34e89e5d3
