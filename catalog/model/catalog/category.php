<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}
	
	//Получим посадочные страницы родительской категории
    public function getCategoryPageGroupLinks($category_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
        
        return $query->row['page_group_links'];
    }
	//Получим посадочные страницы родительской категории КОНЕЦ

	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}
    
    public function getCategoryReplacement($category_id) {
	    
        $query = $this->db->query("SELECT `replacement` FROM " . DB_PREFIX . "category_replacement_rules WHERE category_id = '" . (int)$category_id . "'");
    
        if($query->num_rows>0 and isset($query->row['replacement'])){
            return $query->row['replacement'];
        }else{
            return '';
        }
        
    }
    
    public function getCategoryAddToStart($category_id) {
        
        $query = $this->db->query("SELECT `add_to_start` FROM " . DB_PREFIX . "category_replacement_rules WHERE category_id = '" . (int)$category_id . "'");
        
        if($query->num_rows>0 and isset($query->row['add_to_start'])){
            return $query->row['add_to_start'];
        }else{
            return '';
        }
        
    }

	public function getCategoryFilters($category_id) {
		$implode = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}
    
    public function getCategoryPath($category_id) {
     
	    //$query = $this->db->query("SELECT `path_id` FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "' AND `path_id` <> '".(int)$category_id."' ORDER BY `level` ASC");
	    $query = $this->db->query("SELECT `path_id` FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = '" . (int)$category_id . "' AND `path_id` <> '".(int)$category_id."' AND `path_id` IN(SELECT `category_id` FROM `" . DB_PREFIX . "category_to_store` WHERE `store_id` ='".$this->config->get('config_store_id')."') ORDER BY `level` ASC");
	    
	    $data = array();
	    
	    if ($query->num_rows) {
            foreach ($query->rows as $result) {
                $data[] = $result['path_id'];
            }
            return $data;
        }else {
            return false;
        }
	    
    }
    
    public function getCategoryPathAll($category_id) {
        
        $query = $this->db->query("SELECT `path_id` FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "' AND `path_id` IN(SELECT `category_id` FROM `" . DB_PREFIX . "category_to_store` WHERE `store_id` ='".$this->config->get('config_store_id')."') ORDER BY `level` ASC");
        
        $data = array();
        
        if ($query->num_rows) {
            foreach ($query->rows as $result) {
                $data[] = $result['path_id'];
            }
            return $data;
        }else {
            return false;
        }
        
    }
    
    //Сформируем хлебные крошки из категории максимального уровня вложенности
    public function getProductCategoryPath($product_id) {
        //$query = $this->db->query("SELECT `category_id` FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND `main_category`='1'");
        $query = $this->db->query("SELECT `path_id` FROM `" . DB_PREFIX . "category_path` WHERE `category_id` IN(SELECT `category_id` FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product_id . "') AND `level`=(SELECT MAX(`level`) FROM `" . DB_PREFIX . "category_path` WHERE `category_id` IN(SELECT `category_id` FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product_id . "')) LIMIT 1");
        if ($query->num_rows) {
            return $this->getCategoryPathAll($query->row['path_id']);
        }else{
            return false;
        }
    }
    //Сформируем хлебные крошки из категории максимального уровня вложенности КОНЕЦ
    
    //Сформируем категории "теги"
    public function getTagCategories($parent_id = 0) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' AND c.is_tag='1' ORDER BY c.sort_order, LCASE(cd.name)");
    
        if ($query->num_rows) {
            return $query->rows;
        }else{
            return false;
        }
        
    }
    //Сформируем категории "теги" КОНЕЦ
}