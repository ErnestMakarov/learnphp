<?php
    function Hello() {
        var_dump('Hello!');
    }

    Hello();
    Hello();

    function square($a) {
        if ($a<0){
            echo 'Error: Negative number';
        }
        return $a * $a;
        var_dump('asdasdads');
    }
 
    $answer = square(4);
    var_dump($answer);
    var_dump(square(5));

    for ($i=0; $i<10; $i++) {
        var_dump($i);
    }

    class Box {

        public function __construct( public int $width, private int $height, protected int $length) {
            var_dump('Box created');
        }

        public function volume() {
            return $this->width * $this->height * $this->length;
        }
    }
?>
