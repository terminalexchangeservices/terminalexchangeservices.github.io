<?php
class pjGMP
{
	public function add($a, $b)
	{
		return gmp_strval(gmp_add($a, $b));
	}
	
	public function mul($a, $b)
	{
		return gmp_strval(gmp_mul($a, $b));
	}
	
	public function pow($base, $exp)
	{
		return gmp_strval(gmp_pow($base, $exp));
	}
	
	public function powmod($base, $exp, $mod)
	{
		return gmp_strval(gmp_powm($base, $exp, $mod));
	}
	
	public function div($a, $b)
	{
		return gmp_strval(gmp_div_q($a, $b, GMP_ROUND_ZERO));
	}
	
	public function mod($n, $d)
	{
		return gmp_strval(gmp_mod($n, $d));
	}
	
	public function cmp($a, $b)
	{
		return gmp_strval(gmp_cmp($a, $b));
	}
}
?>