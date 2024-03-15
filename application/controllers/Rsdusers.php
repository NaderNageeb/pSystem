<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rsdusers extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('Rsdusers_model','rsdusers');
	}

	public function index()
	{

		// $this->permission_check('rsdsale_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('rsd_password');
		$this->load->view('rsdpass',$data);
	}
	public function add()
	{
		$this->permission_check('customers_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('customers');
		$this->load->view('customers',$data);
	}
public function add2()
	{
		$this->permission_check('customers_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('rsd_password');
		$this->load->view('rsdpass',$data);
	}
	public function newcustomers(){
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'trim|required');
		
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->customers->verify_and_save();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	public function update($id){
		$this->belong_to('db_customers',$id);
		$this->permission_check('customers_edit');
		$data=$this->data;
		$result=$this->customers->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('customers');
		$this->load->view('customers', $data);
	}
	public function update_customers(){
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {			
			$result=$this->customers->update_customers();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}


	public function ajax_list()
	{
		$list = $this->rsdusers->get_datatables();
		
		$data = array();
		$no = 1;
   
		foreach ($list as $customers) {

			// $no++;
			$row = array();
			
			$row[] = $no++;
			$row[] = $customers->txtUserID;
			$row[] = $customers->txtName;
            $str2 = '<div class="btn-group" title="View Account">
            <a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
                Action <span class="caret"></span>
            </a>
            <ul role="menu" class="dropdown-menu dropdown-light pull-right">';
                $str2.='<li>
                    <a style="cursor:pointer" title="Delete Record ?" onclick="delete_rsduser('.$customers->id.')">
                        <i class="fa fa-fw fa-trash text-red"></i>Delete
                    </a>
                </li>
                
            </ul>
        </div>';			
$row[] = $str2;

			$data[] = $row;
           
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->rsdusers->count_all(),
						"recordsFiltered" => $this->rsdusers->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
        
	}


    
	public function update_status(){
		$this->permission_check_with_msg('customers_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		$result=$this->customers->update_status($id,$status);
		return $result;
	}
	
	public function delete_customers(){
		// $this->permission_check_with_msg('customers_delete');
		$id=$this->input->post('q_id');
		return $this->rsdusers->delete_rsd_from_table($id);
	}



	public function delete_rsduser(){
		$id=$this->input->post('q_id');
		return $this->rsdusers->delete_rsd_from_table($id);
	}

    

	// public function multi_delete(){
	// 	$this->permission_check_with_msg('customers_delete');
	// 	$ids=implode (",",$_POST['checkbox']);
	// 	return $this->customers->delete_customers_from_table($ids);
	// }
	public function show_pay_now_modal(){
		$this->permission_check_with_msg('sales_payment_add');
		$customer_id=$this->input->post('customer_id');
		echo $this->customers->show_pay_now_modal($customer_id);
	}
	public function save_payment(){
		$this->permission_check_with_msg('sales_payment_add');
		echo $this->customers->save_payment();
	}
	public function show_pay_return_due_modal(){
		$this->permission_check_with_msg('sales_return_payment_add');
		$customer_id=$this->input->post('customer_id');
		echo $this->customers->show_pay_return_due_modal($customer_id);
	}
	public function save_return_due_payment(){
		$this->permission_check_with_msg('sales_payment_add');
		echo $this->customers->save_return_due_payment();
	}
	public function delete_opening_balance_entry(){
		$this->permission_check_with_msg('sales_payment_delete');
		$entry_id = $this->input->post('entry_id');
		echo $this->customers->delete_opening_balance_entry($entry_id);
	}
	/*27-06-2020*/
	public function view_payment_list_modal(){
		$this->permission_check_with_msg('sales_payment_add');
		$customer_id=$this->input->post('customer_id');
		echo $this->customers->view_payment_list_modal($customer_id);
	}
	/*28-06-2020*/
	//Print Customer Bulk Payment Receipt
	public function print_show_receipt($payment_id){
		if(!$this->permissions('sales_add') && !$this->permissions('sales_edit')){
			$this->show_access_denied_page();
		}
		$data=$this->data;
		$data['page_title']=$this->lang->line('payment_receipt');
		$data=array_merge($data,array('payment_id'=>$payment_id));
		$this->load->view('print-cust-payment-receipt',$data);
	}

	public function restore_customer_list(){
		echo get_customers_select_list($this->input->post('customer_id'),get_current_store_id());
	}

	public function getCustomers($id=''){
		echo $this->customers->getCustomersJson($id);
	}
}
