<?php
class pjBigInt
{
	public function add($a, $b)
	{
		return bi_add($a, $b);
	}
	
	public function mul($a, $b)
	{
		return bi_mul($a, $b);
	}
	
	public function pow($base, $exp)
	{
		return bi_pow($base, $exp);
	}
	
	public function powmod($base, $exp, $mod)
	{
		return bi_powmod($base, $exp, $mod);
	}
	
	public function div($a, $b)
	{
		return bi_div($a, $b);
	}
	
	public function mod($n, $d)
	{
		return bi_mod($n, $d);
	}
	
	public function cmp($a, $b)
	{
		return bi_cmp($a, $b);
	}
}
?>