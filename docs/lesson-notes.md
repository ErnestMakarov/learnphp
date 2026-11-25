LESONS NOTES


---LESON 1---

installing vscode, node and git

php project creating

<?php
    echo "Hello, World!";
?>

php -S localhost:9999

---LESON 2---

<?php
    function Hello() {
        var_dump('Hello!')
    }

    Hello();
    Hello();

    function square($a) {
        if (is_numeric($a)) {
            return $a * $a;
        } else {
            return 'Error: Input must be a number.';
        }
    }
?>

<?php
    function hello() {
        var_dump("Hello!");
    }
 
    hello();
    hello();
 
    function square(5a) {
        if ($a<0){
            retunr 0;
        }
        return $a * $a;
        var_dump('asdasdads')
    }
 
    $answer = square(4);
    var_dump($answer);
    var_dump(square(5));
 
?>


