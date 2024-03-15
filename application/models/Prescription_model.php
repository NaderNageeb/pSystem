<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription_model extends CI_Model {


    public function get_items_info($rowcount,$item_id){
		$q1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get();
		$tax=$this->db->query("select tax from db_tax where id=".$q1->row()->tax_id)->row()->tax;

		$info['item_id'] = $q1->row()->id;
		$info['item_name'] = $q1->row()->item_name;
		$info['item_available_qty'] = $q1->row()->stock;
		$info['item_sales_qty'] = 1;

		$this->return_row_with_data($rowcount,$info);
	}


    public function return_row_with_data($rowcount,$info){
		extract($info);

		?>
            <tr id="row_<?=$rowcount;?>" data-row='<?=$rowcount;?>'>
               <td id="td_<?=$rowcount;?>_1">
                  <!-- item name  -->
                  <input type="text" style="font-weight: bold;" id="td_data_<?=$rowcount;?>_1" class="form-control no-padding" value='<?=$item_name;?>' readonly >
               </td>
               <!-- Qty -->
               <td id="td_<?=$rowcount;?>_3">
                  <div class="input-group ">
                     <span class="input-group-btn">
                     <!-- <button onclick="decrement_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-minus text-danger"></i></button></span> -->
                     <input typ="text"  class="form-control no-padding text-center" onkeyup="calculate_tax(<?=$rowcount;?>)" id="td_data_<?=$rowcount;?>_3" name="td_data_<?=$rowcount;?>_3">
                     <span class="input-group-btn">
                     <!-- <button onclick="increment_qty(<?=$rowcount;?>)" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus text-success"></i></button></span> -->
                  </div>
               </td>
               
               <!-- Remove button -->
               <td id="td_<?=$rowcount;?>_16" style="text-align: center;">
                  <a class=" fa fa-fw fa-minus-square text-red" style="cursor: pointer;font-size: 34px;" onclick="removerow(<?=$rowcount;?>)" title="Delete ?" name="td_data_<?=$rowcount;?>_16" id="td_data_<?=$rowcount;?>_16"></a>
               </td>
              <input type="hidden" id="tr_available_qty_<?=$rowcount;?>_13" value="<?=$item_available_qty;?>">
               <input type="hidden" id="tr_item_id_<?=$rowcount;?>" name="tr_item_id_<?=$rowcount;?>" value="<?=$item_id;?>">
            </tr>
		<?php

	}


    public function xss_html_filter($input){
		return $this->security->xss_clean(html_escape($input));
	}


    public function preview_labels(){
		//print_r($_POST);exit();
		$CI =& get_instance();
		//Filtering XSS and html escape from user inputs 
		$store_name=$this->db->query("select store_name from db_store where id=".get_current_store_id())->row()->store_name;
		$rowcount = $this->input->post('hidden_rowcount');
		$warehouse_id = $this->input->post('warehouse_id');
		$warehouse_name =  $this->db->query("select warehouse_name from db_warehouse where id=".$warehouse_id)->row()->warehouse_name;
		


		$is_roll_paper=true;
		$page_break = (isset($is_roll_paper) && !empty($is_roll_paper)) ? 'page-break-after: always;' : '';

		?>
		<div style=" height:5in !important;  width:8.5in !important; line-height: 16px !important; ">
			<div class="inner-div-2" style=" height:11in !important;  width:8.5in !important; line-height: 16px !important;">
				<div style="">

					<?php
					//Import post data from form
					for($i=1;$i<=1;$i++){
					
						if(isset($_POST['tr_item_id_'.$i]) && !empty($_POST['tr_item_id_'.$i])){
							

							$item_id 			=$this->xss_html_filter(trim($_POST['tr_item_id_'.$i]));
							$item_count 			=$this->xss_html_filter(trim($_POST['td_data_'.$i."_3"]));
							$res1=$this->db->query("select * from db_items where id=$item_id")->row();

							$item_name =$res1->item_name;
							$item_code = (!empty($res1->custom_barcode)) ? $res1->custom_barcode : $res1->item_code;
							$item_price =$res1->sales_price;

							// for($j=1;$j<=$item_count;$j++){
							?>
							<div style="height:2in !important; line-height: 2in; width:3.0in !important; display: inline-block; <?=$page_break;?>  " class="label_border text-center">
							<div style="display:inline-block;vertical-align:middle;line-height:16px !important;text-align:center;">
								<!-- <b style="display: block !important" class="text-uppercase"><?//=$store_name;?></b> -->
								<b style="display: block !important" class="text-uppercase"><?=$warehouse_name;?></b>
							

								<!-- $warehouse_name -->

									<span style="display: block !important">
									<?= $item_name;?>
									<br>
									
									</span>
								<!-- <b>Price:</b> -->
								<!-- <span><?//= $CI->currency($item_price);?></span> -->
								<span> الدكتور : <?php print ($this->session->userdata('inv_username')); ?></span>
								<!-- <img class="center-block" style="max-height: 0.35in !important; width: 100%; opacity: 1.0" src="<?php //echo base_url();?>barcode/index/<?php //echo urldecode($item_code);?>"> -->
								<br>
                              <hr style="border: 1px solid black;"></hr>
								<?php echo  $item_count;  ?>

							</div>
							</div>
							<br>
							<?php
							// }
						}
					
					 }//for end
					?>
					
					
				</div>
			</div>
		</div>
		<?php
		
	}




	//GET Labels from Purchase Invoice
	public function get_purchase_items_info($rowcount,$item_id,$purchase_qty){
		$q1=$this->db->select('*')->from('db_items')->where("id=$item_id")->get();
		$tax=$this->db->query("select tax from db_tax where id=".$q1->row()->tax_id)->row()->tax;

		$info['item_id'] = $q1->row()->id;
		$info['item_name'] = $q1->row()->item_name;
		$info['item_available_qty'] = $q1->row()->stock;
		$info['item_sales_qty'] = $purchase_qty;

	    return $this->return_row_with_data($rowcount,$info);
	}

































}

?>