<?php
/**************************************************************/
/*	@copyright	OCTemplates 2015-2019.						  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ControllerOCTemplatesModuleOCTProductsModules extends Controller {
    public function index($data) {
    
        $data['page_product_name'] = '';
        if(isset($this->request->get['product_id']) and !empty($this->request->get['product_id'])){
            $this->load->model('catalog/product');
            $page_product_name = $this->model_catalog_product->getProductName($this->request->get['product_id']);
            if($page_product_name){
                $data['page_product_name'] = ' для '.$page_product_name;
            }
        }
        
        $data['position'] = '';
        $data['module_name'] = 'related-products';
        $data['module'] = 0;
        $data['heading_title'] = $this->language->get('text_related');
        
        return $this->load->view('octemplates/module/oct_products_modules', $data);
    }
    
    //Функция логирования для Opencart
    public function ocLog($filename, $data, $append=false)
    {
        if(!$append){
            file_put_contents(DIR_LOGS . $filename . '.txt', var_export($data,true));
        }else{
            file_put_contents(DIR_LOGS . $filename . '.txt', var_export($data,true).PHP_EOL, FILE_APPEND);
        }
        
    }
}
