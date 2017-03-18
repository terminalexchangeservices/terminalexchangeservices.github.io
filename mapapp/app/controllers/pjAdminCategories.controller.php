<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
class pjAdminCategories extends pjAdmin
{
	public function pjActionCheckCategoryName()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_GET['category_title']))
		{
			$pjCategoryModel = pjCategoryModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjCategoryModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjCategoryModel->where('t1.category_title', $_GET['category_title'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['category_create']))
			{
				$pjCategoryModel = pjCategoryModel::factory();
				$id = $pjCategoryModel->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_FILES['marker']) && !empty($_FILES['marker']['tmp_name']))
					{
						$handle = new pjCUpload($_FILES['marker']);
						$hash = md5(uniqid(rand(), true));
						if ($handle->uploaded)
						{
							$handle->allowed = array('image/*');
							$handle->mime_check = true;
							$handle->file_new_name_body = $id . '_' . $hash;
							if($handle->image_src_x > 32)
							{
								$handle->jpeg_quality = 100;
								$handle->image_resize = true;
								$handle->image_x = 32;
								$handle->image_ratio_y = true;
							}
							$handle->process(PJ_UPLOAD_PATH . 'markers/');
							if ($handle->processed)
							{
								$data['marker'] = str_replace('\\', '/', $handle->file_dst_pathname);
								$data['marker'] = preg_replace('/\/+/', '/', $data['marker']);
								$data['marker'] = $data['marker'];
								
								$pjCategoryModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
							}
						}
					}
					
					$err = 'ACT03';
				} else {
					$err = 'ACT04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCategories&action=pjActionIndex&err=$err");
			} else {
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteCategory()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$response = array();
			if ($this->isAdmin() || $this->isEditor())
			{
				$pjCategoryModel = pjCategoryModel::factory();
				$arr = $pjCategoryModel->find($_GET['id'])->getData();
				if(!empty($arr['marker']))
				{
					$marker_path = $arr['marker'];
					if (file_exists(PJ_INSTALL_PATH . $marker_path)) {
						if(unlink(PJ_INSTALL_PATH . $marker_path)){
						}
					}
				}
				if ($pjCategoryModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
				{
					pjStoreCategoryModel::factory()->where('category_id', $_GET['id'])->eraseAll();
					
					$response['code'] = 200;
				} else {
					$response['code'] = 100;
				}
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteCategoryBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if ($this->isAdmin() || $this->isEditor())
			{
				if (isset($_POST['record']) && count($_POST['record']) > 0)
				{
					$pjCategoryModel = pjCategoryModel::factory();
					$arr = $pjCategoryModel->whereIn('id', $_POST['record'])->findAll()->getData();
					foreach($arr as $v)
					{
						if(!empty($v['marker']))
						{
							$marker_path = $v['marker'];
							if (file_exists(PJ_INSTALL_PATH . $marker_path)) {
								if(unlink(PJ_INSTALL_PATH . $marker_path)){
								}
							}
						}
					}
					
					$pjCategoryModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
					pjStoreCategoryModel::factory()->whereIn('category_id', $_POST['record'])->eraseAll();
				}
			}
		}
		exit;
	}
	
	public function pjActionExportCategory()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjCategoryModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Categories-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionExportStores()
	{
		$this->checkLogin();
		
		if (isset($_GET['category_id']))
		{
			$arr = pjStoreModel::factory()->where('id IN(SELECT TSC.store_id FROM `'.pjStoreCategoryModel::factory()->getTable().'` AS TSC WHERE TSC.category_id = '.$_GET['category_id'].')')->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Stores-of-category-". $_GET['category_id'] . '-' .time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	
	public function pjActionGetCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCategoryModel->where('t1.category_title LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCategoryModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'category_title';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCategoryModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjCategoryModel->select('t1.*, (SELECT COUNT(*) FROM `'.pjStoreCategoryModel::factory()->getTable().'` as t2 WHERE t2.category_id = t1.id) as cnt_stores')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCategories.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if($_POST['column'] == 'category_title')
			{
				if($_POST['value'] != '')
				{
					$pjCategoryModel = pjCategoryModel::factory();
					
					$check = $pjCategoryModel->where('t1.category_title', $_POST['value'])->findCount()->getData() == 0 ? true : false;
					if($check == true)
					{
						$pjCategoryModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
					}
				}
			}else{
				pjCategoryModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			}
		}
		exit;
	}
	
	public function pjActionStatusCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjCategoryModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')"
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			if (isset($_POST['category_update']))
			{
				$data = array();
				
				if (isset($_FILES['marker']) && !empty($_FILES['marker']['tmp_name']))
				{
					$arr = $pjCategoryModel->find($_POST['id'])->getData();
					if(!empty($arr['marker']))
					{
						$marker_path = $arr['marker'];
						if (file_exists(PJ_INSTALL_PATH . $marker_path)) {
							if(unlink(PJ_INSTALL_PATH . $marker_path)){
							}
						}
					}
					
					$handle = new pjCUpload($_FILES['marker']);
					$hash = md5(uniqid(rand(), true));
					if ($handle->uploaded)
					{
						$handle->allowed = array('image/*');
						$handle->mime_check = true;
						$handle->file_new_name_body = $_POST['id'] . '_' . $hash;
						$handle->image_convert = 'jpg';
						if($handle->image_src_x > 32)
						{
							$handle->jpeg_quality = 100;
							$handle->image_resize = true;
							$handle->image_x = 32;
							$handle->image_ratio_y = true;
						}
						$handle->process(PJ_UPLOAD_PATH . 'markers/');
						if ($handle->processed)
						{
							$data['marker'] = str_replace('\\', '/', $handle->file_dst_pathname);
							$data['marker'] = preg_replace('/\/+/', '/', $data['marker']);
							$data['marker'] = $data['marker'];
						}
					}
				}
				
				$pjCategoryModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT01");
				
			} else {
				$arr = $pjCategoryModel->select('t1.*, (SELECT COUNT(*) FROM `'.pjStoreCategoryModel::factory()->getTable().'` as t2 WHERE t2.category_id = t1.id) as cnt_stores')
					->find($_GET['id'])->getData();
					
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT08");
				}
				$this->set('arr', $arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteMarker()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjCategoryModel = pjCategoryModel::factory();
			$arr = $pjCategoryModel->find($_GET['id'])->getData(); 
			
			if(!empty($arr))
			{
				$marker_path = $arr['marker'];
				if (file_exists(PJ_INSTALL_PATH . $marker_path)) {
					if(unlink(PJ_INSTALL_PATH . $marker_path)){
					}
				}
				$data = array();
				$data['marker'] = ':NULL';
				$pjCategoryModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
				
				$response['code'] = 200;
				
			}else{
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
}
?>