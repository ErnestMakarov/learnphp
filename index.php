
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
