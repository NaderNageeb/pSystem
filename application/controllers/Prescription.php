<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('prescription_model','prescription');
	}
	
	public function index()
	{
	
		$data=$this->data;
		$data['page_title']=$this->lang->line('prescription');
		$this->load->view('prescription',$data);
	}


		/*Labels Print request*/
		public function return_row_with_data($rowcount,$item_id){
			echo $this->prescription->get_items_info($rowcount,$item_id);
		}

		public function preview_labels(){
			echo $this->prescription->preview_labels();
		}



		public function get_json_items_details(){
			$store_id=$this->input->get('store_id');
			$warehouse_id=$this->input->get('warehouse_id');
			$search_for=$this->input->get('search_for');
	// returnsales :  for sales return
			$returnsales = $this->input->get('returnsales');
	// returnsales :  for sales return
	
			$customer_id=$this->input->get('customer_id');
			$count_id=$this->input->get('count_id');
	
			$show_purchase_price = $this->permissions('show_purchase_price');
			$data = array();
			$display_json = array();
				$name = strtolower(trim($_GET['name']));
	
				if(isset($search_for) && $search_for=='purchase'){
					//$this->db->where('a.service_bit=1');
					$this->db->select("a.service_bit,a.purchase_price,a.id,a.expire_date,a.item_name,a.item_code,COALESCE(SUM(a.stock),0) as stock,item_group");
					$this->db->from("db_items as a");
	
				}
				else if(isset($search_for) && ($search_for=='labels' || $search_for=='sales')){
					$this->db->select("*");
					$this->db->from("db_items as a");
				}
				else{
					$this->db->where('a.service_bit=0');
					$this->db->select("a.service_bit,a.purchase_price,a.id,a.expire_date,a.item_name,a.item_code,COALESCE(b.available_qty,0) as stock,item_group");
					$this->db->from("db_items as a");
					$this->db->join("db_warehouseitems as b","b.item_id=a.id",'left');
					$this->db->where("b.warehouse_id=$warehouse_id");
				}
				
				$this->db->where("a.status",1);
				$this->db->where("a.store_id",$store_id);
				//  $this->db->where("a.custom_barcode LIKE '%$name%' or a.item_name LIKE '%$name%'");
				 $this->db->where("(LOWER(a.custom_barcode) LIKE '%$name%' or LOWER(a.custom_qrcode) LIKE '%$name%' or LOWER(a.item_name) LIKE '%$name%' or LOWER(a.item_code) LIKE '%$name%')");
	
				$this->db->group_by("a.id");
				$this->db->limit("20");
				//echo $this->db->get_compiled_select();exit();
				$sql =$this->db->get();
				
				foreach ($sql->result() as $res) {
					if($res->item_group!='Variants'){
					  $json_arr["id"] = $res->id;
					  $json_arr["value"] = $res->item_name;
					  $json_arr["label"] = $res->item_name;
					  $json_arr["item_code"] = $res->item_code;
					  $json_arr["stock"] = (isset($search_for) && $search_for=='sales') ? total_available_qty_items_of_warehouse($warehouse_id,$store_id,$res->id) : $res->stock;
					  $json_arr["purchase_price"] = ($show_purchase_price) ? store_number_format($res->purchase_price) : '';
					  $json_arr["service_bit"] = $res->service_bit;
					  array_push($display_json, $json_arr);
					}
				}
	
			echo json_encode($display_json);exit;
		}





		public function show_labels($purchase_id=''){
			$i=1;
			$result='';
			$q2=$this->db->query("select item_id,purchase_qty from db_purchaseitems where purchase_id='$purchase_id'");
			if($q2->num_rows()>0){
				
				foreach ($q2 -> result() as $res2) {
					$result.= $this->items->get_purchase_items_info($i++,$res2->item_id,$res2->purchase_qty);
				}
			}
			echo $result;
		}
	

}











?>