<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjXML
{
	private $version = "1.0";
	
	private $eol = "\n";
	
	private $encoding = "UTF-8";
	
	private $data = NULL;
	
	private $name = NULL;
	
	private $record = 'item';
	
	private $root = 'items';
	
	private $fields = array();
	
	private $mimeType = "text/xml";
	
	public function __construct()
	{
		$this->name = time() . ".xml";
	}
	
	public function download()
	{
		pjToolkit::download($this->data, $this->name, $this->mimeType);
	}
	
	public function process($data=array())
	{
		$rows = array();
		$rows[] = '<?xml version="'.$this->version.'" encoding="'.$this->encoding.'"?>';
		$rows[] = '<' . $this->root . '>';
		foreach ($data as $item)
		{
			$cells = array();
			$cells[] = "\t<" . $this->record . ">";
			foreach ($item as $key => $value)
			{
				$cells[] = "\t\t<" . $key . ">" . pjSanitize::html($value) . "</" . $key . ">";
			}
			$cells[] = "\t</" . $this->record . ">";
			
			$rows[] = join($this->eol, $cells);
		}
		$rows[] = "</" . $this->root . ">";
		$this->setData(join($this->eol, $rows));
		
		return $this;
	}
	
	public function write()
	{
		file_put_contents($this->name, $this->data);
		return $this;
	}
	
	public function load($file)
	{
		$pjUpload = new pjUpload();
		$pjUpload->setAllowedExt(array('xml'));

		$data = array();
		if ($pjUpload->load($file))
		{
			$filename = $pjUpload->getFile('tmp_name');
			if (function_exists('simplexml_load_file'))
			{
				$xml = simplexml_load_file($filename);
				
				$xml = (array) $xml;
				$xml = array_values($xml);
				foreach ($xml[0] as $item)
				{
					$item = (array) $item;
					foreach ($item as $k => $v)
					{
						$item[$k] = strval($v);
					}
					$data[] = $item;
				}
				
				$this->setData($data);
				return true;
			}
		}
		return false;
	}
	
	public function import($modelName)
	{
		if (is_array($this->data) && !empty($this->data))
		{
			$modelName .= 'Model';
			$model = new $modelName;
			if (is_object($model))
			{
				$model->begin();
				foreach ($this->data as $data)
				{
					if (count($this->fields) > 0)
					{
						foreach ($data as $k => $v)
						{
							if (!array_key_exists($k, $this->fields))
							{
								unset($data[$k]);
							}
						}
					}
					$model->reset()->setAttributes($data)->insert();
				}
				$model->commit();
			}
		}
		
		return $this;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function setData($value)
	{
		$this->data = $value;
		return $this;
	}
	
	public function setVersion($value)
	{
		$this->version = $value;
		return $this;
	}
	
	public function setEol($value)
	{
		$this->eol = $value;
		return $this;
	}
	
	public function setEncoding($value)
	{
		$this->encoding = $value;
		return $this;
	}
	
	public function setName($value)
	{
		$this->name = $value;
		return $this;
	}
	
	public function setRoot($value)
	{
		$this->root = $value;
		return $this;
	}
	
	public function setRecord($value)
	{
		$this->record = $value;
		return $this;
	}
	
	public function setMimeType($value)
	{
		$this->mimeType = $value;
		return $this;
	}

	public function setFields($value)
	{
		if (is_array($value))
		{
			$this->fields = $value;
		}
		return $this;
	}
}
?>