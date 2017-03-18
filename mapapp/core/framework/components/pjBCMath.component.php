<?php
class pjBCMath
{
	public function add($a, $b)
	{
		return bcadd($a, $b);
	}
	
	public function mul($a, $b)
	{
		return bcmul($a, $b);
	}
	
	public function pow($base, $exp)
	{
		return bcpow($base, $exp);
	}
	
	public function powmod($base, $exp, $mod)
	{
		return bcpowmod($base, $exp, $mod);
	}
	
	public function div($a, $b)
	{
		return bcdiv($a, $b);
	}
	
	public function mod($n, $d)
	{
		return bcmod($n, $d);
	}
	
	public function cmp($a, $b)
	{
		return bccomp($a, $b);
	}
}
?>