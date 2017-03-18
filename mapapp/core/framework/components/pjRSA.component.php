<?php
class RSA
{
	private $n; //modulo
	
	private $e; //public
	
	private $d; //private

	private $math;
	
	public function __construct($n = 0, $e = 0, $d = 0)
	{
		$this->n = $n;
		$this->e = $e;
		$this->d = $d;

		if (extension_loaded('gmp'))
		{
			include_once dirname(__FILE__) . '/pjGMP.component.php';
			$this->math = new pjGMP();
			
		} elseif (extension_loaded('bcmath')) {
			
			include_once dirname(__FILE__) . '/pjBCMath.component.php';
			$this->math = new pjBCMath();
			
		} elseif (extension_loaded('big_int') || extension_loaded('php_big_int')) {
			
			include_once dirname(__FILE__) . '/pjBigInt.component.php';
			$this->math = new pjBigInt();
			
		} else {
			
			//include_once dirname(__FILE__) . '/pjMath.component.php';
			//$this->math = new pjMath();
			//FIXME
		}
		
		return true;
	}
	
	public function encrypt($m, $s = 3)
	{
        $coded = '';
        $max = strlen($m);
        $packets = ceil($max / $s);

        for ($i = 0; $i < $packets; $i++)
        {
            $packet = substr($m, $i * $s, $s);
            $code = '0';

			for ($j = 0; $j < $s; $j++)
			{
				if (isset($packet[$j]))
				{
					$code = $this->math->add($code, $this->math->mul(ord($packet[$j]), $this->math->pow('256', $j)));
				}
			}

			$code = $this->math->powmod($code, $this->e, $this->n);
			$coded .= $code.' ';
		}

		return trim($coded);
    }
        
	public function decrypt($c)
	{
		$coded = explode(' ', $c);
		$message = '';
		$max = count($coded);
		for ($i = 0; $i < $max; $i++)
		{
			$code = $this->math->mod($this->math->pow($coded[$i], $this->d), $this->n);
			while ($this->math->cmp($code, '0') != 0)
			{
				$ascii = $this->math->mod($code, '256');
				$code = $this->math->div($code, '256', 0);
				$message .= chr($ascii);
			}
		}

		return $message;
    }
}
?>