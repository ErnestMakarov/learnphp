<?php

// Run separately: php exercises/basics.php. These examples are not loaded by the site.
declare(strict_types=1);

namespace Lessons;

// Types, arithmetic, strings and arrays (04–09 September).
$count = 3;
$price = 2.5;
$enabled = true;
$nothing = null;
$student = ['name' => 'Ernest', 'subjects' => ['PHP', 'SQL']];
var_dump($count, $price, $enabled, $nothing, "Hello, {$student['name']}!");
echo 'Total: ' . ($count * $price) . PHP_EOL;
var_dump([0 => 'first'] + [0 => 'second', 1 => 'third']);
var_dump(array_merge(['first'], ['second', 'third']));

// Functions, conditions, loops and recursion (11–16 September).
function square(int $number): int
{
    return $number * $number;
}

function factorial(int $number): int
{
    if ($number < 0) {
        throw new \InvalidArgumentException('Use a non-negative integer.');
    }
    return $number <= 1 ? 1 : $number * factorial($number - 1);
}

foreach ($student['subjects'] as $index => $subject) {
    echo "$index: $subject" . PHP_EOL;
}
for ($i = 0; $i < 3; $i++) {
    echo square($i) . PHP_EOL;
}
$remaining = 2;
while ($remaining > 0) {
    $remaining--;
}
do {
    echo "A do-while loop runs at least once." . PHP_EOL;
} while (false);
echo factorial(5) . PHP_EOL;

// Interface, trait, visibility, inheritance, static and magic methods (16–23 September).
interface HasVolume
{
    public function volume(): int;
}

trait DescribesVolume
{
    public function description(): string
    {
        return 'Volume: ' . $this->volume();
    }
}

class Box implements HasVolume
{
    use DescribesVolume;

    public static int $created = 0;

    public function __construct(public int $width, private int $height, protected int $length)
    {
        self::$created++;
    }

    public function volume(): int
    {
        return $this->width * $this->height * $this->length;
    }

    public function __toString(): string
    {
        return $this->description();
    }
}

class MetalBox extends Box
{
    public function material(): string
    {
        return 'metal';
    }
}

function printVolume(HasVolume $object): void
{
    echo $object->volume() . PHP_EOL;
}

$box = new MetalBox(2, 3, 4);
$reference = $box;       // Another reference to the same object.
$copy = clone $box;     // An independent object.
$copy->width = 10;
printVolume($box);
echo $copy . PHP_EOL;
var_dump($reference === $box, $copy === $box, Box::$created);
