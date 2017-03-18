<?php
define("MATH_BIGINTEGER_MODE", 1);
include_once dirname(__FILE__) . '/Math_BigInteger.php';

class pjMath
{
	public function add($a, $b)
	{
		$a = new Math_BigInteger($a);
		$b = new Math_BigInteger($b);
		$c = $a->add($b);
		
		return $c->toString();
	}
	
	public function mul($a, $b)
	{
		$a = new Math_BigInteger($a);
		$b = new Math_BigInteger($b);
		$c = $a->multiply($b);
		
		return $c->toString();
	}
	
	public function pow($base, $exp)
	{
		//FIXME
		return gmp_strval(gmp_pow($base, $exp));
	}
	
	public function powmod($base, $exp, $mod)
	{
		//FIXME
		$base = new Math_BigInteger($base);
		$exp = new Math_BigInteger($exp);
		$mod = new Math_BigInteger($mod);
		$mod = $base->modPow($exp, $mod);
		
		return $mod->toString();
	}
	
	public function div($a, $b)
	{
		$a = new Math_BigInteger($a);
		$b = new Math_BigInteger($b);
		list($quotient, ) = $a->divide($b);

		return $quotient->toString();
	}
	
	public function mod($n, $d)
	{
		$n = new Math_BigInteger($n);
		$d = new Math_BigInteger($d);
		
		return $n->_mod2($d);
	}
	
	public function cmp($a, $b)
	{
		$a = new Math_BigInteger($a);
		$b = new Math_BigInteger($b);
		$c = $a->compare($b);
		
		return $c;
	}
}
?>