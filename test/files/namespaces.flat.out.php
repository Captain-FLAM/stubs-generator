<?php
class A extends \B\B implements \D
{
    const A = \B\B::A * 5;
    const B = GLOBAL_FALLBACK;
    const C = \GLOBAL_EXPLICIT;

    public function a($a = \B\B::A) : \B\B
    {
    }
}
class A extends \C implements \D\D
{
    const A = \B\B;
    const B = \GLOBAL_FALLBACK;
    const C = \GLOBAL_EXPLICIT;
}
function a()
{
}
function a()
{
}

$a = 'a';
