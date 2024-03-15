<?php

class Home extends MY_Controller {


	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('category_model','category');
	}

    public function index()
	{
		
		// $sql = "select MAX(`count_id` + 1) as count_id from db_purchase ";
		// $query=$this->db->simple_query($sql);
		//  $values = mysqli_fetch_assoc($query);
		//  $num_rows = $values['count_id'];
		//  echo $num_rows;
	 $qi = $this->db->select('id')->where('store_id',get_current_store_id())->where('warehouse_type','System')->get('db_warehouse')->row()->id;
  

	 print_r($qi);

	}


}

    ?>